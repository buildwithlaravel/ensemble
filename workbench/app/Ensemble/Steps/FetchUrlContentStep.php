<?php


namespace Workbench\App\Ensemble\Steps;

use BuildWithLaravel\Ensemble\Core\Agent;
use BuildWithLaravel\Ensemble\Core\State;
use BuildWithLaravel\Ensemble\Core\Step;
use Illuminate\Support\Facades\Http;
use Workbench\App\Ensemble\States\ContentAnalysisState;


class FetchUrlContentStep extends Step
{
    public function __construct(protected string $url)
    {
    }

    public function handle(Agent $agent, State $state): State
    {
        if (!$state instanceof ContentAnalysisState) {
            return $state->halt('Invalid state object passed to FetchUrlContentStep.');
        }

        try {
            $agent->getRun()->createLog("Fetching content from: {$this->url}");
            $content = cache()->remember(md5($this->url), now()->addMinutes(10), function () {
                $response = Http::timeout(15)->get($this->url);
                if ($response->successful()) {
                    return $response->body();
                }
                throw new \Exception("Failed to fetch content from: {$this->url}");
            });

            $agent->getRun()->createLog("Successfully fetched content from: {$this->url}");

            $fetched = $state->fetched_content;
            $fetched[$this->url] = preg_replace('/(\s{2,})/', "\s", strip_tags($content)); // Using URL as key directly is simpler if URLs are unique
            $state->fetched_content = $fetched;

            // Return the updated state
            return $state;
        } catch (\Throwable $e) {
            $agent->getRun()->createLog("Exception fetching content from {$this->url}: " . $e->getMessage(), ['url' => $this->url, 'exception' => $e->getTraceAsString()]);

            // Return the state without adding content
            return $state; // Note: This might need more robust error handling
        }
    }

    protected function name(): string
    {
        return "FetchUrlContentStep: {$this->url}";
    }

}
