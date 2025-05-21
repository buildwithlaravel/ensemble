<?php

namespace BuildWithLaravel\Ensemble\Support\Traits;

use Illuminate\Database\Eloquent\Model;

trait ResolvesEnsembleModels
{
    /**
     * Resolve a model class from config, falling back to default.
     *
     * @param  string|null  $mustExtend  (optional base class/interface to check)
     *
     * @throws \RuntimeException
     */
    protected static function resolveModel(string $key, ?string $default = null): string
    {
        $class = config("ensemble.models.{$key}", $default);
        if (! $class || ! class_exists($class)) {
            throw new \RuntimeException("Configured model for '{$key}' does not exist: {$class}");
        }

        return $class;
    }

    protected static function newModel (string $key, ?string $default = null): Model
    {
        $class = self::resolveModel($key, $default);
        return  new $class;
    }
}
