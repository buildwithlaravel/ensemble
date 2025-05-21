<?php

// app/Ensemble/Steps/SaveAnalysisResultsStep.php

namespace Workbench\App\Ensemble\Steps;

use BuildWithLaravel\Ensemble\Core\Agent;
use BuildWithLaravel\Ensemble\Core\State;
use BuildWithLaravel\Ensemble\Core\Step;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workbench\App\Ensemble\States\ContentAnalysisState; // Import custom state
use Workbench\App\Ensemble\Tools\SaveAnalysisResultsTool; // Import the tool

class SaveAnalysisResultsStep extends Step implements ShouldQueue
{
    public function handle(Agent $agent, State $state): State
    {
        // Ensure state is our custom type
        if (! $state instanceof ContentAnalysisState) {
            return $state->halt('Invalid state object passed to SaveAnalysisResultsStep.');
        }

        $summary = $state->summary;
        $topics = $state->topics;
        $runnableId = $agent->getRun()->runnable_id; // Get the ID of the user/runnable

        if (empty($summary) || empty($topics) || empty($runnableId)) {
            $agent->getRun()->createLog('Missing data (summary, topics, or runnable) to save results.');

            // Optionally return an error state
            // return $state->halt('Missing data to save results.');
            return $state;
        }

        $agent->getRun()->createLog('Saving analysis results...');

        // Resolve and call the tool directly from the step if needed
        // This assumes the tool's handle method accepts needed data as arguments
        $tool = app(SaveAnalysisResultsTool::class); // Resolve tool via container

        // Pass the data to the tool's handle method.
        // The tool also receives the agent instance if it needs context.
        $toolResult = $tool->handle([
            'summary' => $summary,
            'topics' => $topics,
            // Note: Tool parameters are typically for LLMs to call tools.
            // If a step calls a tool directly, the parameters() definition isn't used here.
            // The handle method's signature is what matters for direct calls.
            // However, our Tool base defines handle(array $arguments, Agent $agent),
            // so we package data into the $arguments array.
        ], $agent); // Pass the agent instance

        if ($toolResult === null) {
            $agent->getRun()->createLog('SaveAnalysisResultsTool failed.');
            // Optionally return an error state
            // return $state->halt('Failed to save analysis results.');
        } else {
            $agent->getRun()->createLog('Analysis results saved.');
        }

        // The step completes here. The workflow will mark the run as 'completed'
        // if this is the last step and no interrupts occurred.
        return $state;
    }
}
