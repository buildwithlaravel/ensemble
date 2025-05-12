<?php

namespace BuildWithLaravel\Ensemble\Core;

/**
 * Abstract base class for workflow steps.
 * Defines the contract for atomic units of work within a workflow.
 */
abstract class Step
{
    /**
     * Execute the step logic.
     */
    abstract public function handle(Agent $agent, State $state): State;
}
