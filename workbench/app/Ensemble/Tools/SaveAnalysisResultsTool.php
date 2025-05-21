<?php

// app/Ensemble/Tools/SaveAnalysisResultsTool.php

namespace Workbench\App\Ensemble\Tools;

use App\Models\ContentAnalysis;
use App\Models\User;
use BuildWithLaravel\Ensemble\Core\Agent;
use BuildWithLaravel\Ensemble\Core\Tool;
use BuildWithLaravel\Ensemble\Enums\ParameterType; // Assuming analysis is saved related to a User
use BuildWithLaravel\Ensemble\Support\Parameter; // Assuming a model to save results

class SaveAnalysisResultsTool extends Tool
{
    // This description is primarily for LLMs if they were deciding to call this tool.
    // When called directly from a step, it serves as internal documentation.
    public function description(): string
    {
        return 'Saves the analyzed content summary and topics for a user.';
    }

    // These parameters define the expected input if an LLM calls this tool.
    public function parameters(): array
    {
        return [
            Parameter::make('summary', ParameterType::String)->description('The concise summary of the content.')->required(),
            Parameter::make('topics', ParameterType::Array)->description('An array of key topics extracted from the content.')->required(),
        ];
    }

    /**
     * Handle the tool execution.
     *
     * @param  array<string, mixed>  $arguments  Arguments provided to the tool (e.g., from LLM or Step).
     * @param  Agent  $agent  The agent instance calling the tool.
     * @return string|null A string result to return (e.g., success message) or null on failure.
     */
    public function handle(array $arguments, Agent $agent): ?string
    {
        $summary = $arguments['summary'] ?? null;
        $topics = $arguments['topics'] ?? null;
        $runnable = $agent->getRun()->runnable; // Access the associated runnable (User, Team, etc.)

        // Basic validation
        if (empty($summary) || empty($topics) || ! ($runnable instanceof User)) {
            $agent->getRun()->createLog('SaveAnalysisResultsTool: Missing or invalid arguments/runnable.');

            return null; // Indicate failure
        }

        try {
            // Find or create a ContentAnalysis record associated with the user
            $analysis = ContentAnalysis::create([
                'user_id' => $runnable->id,
                'summary' => $summary,
                'topics' => json_encode($topics), // Assuming JSON column
                'original_run_id' => $agent->getRun()->id, // Link back to the run
            ]);

            $agent->getRun()->createLog('SaveAnalysisResultsTool: Results saved successfully.');

            return 'Analysis results saved with ID: '.$analysis->id;

        } catch (\Throwable $e) {
            $agent->getRun()->createLog('SaveAnalysisResultsTool: Database save failed: '.$e->getMessage(), ['exception' => $e->getTraceAsString()]);

            return null; // Indicate failure
        }
    }
}
