<?php

use BuildWithLaravel\Ensemble\Facades\Ensemble;
use BuildWithLaravel\Ensemble\Models\Run;
use Illuminate\Support\Facades\Route;
use Prism\Prism\Enums\Provider;
use Workbench\App\Ensemble\Agents\ContentAnalysisAgent;
use Workbench\App\Models\User;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/run/{run}', function ($run) {
    return Run::find($run);
});

Route::get('/run/{run}/resume', function ($runId) {
    $run = Run::find($runId);

    return Ensemble::resume($run, [
        'urls' => [
            'https://laravel.com/docs/container',
            'https://laravel.com/docs/providers',
        ],
    ]);
});

Route::get('/test', function () {

    set_time_limit(60 * 10);

    config([
        'ensemble.llm' => [
            'provider' => Provider::Ollama,
            'model' => 'gemma3:4b'
        ]
    ]);
    $user = User::find(1);
    Run::find('test-workflow')?->delete();

    $run = Ensemble::run(
        agentClass: ContentAnalysisAgent::class,
        runnable: $user,
    );

//        dispatch(function () use ($user) {
//            Ensemble::run(
//                agentClass: ContentAnalysisAgent::class,
//                runnable: $user,
//                initialState: [
//                    'urls' => [
//                        'https://livewire.laravel.com/docs/properties',
//                        'https://docs.ag-ui.com/llms-full.txt'
//                    ],
//                ]
//            );
//        });

    $run->load('runLogs');
    return $run->fresh()->toArray();

});
