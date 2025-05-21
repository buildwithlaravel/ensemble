<?php

namespace BuildWithLaravel\Ensemble\Enums;

enum LlmMode: string
{
    case TEXT = 'text';

    case JSON = 'json';

    case EMBED = 'embed';

}