<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    private array $permissions = [
        'docker.view',
        'docker.manage',
        'projects.view',
        'projects.manage',
        'notes.view',
        'notes.manage',
        'monitoring.view',
        'chat.use',
        'audit.view',
        'mcp.manage',
        'tunnel.manage',
        'providers.manage',
        'settings.manage',
        'users.manage',
    ];

    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        foreach ($this->permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        $admin = Role::firstOrCreate(['name' => 'admin']);
        $admin->syncPermissions($this->permissions);

        $operator = Role::firstOrCreate(['name' => 'operator']);
        $operator->syncPermissions([
            'docker.view', 'docker.manage',
            'projects.view', 'projects.manage',
            'notes.view', 'notes.manage',
            'monitoring.view', 'chat.use',
        ]);

        $viewer = Role::firstOrCreate(['name' => 'viewer']);
        $viewer->syncPermissions([
            'docker.view', 'projects.view', 'notes.view', 'monitoring.view', 'chat.use',
        ]);
    }
}
