<?php

namespace App\Modules\Task\Enums;

enum TaskStatus: string
{
    case Done = 'done';
    case NotDone = 'not done';
}
