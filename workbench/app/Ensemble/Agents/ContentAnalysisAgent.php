<?php

// app/Ensemble/Agents/ContentAnalysisAgent.php

namespace Workbench\App\Ensemble\Agents;

use BuildWithLaravel\Ensemble\Core\Agent;
use BuildWithLaravel\Ensemble\Core\State;
use BuildWithLaravel\Ensemble\Support\Parallel;
use Workbench\App\Ensemble\States\ContentAnalysisState;
use Workbench\App\Ensemble\Steps\AggregateContentStep;
use Workbench\App\Ensemble\Steps\ExtractTopicsStep;
use Workbench\App\Ensemble\Steps\FetchUrlContentStep;
use Workbench\App\Ensemble\Steps\GetUrlsStep;
use Workbench\App\Ensemble\Steps\SaveAnalysisResultsStep;
use Workbench\App\Ensemble\Steps\SummarizeContentStep;
use Workbench\App\Ensemble\Tools\SaveAnalysisResultsTool;

// Import the custom state

class ContentAnalysisAgent extends Agent
{
    /**
     * Define the custom State DTO used by this agent.
     */
    public function stateClass(): string
    {
        return ContentAnalysisState::class;
    }

    /**
     * Define the sequence of steps for this agent's workflow.
     * Steps can be class names or instances.
     */
    public function steps(): array
    {
        // The State object passed to dynamic step closures
        // is the current state in the workflow.
        return [
            // Validate and prepare URLs
            GetUrlsStep::class,

            // Fetch content from URLs in parallel
            // This closure dynamically creates FetchUrlContentStep instances for each URL and wraps them in the Parallel helper. The Workflow will dispatch these as jobs and wait.
            function (State $state) {
                // Ensure state is our custom type for type hinting
                if (!$state instanceof ContentAnalysisState) {
                    throw new \RuntimeException('Invalid state object received.');
                }

                return Parallel::make(
                    collect($state->urls)
                        ->map(fn (string $url) => new FetchUrlContentStep($url)) // Pass URL to step constructor
                        ->all()
                );
            },

            // Combine content from all fetched URLs
            AggregateContentStep::class,

            // Extract key topics using an LLM
            ExtractTopicsStep::class,

            // Summarize the combined content using an LLM
            SummarizeContentStep::class,

            // Save the results using a Tool
            SaveAnalysisResultsStep::class,
        ];
    }

    /**
     * Define the tools available to this agent (primarily for LLM use).
     */
    public function tools(): array
    {
        // Return tool class names or instances.
        // Tool instances can have dependencies injected.
        return [
            SaveAnalysisResultsTool::class,
            // Potentially other tools like SendEmailTool, etc.
        ];
    }

    /**
     * Optional description for the agent (useful for Manager Agents).
     */
    public function description(): string
    {
        return 'Analyzes content from a list of URLs, summarizes it, and extracts key topics.';
    }

    /**
     * Optional system prompt base for LLM interactions within this agent.
     */
    public function systemPrompt(): ?string
    {
        return 'You are an expert content analysis assistant.';
    }
}
