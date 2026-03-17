<?php

namespace Callcocam\LaravelRaptorPlanogram\Enums;

enum GondolaWorkflowStatus: string
{
    case Pending = 'pending';
    case InProgress = 'in_progress';
    case Completed = 'completed';
    case Blocked = 'blocked';
    case Skipped = 'skipped';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Pending',
            self::InProgress => 'In Progress',
            self::Completed => 'Completed',
            self::Blocked => 'Blocked',
            self::Skipped => 'Skipped',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Pending => 'gray',
            self::InProgress => 'blue',
            self::Completed => 'green',
            self::Blocked => 'red',
            self::Skipped => 'yellow',
        };
    }
}
