<?php

namespace App\Modules\Task\Models;

use App\Models\User;
use App\Modules\List\Models\TodoList;
use App\Modules\Task\Enums\TaskPriority;
use App\Modules\Task\Enums\TaskStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'list_id',
    'title',
    'priority',
    'deadline',
    'status',
    'created_by',
    'assignee_id',
])]
class Task extends Model
{
    use HasFactory;

    public const PRIORITY_LOW = 'Low';

    public const PRIORITY_MEDIUM = 'Medium';

    public const PRIORITY_HIGH = 'High';

    public const STATUS_DONE = 'done';

    public const STATUS_NOT_DONE = 'not done';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tasks';

    /**
     * Default model attributes.
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'priority' => 'Medium',
        'status' => 'not done',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'deadline' => 'datetime',
            'priority' => TaskPriority::class,
            'status' => TaskStatus::class,
        ];
    }

    /**
     * Get the list that owns the task.
     *
     * @return BelongsTo<TodoList, $this>
     */
    public function list(): BelongsTo
    {
        return $this->belongsTo(TodoList::class, 'list_id');
    }

    /**
     * Get the user who created the task.
     *
     * @return BelongsTo<User, $this>
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user assigned to the task (nullable).
     *
     * @return BelongsTo<User, $this>
     */
    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assignee_id');
    }

    /**
     * Scope a query to only include completed tasks.
     *
     * @param  Builder<$this>  $query
     * @return Builder<$this>
     */
    public function scopeDone(Builder $query): Builder
    {
        return $query->where('status', TaskStatus::Done->value);
    }

    /**
     * Scope a query to only include incomplete tasks.
     *
     * @param  Builder<$this>  $query
     * @return Builder<$this>
     */
    public function scopeNotDone(Builder $query): Builder
    {
        return $query->where('status', TaskStatus::NotDone->value);
    }

    /**
     * Scope a query to filter by priority.
     *
     * @param  Builder<$this>  $query
     * @return Builder<$this>
     */
    public function scopeByPriority(Builder $query, TaskPriority|string $priority): Builder
    {
        $value = $priority instanceof TaskPriority ? $priority->value : $priority;

        return $query->where('priority', $value);
    }

    /**
     * Check if the task is marked as done.
     */
    public function isDone(): bool
    {
        return $this->status === TaskStatus::Done || $this->status?->value === 'done';
    }

    /**
     * Mark the task as done and save.
     */
    public function markAsDone(): bool
    {
        $this->status = TaskStatus::Done;

        return $this->save();
    }

    /**
     * Mark the task as not done and save.
     */
    public function markAsNotDone(): bool
    {
        $this->status = TaskStatus::NotDone;

        return $this->save();
    }
}
