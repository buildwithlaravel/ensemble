<?php

namespace BuildWithLaravel\Ensemble\Enums;

enum RunStatus: string
{
    case Scheduled = 'scheduled';
    case Running = 'running';
    case Interrupted = 'interrupted';
    case Error = 'error';
    case Completed = 'completed';

}