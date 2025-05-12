<?php

namespace BuildWithLaravel\Ensemble;

use Illuminate\Support\Facades\Facade;

/**
 * @see \BuildWithLaravel\Ensemble\Skeleton\SkeletonClass
 */
class EnsembleFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'ensemble';
    }
}
