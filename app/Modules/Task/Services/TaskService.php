<?php

namespace App\Modules\Task\Services;

use App\Modules\List\Models\TodoList;
use App\Modules\Task\Enums\TaskStatus;
use App\Modules\Task\Models\Task;
use Illuminate\Database\Eloquent\Collection;

class TaskService
{
    /**
     * Get all tasks within a specific list.
     *
     * @return Collection<int, Task>
     */
    public function getTasksByList(int $listId): Collection
    {
        TodoList::findOrFail($listId);

        return Task::where('list_id', $listId)->get();
    }

    /**
     * Create a new task within a specific list (FR-03, FR-04, FR-05).
     *
     * @param  array<string, mixed>  $data
     */
    public function createTask(int $listId, array $data, int $userId): Task
    {
        TodoList::findOrFail($listId);

        return Task::create([
            'list_id' => $listId,
            'title' => $data['title'],
            'priority' => $data['priority'] ?? 'Medium',
            'deadline' => $data['deadline'] ?? null,
            'status' => 'not done',
            'created_by' => $userId,
            'assignee_id' => $data['assignee_id'] ?? null,
        ]);
    }

    /**
     * Update title, priority, deadline, and assignee of a task.
     *
     * @param  array<string, mixed>  $data
     */
    public function updateTask(int $id, array $data): Task
    {
        $task = Task::findOrFail($id);

        $task->update($data);

        return $task->fresh();
    }

    /**
     * Set or toggle task completion status (FR-06).
     */
    public function toggleOrSetStatus(int $id, ?string $status = null): Task
    {
        $task = Task::findOrFail($id);

        if ($status !== null && $status !== '') {
            $task->status = $status instanceof TaskStatus ? $status : TaskStatus::from($status);
        } else {
            // Toggle current status
            $task->status = $task->isDone() ? TaskStatus::NotDone : TaskStatus::Done;
        }

        $task->save();

        return $task->fresh();
    }

    /**
     * Delete a task.
     */
    public function deleteTask(int $id): void
    {
        $task = Task::findOrFail($id);
        $task->delete();
    }
}

