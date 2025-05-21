<?php

use BuildWithLaravel\Ensemble\Contracts\InterruptHandlerInterface;
use BuildWithLaravel\Ensemble\Core\State;
use BuildWithLaravel\Ensemble\Ensemble;
use BuildWithLaravel\Ensemble\Enums\InterruptType;
use BuildWithLaravel\Ensemble\Models\Run;
use BuildWithLaravel\Ensemble\Runtime\Handlers\CallToolHandler;
use BuildWithLaravel\Ensemble\Runtime\Handlers\DelegateHandler;
use BuildWithLaravel\Ensemble\Runtime\Handlers\DoneHandler;
use BuildWithLaravel\Ensemble\Runtime\Handlers\HaltHandler;
use BuildWithLaravel\Ensemble\Runtime\Handlers\RetryHandler;
use BuildWithLaravel\Ensemble\Runtime\Handlers\WaitForEventHandler;
use BuildWithLaravel\Ensemble\Runtime\Handlers\WaitForHumanHandler;
use BuildWithLaravel\Ensemble\Runtime\InterruptHandler;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\App;

uses(RefreshDatabase::class);

describe('InterruptHandler', function () {
    it('dispatches to correct handler', function () {
        // TODO: Test that InterruptHandler dispatches to the correct handler
    });
    it('throws if no handler registered', function () {
        // TODO: Test that InterruptHandler throws if no handler is registered
    });
    it('HaltHandler sets status and logs', function () {
        // TODO: Test that HaltHandler sets the status and logs correctly
    });
    it('RetryHandler sets status and logs', function () {
        // TODO: Test that RetryHandler sets the status and logs correctly
    });
    it('WaitForHumanHandler sets status and logs', function () {
        // TODO: Test that WaitForHumanHandler sets the status and logs correctly
    });
    it('WaitForEventHandler sets status and logs', function () {
        // TODO: Test that WaitForEventHandler sets the status and logs correctly
    });
    it('CallToolHandler sets status and logs', function () {
        // TODO: Test that CallToolHandler sets the status and logs correctly
    });
    it('DelegateHandler sets status and logs', function () {
        // TODO: Test that DelegateHandler sets the status and logs correctly
    });
    it('DoneHandler sets status and logs', function () {
        // TODO: Test that DoneHandler sets the status and logs correctly
    });
    it('Ensemble::run calls InterruptHandler if interrupted', function () {
        // TODO: Test that Ensemble::run calls InterruptHandler if the run is interrupted
    });
    it('RunResumer::resume calls InterruptHandler if interrupted', function () {
        // TODO: Test that RunResumer::resume calls InterruptHandler if the run is interrupted
    });
});
