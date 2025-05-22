<?php

namespace BuildWithLaravel\Ensemble\Support;

use BuildWithLaravel\Ensemble\Enums\LlmMode;
use Prism\Prism\Embeddings\PendingRequest as PendingEmbeddingRequest;
use Prism\Prism\Embeddings\Response as PendingEmbeddingResponse;
use Prism\Prism\Prism;
use Prism\Prism\Structured\PendingRequest as PendingStructuredRequest;
use Prism\Prism\Structured\Response as PendingStructuredResponse;
use Prism\Prism\Text\PendingRequest as PendingTextRequest;
use Prism\Prism\Text\Response as PendingTextResponse;

/**
 * @see  Prism
 * @mixin PendingEmbeddingRequest
 * @mixin PendingTextResponse
 * @mixin PendingStructuredRequest
 */
class PrismClient
{
    protected PendingTextRequest|PendingStructuredRequest|PendingEmbeddingRequest|null $prism = null;

    public function __construct($mode)
    {
        $this->prism = match ($mode) {
            LlmMode::JSON => Prism::structured(),
            LlmMode::TEXT => Prism::text(),
            LlmMode::EMBED => Prism::embeddings(),
        };
    }

    public function send(): PendingEmbeddingResponse|PendingStructuredResponse|PendingTextResponse
    {
        $method = match (true) {
            $this->prism instanceof PendingStructuredRequest => 'asStructured',
            $this->prism instanceof PendingTextRequest => 'asText',
            $this->prism instanceof PendingEmbeddingRequest => 'asEmbeddings',
            default => 'generate',
        };

        return $this->prism->{$method}();
    }


    public function tap(callable $callback): static
    {
        call_user_func($callback, $this->prism);

        return $this;
    }

    public function __call(string $name, array $arguments)
    {
        $this->prism->{$name}(...$arguments);
        return $this;
    }

}