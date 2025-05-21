<?php

use BuildWithLaravel\Ensemble\Models\Run;
use BuildWithLaravel\Ensemble\Support\Traits\ResolvesEnsembleModels;
use Illuminate\Support\Facades\Config;

describe('ResolvesEnsembleModels', function () {
    it('returns default model if config is not set', function () {
        // TODO: Test that the default model is returned if config is not set
    });

    it('returns custom model if config is set', function () {
        // TODO: Test that a custom model is returned if config is set
    });

    it('resolves models from config', function () {
        // TODO: Test that models are resolved from config
    });

    it('throws if class does not exist', function () {
        // TODO: Test that an exception is thrown if the class does not exist
    });

    it('throws if class does not extend base (when mustExtend is set)', function () {
        // TODO: Test that an exception is thrown if the class does not extend the base class when mustExtend is set
    });
});
