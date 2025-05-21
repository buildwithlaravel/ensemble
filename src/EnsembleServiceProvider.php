<?php

namespace BuildWithLaravel\Ensemble;

use BuildWithLaravel\Ensemble\Console\MakeAgentCommand;
use BuildWithLaravel\Ensemble\Console\MakeEnsembleWorkflowCommand;
use BuildWithLaravel\Ensemble\Console\MakeStepCommand;
use BuildWithLaravel\Ensemble\Console\MakeToolCommand;
use BuildWithLaravel\Ensemble\Console\ResumeAgent;
use BuildWithLaravel\Ensemble\Console\RunAgent;
use BuildWithLaravel\Ensemble\Runtime\InterruptHandler;
use BuildWithLaravel\Ensemble\Runtime\RunResumer;
use Illuminate\Support\ServiceProvider;

class EnsembleServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        /*
         * Optional methods to load your package assets
         */
        // $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'ensemble');
        // $this->loadViewsFrom(__DIR__.'/../resources/views', 'ensemble');
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        // $this->loadRoutesFrom(__DIR__.'/routes.php');

        $this->publishes([
            __DIR__ . '/../config/ensemble.php' => config_path('ensemble.php'),
        ], 'ensemble-config');

        $this->publishes([
            __DIR__ . '/../database/migrations/' => database_path('migrations'),
        ], 'ensemble-migrations');

        // Publishing the views.
        /*$this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/vendor/ensemble'),
        ], 'views');*/

        // Publishing assets.
        /*$this->publishes([
            __DIR__.'/../resources/assets' => public_path('vendor/ensemble'),
        ], 'assets');*/

        // Publishing the translation files.
        /*$this->publishes([
            __DIR__.'/../resources/lang' => resource_path('lang/vendor/ensemble'),
        ], 'lang');*/

        // Registering package commands.
        $this->commands([
            MakeAgentCommand::class,
            MakeEnsembleWorkflowCommand::class,
            MakeStepCommand::class,
            MakeToolCommand::class,
            RunAgent::class,
            ResumeAgent::class,
        ]);

        // Register interrupt handlers from config
        $interruptHandler = $this->app->make(InterruptHandler::class);
        foreach (config('ensemble.interrupt_handlers', []) as $handlerClass) {
            $interruptHandler->register($this->app->make($handlerClass));
        }
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/ensemble.php', 'ensemble');

        $this->app->singleton(InterruptHandler::class, function ($app) {
            return new InterruptHandler;
        });
        $this->app->singleton(RunResumer::class, RunResumer::class);
        $this->app->bind(Ensemble::class, Ensemble::class);
        $this->app->bind('ensemble', function ($app) {
            return $app->make(Ensemble::class);
        });

        $this->commands([
            MakeEnsembleWorkflowCommand::class,
            MakeAgentCommand::class,
            MakeStepCommand::class,
            MakeToolCommand::class,
        ]);
    }
}
