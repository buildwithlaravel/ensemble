<?php

// app/Ensemble/Steps/ExtractTopicsStep.php

namespace Workbench\App\Ensemble\Steps;

use BuildWithLaravel\Ensemble\Core\Agent;
use BuildWithLaravel\Ensemble\Core\State;
use BuildWithLaravel\Ensemble\Core\Step;
use BuildWithLaravel\Ensemble\Enums\LlmMode;
use BuildWithLaravel\Ensemble\Support\Traits\InteractsWithLLM;
use Illuminate\Contracts\Queue\ShouldQueue;
use Prism\Prism\Enums\StructuredMode;
use Prism\Prism\Schema\ArraySchema;
use Prism\Prism\Schema\StringSchema;
use Prism\Prism\ValueObjects\Messages\UserMessage;
use Workbench\App\Ensemble\States\ContentAnalysisState;

class ExtractTopicsStep extends Step implements ShouldQueue
{
    use InteractsWithLLM;

    public function handle(Agent $agent, State $state): State
    {
        // Ensure state is our custom type
        if (!$state instanceof ContentAnalysisState) {
            return $state->halt('Invalid state object passed to ExtractTopicsStep.');
        }

        $content = $state->combined_content; // Or use $state->summary if topics should be from summary

        if (empty($content)) {
            $agent->getRun()->createLog('No content to extract topics from.');

            return $state; // No topics, return state as is
        }

        $agent->getRun()->createLog('Calling LLM for topic extraction...');

        $response = $this->getPrismClient(LlmMode::JSON)
            ->usingStructuredMode(StructuredMode::Auto)
            ->withClientOptions([
                'timeout' => 60,
            ])
            ->withMessages([
                new UserMessage(implode("\n", [
                    'Extract the key topics from the following content as a JSON array of strings.',
                    'Only return unique topics with content.'
                ])),
                new UserMessage("<content>$content</content>"),
            ])
            ->asStructured();

        ray($response);

        // Ask for JSON output
        $topics = $response->structured;


        if (!is_array($topics)) {
            $agent->getRun()->createLog('LLM returned invalid JSON for topics: ' . $topics);

            // Optionally return an error state
            // return $state->halt('LLM failed to extract topics.');
            return $state; // No topics, return state as is
        }

        $agent->getRun()->createLog('Topics extracted: ' . implode(', ', $topics));

        // Update the state with the topics
        $state->topics = $topics;

        return $state;
    }

    public function schema()
    {
        return new ArraySchema(
            name: 'topics',
            description: 'A list of topics ',
            items: new StringSchema(
                name: 'topic',
                description: 'A list of topics from content'
            )
        );
    }
}
