<?php

use BuildWithLaravel\Ensemble\Core\Agent;
use BuildWithLaravel\Ensemble\Models\Run;
use BuildWithLaravel\Ensemble\Support\MemoryStore;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Workbench\App\Models\User;

uses(RefreshDatabase::class);

describe('MemoryStore', function () {
    it('scoping: separate memory for different memory keys', function () {
        // TODO: Test that MemoryStore keeps separate memory for different memory keys
    });
    it('CRUD: put, get, forget, all, flush', function () {
        // TODO: Test CRUD operations on MemoryStore
    });
    it('Agent memory access and runnable requirement', function () {
        // TODO: Test that Agent memory access requires a runnable and works as expected
    });
});

test('MemoryStore scoping: separate memory for different memoryables', function () {
    // TODO: Test that MemoryStore keeps separate memory for different memoryables
});

test('MemoryStore CRUD: put, get, forget, all, flush', function () {
    // TODO: Test CRUD operations on MemoryStore
});

test('Agent memory access and runnable requirement', function () {
    // TODO: Test that Agent memory access requires a runnable and works as expected
});

test('Agent throws if run has no runnable', function () {
    // TODO: Test that Agent throws if run has no runnable
});

class TestMemoryAgent extends Agent
{
    public function stateClass(): string
    {
        return \BuildWithLaravel\Ensemble\Core\GenericState::class;
    }
}
