<?php

namespace BuildWithLaravel\Ensemble\Enums;

enum EventType: string
{
    case RunStarted = 'RunStarted';
    case RunFinished = 'RunFinished';
    case RunError = 'RunError';
    case StepStarted = 'StepStarted';
    case StepFinished = 'StepFinished';
    case StepError = 'StepError';
    case TextMessageStart = 'TextMessageStart';
    case TextMessageContent = 'TextMessageContent';
    case TextMessageEnd = 'TextMessageEnd';
    case ToolCallStart = 'ToolCallStart';
    case ToolCallArgs = 'ToolCallArgs';
    case ToolCallEnd = 'ToolCallEnd';
    case StateSnapshot = 'StateSnapshot';
    case StateDelta = 'StateDelta';
    case MessagesSnapshot = 'MessagesSnapshot';
    case Raw = 'Raw';
    case Custom = 'Custom';

    case StatusUpdate = 'StatusUpdate';
}