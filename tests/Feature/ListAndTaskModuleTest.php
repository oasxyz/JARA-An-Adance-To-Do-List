<?php

namespace Tests\Feature;

use App\Models\User;
use App\Modules\List\Models\Lists;
use App\Modules\List\Models\TodoList;
use App\Modules\Task\Enums\TaskPriority;
use App\Modules\Task\Enums\TaskStatus;
use App\Modules\Task\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ListAndTaskModuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_lists_and_tasks_tables_have_expected_columns(): void
    {
        $this->assertTrue(Schema::hasTable('lists'));
        $this->assertTrue(Schema::hasColumns('lists', [
            'id',
            'name',
            'owner_id',
            'created_at',
            'updated_at',
        ]));

        $this->assertTrue(Schema::hasTable('tasks'));
        $this->assertTrue(Schema::hasColumns('tasks', [
            'id',
            'list_id',
            'title',
            'priority',
            'deadline',
            'status',
            'created_by',
            'assignee_id',
            'created_at',
            'updated_at',
        ]));
    }

    public function test_can_create_list_with_owner_using_todolist_and_lists_model(): void
    {
        $user = User::factory()->create();

        $todoList = TodoList::create([
            'name' => 'Project Jara Sprint 1',
            'owner_id' => $user->id,
        ]);

        $this->assertDatabaseHas('lists', [
            'id' => $todoList->id,
            'name' => 'Project Jara Sprint 1',
            'owner_id' => $user->id,
        ]);

        $this->assertTrue($todoList->owner->is($user));

        // Test Lists alias
        $retrievedByLists = Lists::find($todoList->id);
        $this->assertNotNull($retrievedByLists);
        $this->assertSame($todoList->name, $retrievedByLists->name);
    }

    public function test_can_create_task_with_default_status_and_priority(): void
    {
        $owner = User::factory()->create();
        $assignee = User::factory()->create();

        $list = TodoList::create([
            'name' => 'Backend Development',
            'owner_id' => $owner->id,
        ]);

        $task = Task::create([
            'list_id' => $list->id,
            'title' => 'Implement FR-02 and FR-03',
            'created_by' => $owner->id,
            'assignee_id' => $assignee->id,
            'deadline' => now()->addDays(3),
        ]);

        $this->assertSame('not done', $task->status->value);
        $this->assertSame('Medium', $task->priority->value);
        $this->assertFalse($task->isDone());
        $this->assertTrue($task->list->is($list));
        $this->assertTrue($task->creator->is($owner));
        $this->assertTrue($task->assignee->is($assignee));
        $this->assertNotNull($task->deadline);
    }

    public function test_can_update_task_status_and_priority(): void
    {
        $user = User::factory()->create();

        $list = TodoList::create([
            'name' => 'Sprint List',
            'owner_id' => $user->id,
        ]);

        $task = Task::create([
            'list_id' => $list->id,
            'title' => 'High Priority Task',
            'priority' => TaskPriority::High,
            'created_by' => $user->id,
        ]);

        $this->assertSame(TaskPriority::High, $task->priority);

        $task->markAsDone();
        $this->assertTrue($task->isDone());
        $this->assertSame(TaskStatus::Done, $task->fresh()->status);

        $task->markAsNotDone();
        $this->assertFalse($task->isDone());
        $this->assertSame(TaskStatus::NotDone, $task->fresh()->status);
    }

    public function test_task_scopes_filter_correctly(): void
    {
        $user = User::factory()->create();

        $list = TodoList::create([
            'name' => 'Scoping Test List',
            'owner_id' => $user->id,
        ]);

        Task::create([
            'list_id' => $list->id,
            'title' => 'Task 1 - Done High',
            'priority' => TaskPriority::High,
            'status' => TaskStatus::Done,
            'created_by' => $user->id,
        ]);

        Task::create([
            'list_id' => $list->id,
            'title' => 'Task 2 - Not Done Low',
            'priority' => TaskPriority::Low,
            'status' => TaskStatus::NotDone,
            'created_by' => $user->id,
        ]);

        $this->assertCount(1, Task::done()->get());
        $this->assertCount(1, Task::notDone()->get());
        $this->assertCount(1, Task::byPriority(TaskPriority::High)->get());
        $this->assertCount(1, Task::byPriority(TaskPriority::Low)->get());
        $this->assertCount(0, Task::byPriority(TaskPriority::Medium)->get());
    }

    public function test_cascade_delete_deletes_tasks_when_list_is_deleted(): void
    {
        $user = User::factory()->create();

        $list = TodoList::create([
            'name' => 'To be deleted',
            'owner_id' => $user->id,
        ]);

        $task1 = Task::create([
            'list_id' => $list->id,
            'title' => 'Sub task 1',
            'created_by' => $user->id,
        ]);

        $task2 = Task::create([
            'list_id' => $list->id,
            'title' => 'Sub task 2',
            'created_by' => $user->id,
        ]);

        $this->assertDatabaseHas('tasks', ['id' => $task1->id]);
        $this->assertDatabaseHas('tasks', ['id' => $task2->id]);

        $list->delete();

        $this->assertDatabaseMissing('lists', ['id' => $list->id]);
        $this->assertDatabaseMissing('tasks', ['id' => $task1->id]);
        $this->assertDatabaseMissing('tasks', ['id' => $task2->id]);
    }

    public function test_task_assignee_can_be_null(): void
    {
        $user = User::factory()->create();

        $list = TodoList::create([
            'name' => 'Unassigned Tasks List',
            'owner_id' => $user->id,
        ]);

        $task = Task::create([
            'list_id' => $list->id,
            'title' => 'Unassigned Task',
            'created_by' => $user->id,
            'assignee_id' => null,
        ]);

        $this->assertNull($task->assignee_id);
        $this->assertNull($task->assignee);
    }
}

