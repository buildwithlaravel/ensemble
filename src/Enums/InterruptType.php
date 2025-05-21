<?php

namespace BuildWithLaravel\Ensemble\Enums;

enum InterruptType: string
{
    case Halt = 'halt';
    case Retry = 'retry';
    case WaitHuman = 'wait_human';
    case WaitEvent = 'wait_event';
    case CallTool = 'call_tool';
    case Delegate = 'delegate';
    case Done = 'done';
    case WaitForQueue = 'queued';
    case Error = 'error';
}
