<?php

namespace Workbench\App\Ensemble\Steps;

use BuildWithLaravel\Ensemble\Core\Agent;
use BuildWithLaravel\Ensemble\Core\State;
use BuildWithLaravel\Ensemble\Core\Step;
use BuildWithLaravel\Ensemble\Enums\EventType;
use Workbench\App\Ensemble\States\ContentAnalysisState;

class GetUrlsStep extends Step
{
    public function handle(Agent $agent, State $state): State
    {
        if (!$state instanceof ContentAnalysisState) {
            return $state->halt('Invalid state object passed to GetUrlsStep.');
        }

        if (empty($state->urls) || !is_array($state->urls)) {
            // return $state->halt('No URLs provided for analysis.');
            return $state->waitForHuman('provide_urls', 'Please provide a list of URLs to analyze.');
        }

        // URLs are present and valid (basic check), return the state
        $agent->getRun()->event(EventType::StatusUpdate, ['message' => 'URLs received: ' . count($state->urls)]);

        return $state;
    }
}
