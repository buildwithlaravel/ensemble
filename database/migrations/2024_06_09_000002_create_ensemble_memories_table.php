<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ensemble_memories', function (Blueprint $table) {
            $table->char('id', 36)->primary();
            $table->char('memoryable_id', 36);
            $table->string('memoryable_type');
            $table->string('key');
            $table->json('value')->nullable();
            $table->timestamps();
            $table->unique(['memoryable_id', 'memoryable_type', 'key'], 'ensemble_memories_memoryable_unique');
            $table->index(['memoryable_id', 'memoryable_type'], 'ensemble_memories_memoryable_id_memoryable_type_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ensemble_memories');
    }
};
