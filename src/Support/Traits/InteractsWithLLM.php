<?php

namespace BuildWithLaravel\Ensemble\Support\Traits;

use BuildWithLaravel\Ensemble\Core\Tool;
use BuildWithLaravel\Ensemble\Enums\LlmMode;
use BuildWithLaravel\Ensemble\Enums\ParameterType;
use BuildWithLaravel\Ensemble\Support\Parameter;
use Illuminate\Support\Traits\Conditionable;
use Prism\Prism\Contracts\Schema;
use Prism\Prism\Enums\Provider;
use Prism\Prism\Prism;
use Prism\Prism\Structured\PendingRequest as PendingStructuredRequest;
use Prism\Prism\Text\PendingRequest as PendingTextRequest;

/**
 * @property ?Provider $provider
 * @property ?string $model
 */
trait InteractsWithLLM
{
    use Conditionable;

    protected function resolvePrismProvider(): ?Provider
    {
        if (isset($this->provider) && $this->provider !== null) {
            return $this->provider;
        }
        $config = config('ensemble.llm.provider');

        return $config instanceof Provider ? $config : (Provider::tryFrom($config) ?: null);
    }

    protected function resolvePrismModel(): ?string
    {
        if (isset($this->model) && $this->model !== null) {
            return $this->model;
        }

        return config('ensemble.llm.model');
    }

    protected function getPrismClient($mode = LlmMode::TEXT): PendingTextRequest|PendingStructuredRequest
    {
        /** @var PendingTextRequest|PendingStructuredRequest $prism */
        $prism = match ($mode) {
            LlmMode::JSON => Prism::structured(),
            LlmMode::TEXT => Prism::text(),
            LlmMode::EMBED => Prism::embeddings(),
        };

        $prism->using($this->resolvePrismProvider(), $this->resolvePrismModel())
            ->withClientOptions($this->clientOptions());

        $this->when($this->schema(), fn () => $prism->withSchema($this->schema()));

        // If a list of tools are defined add them to prism
        if (method_exists($this, 'tools')) {
            $tools = $this->tools();
            $formatted = $this->formatToolsForPrism($tools);
            if (!empty($formatted)) {
                $prism = $prism->withTools($formatted);
            }
        }

        return $prism;
    }

    /**
     * Format tools for Prism/OpenAI function calling schema.
     */
    protected function formatToolsForPrism(array $tools): array
    {
        $result = [];
        foreach ($tools as $tool) {
            if (is_string($tool)) {
                $tool = app($tool);
            }
            if (!$tool instanceof Tool) {
                continue;
            }
            $params = [];
            foreach ($tool->parameters() as $param) {
                if (!$param instanceof Parameter) {
                    continue;
                }
                $params[$param->name] = [
                    'type' => $this->mapParameterType($param->type),
                    'description' => $param->description,
                    'required' => $param->required,
                ];
            }
            $result[] = [
                'name' => (new \ReflectionClass($tool))->getShortName(),
                'description' => $tool->description(),
                'parameters' => $params,
            ];
        }

        return $result;
    }

    /**
     * Map ParameterType enum to Prism/OpenAI type string.
     */
    protected function mapParameterType(ParameterType $type): string
    {
        return match ($type) {
            ParameterType::String => 'string',
            ParameterType::Integer => 'integer',
            ParameterType::Boolean => 'boolean',
            ParameterType::Array => 'array',
            ParameterType::Object => 'object',
            ParameterType::Null => 'null',
        };
    }

    protected function schema(): ?Schema
    {
        return null;
    }

    protected function clientOptions()
    {
        return isset($this->clientOptions) ? $this->clientOptions : (config('ensemble.llm.client') ?: []);
    }
}
