<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ensemble_interrupts', function (Blueprint $table) {
            $table->char('id', 36)->primary();
            $table->char('run_id', 36);
            $table->string('type', 50);
            $table->json('payload')->nullable();
            $table->timestamp('interrupted_at');
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
            $table->foreign('run_id', 'ensemble_interrupts_run_id_foreign')
                ->references('id')->on('ensemble_runs')->onDelete('cascade');
            $table->index('run_id', 'ensemble_interrupts_run_id_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ensemble_interrupts');
    }
};
