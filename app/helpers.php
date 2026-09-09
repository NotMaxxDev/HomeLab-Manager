<?php

use Illuminate\Support\Str;

if (! function_exists('formatUptime')) {
    function formatUptime(int|float $seconds): string
    {
        $seconds = (int) $seconds;
        $days = intdiv($seconds, 86400);
        $hours = intdiv($seconds % 86400, 3600);
        $minutes = intdiv($seconds % 3600, 60);

        if ($days > 0) {
            return "{$days}d {$hours}h";
        }

        if ($hours > 0) {
            return "{$hours}h {$minutes}m";
        }

        return "{$minutes}m";
    }
}

if (! function_exists('formatBytes')) {
    function formatBytes(int|float $bytes, int $precision = 1): string
    {
        if ($bytes <= 0) {
            return '0 B';
        }

        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $i = (int) floor(log($bytes, 1024));

        return round($bytes / (1024 ** $i), $precision).' '.$units[$i];
    }
}

if (! function_exists('slugify')) {
    function slugify(string $text): string
    {
        return Str::slug($text);
    }
}
