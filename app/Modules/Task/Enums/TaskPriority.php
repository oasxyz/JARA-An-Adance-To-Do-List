<?php

namespace App\Modules\Task\Enums;

enum TaskPriority: string
{
    case Low = 'Low';
    case Medium = 'Medium';
    case High = 'High';
}
