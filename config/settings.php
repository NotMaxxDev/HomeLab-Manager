<?php

/*
|--------------------------------------------------------------------------
| App-Einstellungen (Defaults)
|--------------------------------------------------------------------------
|
| Diese Datei definiert die Standardwerte aller über die Weboberfläche
| konfigurierbaren Einstellungen. Zur Laufzeit werden die Werte aus der
| Datenbank (settings-Tabelle) geladen und gecacht. Ein Fehlen in der DB
| bedeutet: Default aus dieser Datei verwenden.
|
| Struktur pro Key:
|   'key' => [
|       'default'   => mixed,
|       'type'      => 'string'|'int'|'bool'|'json'|'encrypted',
|       'group'     => 'general'|'docker'|'monitoring'|'security'|'llm'|'notifications'|'external_access',
|       'label'     => 'settings.label.key', // Translatable-Schlüssel
|       'encrypted' => bool, // Wert wird in der DB mit Crypt verschlüsselt
|   ]
|
*/

return [

    'theme' => [
        'default' => 'dark',
        'type' => 'string',
        'group' => 'general',
        'label' => 'settings.theme',
    ],

    'session_timeout' => [
        'default' => 120,
        'type' => 'int',
        'group' => 'security',
        'label' => 'settings.session_timeout',
    ],

    'two_factor_enforced' => [
        'default' => false,
        'type' => 'bool',
        'group' => 'security',
        'label' => 'settings.two_factor_enforced',
    ],

    'docker_read_only' => [
        'default' => false,
        'type' => 'bool',
        'group' => 'docker',
        'label' => 'settings.docker_read_only',
    ],

    'docker_exec_enabled' => [
        'default' => false,
        'type' => 'bool',
        'group' => 'docker',
        'label' => 'settings.docker_exec_enabled',
    ],

    'metrics_retention_days' => [
        'default' => 7,
        'type' => 'int',
        'group' => 'monitoring',
        'label' => 'settings.metrics_retention_days',
    ],

    'metrics_polling_interval' => [
        'default' => 15,
        'type' => 'int',
        'group' => 'monitoring',
        'label' => 'settings.metrics_polling_interval',
    ],

    'alert_cpu_threshold' => [
        'default' => 90,
        'type' => 'int',
        'group' => 'monitoring',
        'label' => 'settings.alert_cpu_threshold',
    ],

    'alert_memory_threshold' => [
        'default' => 90,
        'type' => 'int',
        'group' => 'monitoring',
        'label' => 'settings.alert_memory_threshold',
    ],

    'note_attachment_max_size' => [
        'default' => 10,
        'type' => 'int',
        'group' => 'general',
        'label' => 'settings.note_attachment_max_size',
    ],

    'agent_allow_read_without_confirmation' => [
        'default' => false,
        'type' => 'bool',
        'group' => 'llm',
        'label' => 'settings.agent_allow_read_without_confirmation',
    ],

    'agent_system_prompt' => [
        'default' => 'Du bist HomelabManager-Assistent, ein Agent zur Verwaltung eines Homelabs. Antworte präzise. Bevor du verändernde Aktionen ausführst, fordere eine Bestätigung an.',
        'type' => 'string',
        'group' => 'llm',
        'label' => 'settings.agent_system_prompt',
    ],

    'cloudflare_warning_shown' => [
        'default' => false,
        'type' => 'bool',
        'group' => 'external_access',
        'label' => 'settings.cloudflare_warning_shown',
    ],

];
