<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mcp_call_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('token_id')->nullable()->constrained('mcp_tokens')->nullOnDelete();
            $table->string('tool');
            $table->json('arguments')->nullable();
            $table->json('result')->nullable();
            $table->string('status')->default('ok');
            $table->unsignedInteger('duration_ms')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mcp_call_logs');
    }
};
