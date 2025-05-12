<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ensemble_runs', function (Blueprint $table) {
            $table->char('id', 36)->primary();
            $table->char('runnable_id', 36)->nullable();
            $table->string('runnable_type')->nullable();
            $table->string('agent_class');
            $table->json('state')->nullable();
            $table->string('status', 50)->default('running');
            $table->integer('current_step_index')->default(0);
            $table->timestamp('last_ran_at')->nullable();
            $table->timestamps();
            $table->index(['runnable_id', 'runnable_type'], 'ensemble_runs_runnable_id_runnable_type_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ensemble_runs');
    }
}; 