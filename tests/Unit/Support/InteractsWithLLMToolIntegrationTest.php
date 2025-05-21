<?php

use BuildWithLaravel\Ensemble\Core\Tool;
use BuildWithLaravel\Ensemble\Enums\ParameterType;
use BuildWithLaravel\Ensemble\Support\Parameter;
use BuildWithLaravel\Ensemble\Support\Traits\InteractsWithLLM;
use Prism\Prism\Prism;

it('formatToolsForPrism formats tools for Prism/OpenAI', function () {
    // PREPARE: Create a trait user with InteractsWithLLM and a Tool with parameters
    // ACT: Call formatToolsForPrism with the tool
    // ASSERT: Result is an array with correct tool name, description, and parameter types/required flags
});

it('Agent with tools integrates with Prism withTools', function () {
    // PREPARE: Fake a Prism response, create a Tool and an Agent with tools
    // ACT: Call configurePrism on the agent and use withTools
    // ASSERT: The result is the expected fake Prism response text
});
