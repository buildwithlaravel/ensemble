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
    'defaults' => [
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
            'provider' => env('LLM_PROVIDER', 'openai'),

            /*
            |--------------------------------------------------------------------------
            | LLM Model
            |--------------------------------------------------------------------------
            |
            | The default model to use with the configured provider.
            |
            */
            'model' => env('LLM_MODEL', 'gpt-4'),
        ],
    ],
];
