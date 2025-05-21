<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default Settings
    |--------------------------------------------------------------------------
    |
    | These are the default settings that will be used throughout the framework
    | unless overridden by specific components.
    |
    */
    'llm' => [
        /*
        |--------------------------------------------------------------------------
        | LLM Provider
        |--------------------------------------------------------------------------
        |
        | The default LLM provider to use. This should be a value from the
        | Prism\Prism\Enums\Provider enum.
        |
        */
        'provider' => env('ENSEMBLE_LLM_PROVIDER', 'openai'),

        /*
        |--------------------------------------------------------------------------
        | LLM Model
        |--------------------------------------------------------------------------
        |
        | The default model to use with the configured provider.
        |
        */
        'model' => env('ENSEMBLE_LLM_MODEL', 'gpt-4'),

        'client' => [
            'timeout' => env('ENSEMBLE_LLM_TIMEOUT', 30),
        ]
    ],

    'interrupt_handlers' => [
        // Add your interrupt handler class names here
        // e.g. BuildWithLaravel\Ensemble\Runtime\Handlers\HaltHandler::class,
    ],

    'models' => [
        'run' => BuildWithLaravel\Ensemble\Models\Run::class,
        'memory' => BuildWithLaravel\Ensemble\Models\Memory::class,
        'run_log' => BuildWithLaravel\Ensemble\Models\RunLog::class,
        // Add other models as needed
    ],
];
