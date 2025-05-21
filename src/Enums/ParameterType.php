<?php

namespace BuildWithLaravel\Ensemble\Enums;

enum ParameterType: string
{
    case String = 'string';
    case Integer = 'integer';
    case Boolean = 'boolean';
    case Array = 'array';
    case Object = 'object';
    case Null = 'null';
}
