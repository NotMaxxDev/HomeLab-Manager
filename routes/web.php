<?php

use App\Http\Controllers\SetupController;
use App\Livewire\Agent\AgentService;
use App\Livewire\Audit\AuditIndex;
use App\Livewire\Chat\Chat;
use App\Livewire\Dashboard;
use App\Livewire\Docker\ContainerIndex;
use App\Livewire\Docker\ContainerShow;
use App\Livewire\Docker\ImagesIndex;
use App\Livewire\Docker\VolumesNetworksIndex;
use App\Livewire\Mcp\TokenIndex;
use App\Livewire\Monitoring\MonitoringIndex;
use App\Livewire\Notes\NoteEditor;
use App\Livewire\Notes\NoteIndex;
use App\Livewire\Notes\NoteShow;
use App\Livewire\Profile\Profile;
use App\Livewire\Projects\ProjectIndex;
use App\Livewire\Projects\ProjectShow;
use App\Livewire\Settings\LlmProviders;
use App\Livewire\Settings\NotificationChannels;
use App\Livewire\Settings\SettingsIndex;
use App\Livewire\Tunnel\TunnelSettings;
use App\Livewire\Users\UserIndex;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('dashboard'));

// Setup-Wizard (Erststart)
Route::get('/setup', [SetupController::class, 'show'])->name('setup.show');
Route::post('/setup', [SetupController::class, 'store'])->name('setup.store');

// Auth-geschützte Routes (via Fortify/Livewire)
Route::middleware(['auth', 'two_factor', 'session.timeout'])->group(function () {
    Route::get('/dashboard', Dashboard::class)->name('dashboard');

    // Docker
    Route::livewire('/containers', ContainerIndex::class)->name('containers.index');
    Route::livewire('/containers/{container}', ContainerShow::class)->name('containers.show');
    Route::livewire('/images', ImagesIndex::class)->name('images.index');
    Route::livewire('/volumes', VolumesNetworksIndex::class)->name('volumes.index');

    // Projekte
    Route::livewire('/projects', ProjectIndex::class)->name('projects.index');
    Route::livewire('/projects/{project}', ProjectShow::class)->name('projects.show');

    // Notizen
    Route::livewire('/notes', NoteIndex::class)->name('notes.index');
    Route::livewire('/notes/create', NoteEditor::class)->name('notes.create');
    Route::livewire('/notes/{note}/edit', NoteEditor::class)->name('notes.edit');
    Route::livewire('/notes/{note}', NoteShow::class)->name('notes.show');

    // Monitoring
    Route::livewire('/monitoring', MonitoringIndex::class)->name('monitoring.index');

    // Chat
    Route::livewire('/chat', Chat::class)->name('chat.index');
    Route::livewire('/chat/{conversation}', Chat::class)->name('chat.show');

    // Administration
    Route::livewire('/audit', AuditIndex::class)->name('audit.index');
    Route::livewire('/mcp', TokenIndex::class)->name('mcp.index');
    Route::livewire('/tunnel', TunnelSettings::class)->name('tunnel.index');
    Route::livewire('/users', UserIndex::class)->name('users.index');

    // Einstellungen
    Route::livewire('/settings', SettingsIndex::class)->name('settings.index');
    Route::livewire('/settings/llm', LlmProviders::class)->name('settings.llm');
    Route::livewire('/settings/notifications', NotificationChannels::class)->name('settings.notifications');

    // Profil
    Route::livewire('/profile', Profile::class)->name('profile.show');
});