<?php

namespace BuildWithLaravel\Ensemble\Database\Factories;

use BuildWithLaravel\Ensemble\Models\Run;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Workbench\App\Models\User;

class RunFactory extends Factory
{
    protected $model = Run::class;

    public function definition(): array
    {
        $user = User::factory()->create();

        return [
            'id' => (string) Str::uuid(),
            'runnable_id' => $user->id,
            'runnable_type' => $user->getMorphClass(),
            'agent' => 'TestAgent',
            'state' => [],
            'status' => 'running',
            'current_step_index' => 0,
            'last_ran_at' => now(),
        ];
    }
}
