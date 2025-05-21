<?php

namespace Workbench\App\Ensemble\States;

use BuildWithLaravel\Ensemble\Core\State;

/**
 * @property ?array $urls
 * @property ?array $fetched_content
 * @property ?string $combined_content
 * @property ?string $summary
 * @property ?array $topics
 */
class ContentAnalysisState extends State
{
    protected $casts = [
        'urls' => 'array',
        'fetched_content' => 'array',
        'combined_content' => 'string',
        'summary' => 'string',
        'topics' => 'array',
    ];
}
