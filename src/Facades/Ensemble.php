<?php

namespace BuildWithLaravel\Ensemble\Facades;

use BuildWithLaravel\Ensemble\Models\Run;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Facade;

/**
 * @method static Run run(string $agentClass, ?Model $runnable = null, array $initialState = [])
 * @method static Run resume(Run $run, array $inputData)
 * @extends  \BuildWithLaravel\Ensemble\Ensemble
 */
class Ensemble extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'ensemble';
    }
}
