<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('containers', function (Blueprint $table) {
            $table->id();
            $table->string('docker_id', 64)->unique();
            $table->string('name');
            $table->string('image')->nullable();
            $table->string('status')->nullable();
            $table->string('health')->nullable();
            $table->string('compose_project')->nullable()->index();
            $table->string('compose_service')->nullable();
            $table->json('ports')->nullable();
            $table->json('networks')->nullable();
            $table->json('volumes')->nullable();
            $table->json('labels')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('last_seen_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('containers');
    }
};
