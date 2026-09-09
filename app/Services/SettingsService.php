<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;

/**
 * DB-basiertes, zur Laufzeit veränderbares Settings-System.
 *
 * Nur infrastrukturkritische Werte stehen in der .env. Alle übrigen
 * Einstellungen werden über die Weboberfläche konfiguriert und hier
 * gespeichert. Änderungen wirken ohne Container-Neustart (Cache-Invalidierung).
 */
class SettingsService
{
    private const CACHE_KEY = 'app.settings.all';

    private ?array $resolved = null;

    /** @return array<string, array> */
    public function definitions(): array
    {
        return config('settings', []);
    }

    public function definition(string $key): ?array
    {
        return $this->definitions()[$key] ?? null;
    }

    /** Alle Settings (Definitionen + gespeicherte Werte) aufgelöst. */
    public function all(): array
    {
        if ($this->resolved !== null) {
            return $this->resolved;
        }

        $this->resolved = Cache::rememberForever(self::CACHE_KEY, function () {
            $result = [];

            foreach ($this->definitions() as $key => $def) {
                $result[$key] = $this->readStored($key) ?? ($def['default'] ?? null);
            }

            return $result;
        });

        return $this->resolved;
    }

    public function get(string $key, mixed $default = null): mixed
    {
        $values = $this->all();

        return array_key_exists($key, $values) ? $values[$key] : $default;
    }

    public function bool(string $key, bool $default = false): bool
    {
        return (bool) $this->get($key, $default);
    }

    public function int(string $key, int $default = 0): int
    {
        return (int) $this->get($key, $default);
    }

    public function string(string $key, string $default = ''): string
    {
        return (string) $this->get($key, $default);
    }

    public function set(string $key, mixed $value): void
    {
        $def = $this->definition($key) ?? ['type' => 'string', 'group' => 'general'];

        $serialized = $this->serialize($value, $def['type'] ?? 'string');
        $encrypted = (bool) ($def['encrypted'] ?? false);

        Setting::updateOrCreate(
            ['key' => $key],
            [
                'value' => $encrypted ? Crypt::encryptString($serialized) : $serialized,
                'type' => $def['type'] ?? 'string',
                'group' => $def['group'] ?? 'general',
                'encrypted' => $encrypted,
                'label' => $def['label'] ?? $key,
            ]
        );

        $this->flush();
    }

    public function forget(string $key): void
    {
        Setting::where('key', $key)->delete();
        $this->flush();
    }

    public function flush(): void
    {
        $this->resolved = null;
        Cache::forget(self::CACHE_KEY);
    }

    private function readStored(string $key): mixed
    {
        $setting = Setting::where('key', $key)->first();
        if (! $setting) {
            return null;
        }

        $raw = $setting->encrypted
            ? Crypt::decryptString($setting->value)
            : $setting->value;

        return $this->deserialize($raw, $setting->type);
    }

    private function serialize(mixed $value, string $type): string
    {
        return match ($type) {
            'bool' => $value ? '1' : '0',
            'int' => (string) (int) $value,
            'json' => json_encode($value),
            default => (string) $value,
        };
    }

    private function deserialize(?string $raw, string $type): mixed
    {
        return match ($type) {
            'bool' => $raw === '1' || $raw === 'true',
            'int' => (int) $raw,
            'json' => json_decode($raw ?? 'null', true),
            default => $raw,
        };
    }
}
