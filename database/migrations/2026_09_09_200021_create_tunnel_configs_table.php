<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tunnel_configs', function (Blueprint $table) {
            $table->id();
            $table->boolean('enabled')->default(false);
            $table->text('token')->nullable(); // encrypted
            $table->string('hostname')->nullable();
            $table->string('container_id', 64)->nullable();
            $table->boolean('connected')->default(false);
            $table->json('last_status')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tunnel_configs');
    }
};
