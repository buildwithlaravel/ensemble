<?php

// app/Ensemble/Steps/AggregateContentStep.php

namespace Workbench\App\Ensemble\Steps;

use BuildWithLaravel\Ensemble\Core\Agent;
use BuildWithLaravel\Ensemble\Core\State;
use BuildWithLaravel\Ensemble\Core\Step;
use BuildWithLaravel\Ensemble\Enums\EventType;
use Illuminate\Support\Str;
use Workbench\App\Ensemble\States\ContentAnalysisState;

// Import custom state

// Example using Laravel helper

class AggregateContentStep extends Step
{

    public function handle(Agent $agent, State $state): State
    {
        // Ensure state is our custom type
        if (!$state instanceof ContentAnalysisState) {
            return $state->error('Invalid state object passed to AggregateContentStep.');
        }

        // Ensure state is our custom type
        if (!$state->fetched_content) {
            return $state->halt('No content fetched');
        }

        // Combine all fetched content strings
        $combined = collect($state->fetched_content)->filter()->join("\n\n---\n\n");

        // Limit the combined content to avoid exceeding LLM context limits
        $combined = Str::limit($combined, 15000); // Adjust limit as needed

        $agent->getRun()->event(EventType::StatusUpdate, ['run' => $agent->getRun(), 'message' => 'Aggregated content. Total size: ' . strlen($combined)]);

        // Update the state with the combined content
        $state->combined_content = $combined;

        return $state;
    }
}
