<?php

namespace App\Services\Agent;

use App\Models\Container;
use App\Models\Note;
use App\Models\Project;
use App\Services\Contracts\DockerServiceInterface;
use App\Services\Monitoring\HostMetricsService;

/**
 * Registry der Agenten-Tools.
 *
 * Jedes Tool hat eine Definition (für das LLM) und einen Handler.
 * Tools sind entweder "read" (ohne Bestätigung ausführbar) oder "write"
 * (human-in-the-loop Bestätigung erforderlich).
 */
class ToolRegistry
{
    private array $tools = [];

    public function __construct(
        private DockerServiceInterface $docker,
        private HostMetricsService $hostMetrics,
    ) {
        $this->registerDefaults();
    }

    public function get(string $name): ?array
    {
        return $this->tools[$name] ?? null;
    }

    public function definitions(): array
    {
        return array_map(fn (array $t) => $t['definition'], array_values($this->tools));
    }

    public function names(): array
    {
        return array_keys($this->tools);
    }

    public function level(string $name): string
    {
        return $this->tools[$name]['level'] ?? 'read';
    }

    /** Tool ausführen (nach Berechtigungsprüfung durch den AgentService). */
    public function execute(string $name, array $args): mixed
    {
        $tool = $this->get($name);

        if (! $tool) {
            throw new \RuntimeException("Unbekanntes Tool {$name}");
        }

        return ($tool['handler'])($args);
    }

    private function registerDefaults(): void
    {
        $this->register([
            'name' => 'list_containers',
            'level' => 'read',
            'description' => 'Listet alle Docker-Container mit Name, Status und Health auf.',
            'parameters' => [],
            'handler' => function (array $args): array {
                $containers = $this->docker->listContainers(true);

                return array_map(fn (array $c) => [
                    'id' => $c['Id'] ?? null,
                    'name' => ltrim(implode(',', $c['Names'] ?? []), '/'),
                    'image' => $c['Image'] ?? null,
                    'status' => $c['Status'] ?? null,
                    'state' => $c['State'] ?? null,
                ], $containers);
            },
        ]);

        $this->register([
            'name' => 'get_container_status',
            'level' => 'read',
            'description' => 'Liefert den detaillierten Status eines Containers anhand seines Namens oder seiner ID.',
            'parameters' => [
                'container' => ['type' => 'string', 'description' => 'Name oder ID des Containers'],
            ],
            'handler' => function (array $args): array {
                $id = $this->resolveContainerId($args['container'] ?? '');
                $inspect = $this->docker->inspectContainer($id);

                return [
                    'name' => $inspect['Name'] ?? null,
                    'state' => $inspect['State']['Status'] ?? null,
                    'health' => $inspect['State']['Health']['Status'] ?? null,
                    'started_at' => $inspect['State']['StartedAt'] ?? null,
                ];
            },
        ]);

        $this->register([
            'name' => 'get_container_logs',
            'level' => 'read',
            'description' => 'Liest die letzten Logzeilen eines Containers.',
            'parameters' => [
                'container' => ['type' => 'string', 'description' => 'Name oder ID des Containers'],
                'tail' => ['type' => 'integer', 'description' => 'Anzahl Zeilen (Default 100)'],
            ],
            'handler' => function (array $args): array {
                $id = $this->resolveContainerId($args['container'] ?? '');
                $lines = $this->docker->logs($id, (int) ($args['tail'] ?? 100));

                return ['lines' => $lines];
            },
        ]);

        $this->register([
            'name' => 'get_container_stats',
            'level' => 'read',
            'description' => 'Liefert aktuelle Ressourcen-Metriken (CPU/RAM/Netz) eines Containers.',
            'parameters' => [
                'container' => ['type' => 'string', 'description' => 'Name oder ID des Containers'],
            ],
            'handler' => function (array $args): array {
                $id = $this->resolveContainerId($args['container'] ?? '');

                return $this->docker->containerStats($id);
            },
        ]);

        $this->register([
            'name' => 'list_projects',
            'level' => 'read',
            'description' => 'Listet alle Projekte und ihre Container auf.',
            'parameters' => [],
            'handler' => fn (array $args): array => Project::with('containers')->get()->map(fn (Project $p) => [
                'id' => $p->id,
                'name' => $p->name,
                'containers' => $p->containers->pluck('name')->all(),
            ])->all(),
        ]);

        $this->register([
            'name' => 'get_project',
            'level' => 'read',
            'description' => 'Liefert Details zu einem Projekt.',
            'parameters' => [
                'project' => ['type' => 'string', 'description' => 'Name oder ID des Projekts'],
            ],
            'handler' => function (array $args): array {
                $project = Project::where('id', $args['project'] ?? 0)
                    ->orWhere('slug', $args['project'] ?? '')
                    ->orWhere('name', $args['project'] ?? '')
                    ->with('containers')
                    ->first();

                return $project ? [
                    'name' => $project->name,
                    'description' => $project->description,
                    'containers' => $project->containers->pluck('name')->all(),
                ] : ['error' => 'Projekt nicht gefunden'];
            },
        ]);

        $this->register([
            'name' => 'search_notes',
            'level' => 'read',
            'description' => 'Durchsucht die Knowledge Base nach Notizen.',
            'parameters' => [
                'query' => ['type' => 'string', 'description' => 'Suchbegriff'],
            ],
            'handler' => function (array $args): array {
                $query = $args['query'] ?? '';

                return Note::query()
                    ->where('title', 'like', "%{$query}%")
                    ->orWhere('content', 'like', "%{$query}%")
                    ->limit(20)
                    ->get(['id', 'title', 'slug'])
                    ->toArray();
            },
        ]);

        $this->register([
            'name' => 'get_note',
            'level' => 'read',
            'description' => 'Liest eine Notiz anhand ihres Titels oder Slugs.',
            'parameters' => [
                'note' => ['type' => 'string', 'description' => 'Titel oder Slug der Notiz'],
            ],
            'handler' => function (array $args): array {
                $note = Note::where('slug', $args['note'] ?? '')
                    ->orWhere('title', $args['note'] ?? '')
                    ->first();

                return $note ? ['title' => $note->title, 'content' => $note->content] : ['error' => 'Notiz nicht gefunden'];
            },
        ]);

        $this->register([
            'name' => 'create_note',
            'level' => 'write',
            'description' => 'Erstellt eine neue Notiz in der Knowledge Base.',
            'parameters' => [
                'title' => ['type' => 'string', 'description' => 'Titel der Notiz'],
                'content' => ['type' => 'string', 'description' => 'Inhalt (Markdown)'],
            ],
            'handler' => function (array $args): array {
                $note = Note::create([
                    'title' => $args['title'] ?? 'Unbenannt',
                    'slug' => str($args['title'] ?? '')->slug().'-'.strtolower(str()->random(4)),
                    'content' => $args['content'] ?? '',
                ]);

                return ['id' => $note->id, 'title' => $note->title, 'slug' => $note->slug];
            },
        ]);

        $this->register([
            'name' => 'start_container',
            'level' => 'write',
            'description' => 'Startet einen Container.',
            'parameters' => [
                'container' => ['type' => 'string', 'description' => 'Name oder ID des Containers'],
            ],
            'handler' => function (array $args): array {
                $id = $this->resolveContainerId($args['container'] ?? '');
                $this->docker->startContainer($id);

                return ['status' => 'started', 'container' => $id];
            },
        ]);

        $this->register([
            'name' => 'stop_container',
            'level' => 'write',
            'description' => 'Stoppt einen Container.',
            'parameters' => [
                'container' => ['type' => 'string', 'description' => 'Name oder ID des Containers'],
            ],
            'handler' => function (array $args): array {
                $id = $this->resolveContainerId($args['container'] ?? '');
                $this->docker->stopContainer($id);

                return ['status' => 'stopped', 'container' => $id];
            },
        ]);

        $this->register([
            'name' => 'restart_container',
            'level' => 'write',
            'description' => 'Startet einen Container neu.',
            'parameters' => [
                'container' => ['type' => 'string', 'description' => 'Name oder ID des Containers'],
            ],
            'handler' => function (array $args): array {
                $id = $this->resolveContainerId($args['container'] ?? '');
                $this->docker->restartContainer($id);

                return ['status' => 'restarted', 'container' => $id];
            },
        ]);

        $this->register([
            'name' => 'get_host_metrics',
            'level' => 'read',
            'description' => 'Liefert Host-Metriken (CPU, RAM, Load, Uptime).',
            'parameters' => [],
            'handler' => fn (array $args): array => $this->hostMetrics->all(),
        ]);
    }

    private function register(array $tool): void
    {
        $this->tools[$tool['name']] = [
            'name' => $tool['name'],
            'level' => $tool['level'],
            'handler' => $tool['handler'],
            'definition' => [
                'type' => 'function',
                'function' => [
                    'name' => $tool['name'],
                    'description' => $tool['description'],
                    'parameters' => [
                        'type' => 'object',
                        'properties' => collect($tool['parameters'])->mapWithKeys(
                            fn (array $p, string $k) => [$k => ['type' => $p['type'], 'description' => $p['description']]]
                        )->all(),
                        'required' => array_keys($tool['parameters']),
                    ],
                ],
            ],
        ];
    }

    private function resolveContainerId(string $nameOrId): string
    {
        if (Container::where('docker_id', $nameOrId)->exists()) {
            return $nameOrId;
        }

        $container = Container::where('name', $nameOrId)
            ->orWhere('name', '/'.$nameOrId)
            ->first();

        if ($container) {
            return $container->docker_id;
        }

        // Als letzten Ausweg direkt an die Engine durchreichen (Name)
        return $nameOrId;
    }
}
