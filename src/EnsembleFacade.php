<?php

namespace Buildwithlaravel\Ensemble;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Buildwithlaravel\Ensemble\Skeleton\SkeletonClass
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
