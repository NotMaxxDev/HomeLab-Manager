<?php

namespace App\Services\Monitoring;

use Illuminate\Support\Facades\File;

/**
 * Host-Metriken über Auslesen des gemounteten Host-/proc-Verzeichnisses.
 *
 * Der Pfad wird über HOST_PROC_PATH konfiguriert (Standard /host/proc).
 * Läuft die App nicht im Container (lokal), wird auf das lokale /proc
 * zurückgegriffen, sofern vorhanden.
 */
class HostMetricsService
{
    private string $procPath;

    private array $lastCpu = [];

    public function __construct(?string $procPath = null)
    {
        $this->procPath = rtrim($procPath ?? (string) config('docker.host_proc_path', '/host/proc'), '/');

        if (! File::isReadable($this->procPath.'/stat')) {
            $this->procPath = '/proc';
        }
    }

    public function cpu(): array
    {
        $stat = $this->read('stat');

        $times = array_values(array_map('intval', array_slice(explode(' ', trim($stat)), 1, 10)));
        $idle = ($times[3] ?? 0) + ($times[4] ?? 0);
        $total = array_sum($times);
        $busy = $total - $idle;

        $prevIdle = $this->lastCpu['idle'] ?? 0;
        $prevTotal = $this->lastCpu['total'] ?? 0;

        $this->lastCpu = ['idle' => $idle, 'total' => $total];

        $dTotal = $total - $prevTotal;
        $dIdle = $idle - $prevIdle;

        $percent = $dTotal > 0 ? max(0, min(100, 100 * ($dTotal - $dIdle) / $dTotal)) : 0;

        return ['percent' => round($percent, 2)];
    }

    public function memory(): array
    {
        $meminfo = $this->parseKeyValue($this->read('meminfo'));

        $total = (int) ($meminfo['MemTotal'] ?? 0);
        $available = (int) ($meminfo['MemAvailable'] ?? ($meminfo['MemFree'] ?? 0));

        $used = $total - $available;
        $percent = $total > 0 ? round(100 * $used / $total, 2) : 0;

        return [
            'total' => $total * 1024,
            'used' => $used * 1024,
            'available' => $available * 1024,
            'percent' => $percent,
        ];
    }

    public function load(): array
    {
        $parts = preg_split('/\s+/', trim($this->read('loadavg')));

        return [
            'load1' => (float) ($parts[0] ?? 0),
            'load5' => (float) ($parts[1] ?? 0),
            'load15' => (float) ($parts[2] ?? 0),
        ];
    }

    public function uptime(): array
    {
        $parts = preg_split('/\s+/', trim($this->read('uptime')));

        return ['seconds' => (float) ($parts[0] ?? 0)];
    }

    public function all(): array
    {
        return [
            'cpu' => $this->cpu(),
            'memory' => $this->memory(),
            'load' => $this->load(),
            'uptime' => $this->uptime(),
        ];
    }

    private function read(string $file): string
    {
        $path = $this->procPath.'/'.$file;

        return File::isReadable($path) ? File::get($path) : '';
    }

    private function parseKeyValue(string $content): array
    {
        $result = [];
        foreach (explode("\n", $content) as $line) {
            if (preg_match('/^(\w+):\s*(.*)$/', trim($line), $m)) {
                $result[$m[1]] = (int) $m[2];
            }
        }

        return $result;
    }
}
