<?php

namespace App\Services\Contracts;

/**
 * Abstraktion über die Docker Engine API.
 *
 * Alle Docker-Interaktionen laufen über dieses Interface, damit die
 * Implementierung austauschbar und testbar bleibt (z. B. Socket-Proxy,
 * Mock in Tests).
 */
interface DockerServiceInterface
{
    /** Ping der Docker Engine. Gibt false zurück, wenn nicht erreichbar. */
    public function ping(): bool;

    /** Alle Container auflisten (all = auch gestoppte). */
    public function listContainers(bool $all = true): array;

    /** Einzelnen Container inspizieren. */
    public function inspectContainer(string $id): array;

    /** Ressourcen-Nutzung eines Containers (ein Messpunkt). */
    public function containerStats(string $id): array;

    /** Logs eines Containers (nicht-folgend). */
    public function logs(string $id, int $tail = 200, ?string $since = null): array;

    public function startContainer(string $id): array;

    public function stopContainer(string $id): array;

    public function restartContainer(string $id): array;

    public function pauseContainer(string $id): array;

    public function unpauseContainer(string $id): array;

    public function removeContainer(string $id, bool $force = false): array;

    public function recreateContainer(string $id): array;

    public function listImages(bool $all = false): array;

    public function imageRemove(string $id, bool $force = false): array;

    public function imagesPrune(): array;

    public function listVolumes(): array;

    public function removeVolume(string $name): array;

    public function listNetworks(): array;

    public function removeNetwork(string $id): array;

    /** Host-/Engine-Informationen (CPU, RAM, Docker-Version). */
    public function systemInfo(): array;
}
