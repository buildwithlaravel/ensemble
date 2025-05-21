<?php

// app/Ensemble/Steps/SummarizeContentStep.php

namespace Workbench\App\Ensemble\Steps;

use BuildWithLaravel\Ensemble\Core\Agent;
use BuildWithLaravel\Ensemble\Core\State;
use BuildWithLaravel\Ensemble\Core\Step;
use BuildWithLaravel\Ensemble\Support\Traits\InteractsWithLLM;
use Illuminate\Contracts\Queue\ShouldQueue;
use Prism\Prism\Enums\FinishReason;
use Prism\Prism\ValueObjects\Messages\UserMessage;
use Workbench\App\Ensemble\States\ContentAnalysisState;

class SummarizeContentStep extends Step implements ShouldQueue
{
    use InteractsWithLLM;

    public function handle(Agent $agent, State $state): State
    {
        // Ensure state is our custom type
        if (!$state instanceof ContentAnalysisState) {
            return $state->halt('Invalid state object passed to SummarizeContentStep.');
        }


        if (empty($state->combined_content)) {
            $agent->getRun()->createLog('No content to summarize.');

            return $state; // No summary, return state as is
        }

        $agent->getRun()->createLog('Calling LLM for summarization...');

        // Use the withPrism method provided by the trait
        $topics = implode(PHP_EOL, $state->topics);
        $response = $this->getPrismClient()
            ->withMessages([
                new UserMessage("Summarize the following content concisely using the following content.
                Respond with the summary alone, no introduction.
                <topics>{$topics}</topics>                
                <content>{$state->combined_content}</content>"),
            ])
            ->asText();

        if ($response->finishReason !== FinishReason::Stop) {
            $agent->getRun()->createLog('LLM finished with reason:' . $response->finishReason->name);
            return $state->halt('Finish reason: ' . $response->finishReason->name);
        }
        $summary = $response->text;
        if (empty($summary)) {
            $agent->getRun()->createLog('LLM returned empty summary.');

            // return an error state
            return $state->halt('LLM failed to generate summary.');
        }

        $agent->getRun()->createLog('Summary generated.');

        // Update the state with the summary
        $state->summary = $summary;

        ray(['summary' => $summary]);

        return $state;
    }
}
