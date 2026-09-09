<?php

namespace Tests\Feature;

use App\Models\User;
use App\Modules\List\Models\TodoList;
use App\Modules\Task\Enums\TaskPriority;
use App\Modules\Task\Enums\TaskStatus;
use App\Modules\Task\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ListAndTaskApiTest extends TestCase
{
    use RefreshDatabase;

    protected User $user1;

    protected User $user2;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user1 = User::factory()->create();
        $this->user2 = User::factory()->create();
    }

    /*
    |--------------------------------------------------------------------------
    | List Endpoints (FR-02) Tests
    |--------------------------------------------------------------------------
    */

    public function test_get_lists_returns_only_lists_owned_by_current_user(): void
    {
        TodoList::create(['name' => 'User 1 List A', 'owner_id' => $this->user1->id]);
        TodoList::create(['name' => 'User 1 List B', 'owner_id' => $this->user1->id]);
        TodoList::create(['name' => 'User 2 List', 'owner_id' => $this->user2->id]);

        $response = $this->withHeader('X-User-Id', (string) $this->user1->id)
            ->getJson('/api/lists');

        $response->assertStatus(200)
            ->assertJsonPath('status', 'success')
            ->assertJsonCount(2, 'data')
            ->assertJsonFragment(['name' => 'User 1 List A'])
            ->assertJsonFragment(['name' => 'User 1 List B'])
            ->assertJsonMissing(['name' => 'User 2 List']);
    }

    public function test_post_lists_creates_new_list_with_current_user_as_owner(): void
    {
        $response = $this->withHeader('X-User-Id', (string) $this->user1->id)
            ->postJson('/api/lists', [
                'name' => 'My New Project List',
            ]);

        $response->assertStatus(201)
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('data.name', 'My New Project List')
            ->assertJsonPath('data.owner_id', $this->user1->id);

        $this->assertDatabaseHas('lists', [
            'name' => 'My New Project List',
            'owner_id' => $this->user1->id,
        ]);
    }

    public function test_post_lists_validation_fails_when_name_is_missing(): void
    {
        $response = $this->withHeader('X-User-Id', (string) $this->user1->id)
            ->postJson('/api/lists', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name']);
    }

    public function test_put_lists_updates_name_if_user_is_owner(): void
    {
        $list = TodoList::create([
            'name' => 'Old Name',
            'owner_id' => $this->user1->id,
        ]);

        $response = $this->withHeader('X-User-Id', (string) $this->user1->id)
            ->putJson('/api/lists/'.$list->id, [
                'name' => 'Updated Name',
            ]);

        $response->assertStatus(200)
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('data.name', 'Updated Name');

        $this->assertDatabaseHas('lists', [
            'id' => $list->id,
            'name' => 'Updated Name',
        ]);
    }

    public function test_put_lists_returns_403_if_user_is_not_owner(): void
    {
        $list = TodoList::create([
            'name' => 'User 1 List',
            'owner_id' => $this->user1->id,
        ]);

        $response = $this->withHeader('X-User-Id', (string) $this->user2->id)
            ->putJson('/api/lists/'.$list->id, [
                'name' => 'Hacked Name',
            ]);

        $response->assertStatus(403);
    }

    public function test_delete_lists_deletes_list_and_cascades_tasks_if_user_is_owner(): void
    {
        $list = TodoList::create([
            'name' => 'List to delete',
            'owner_id' => $this->user1->id,
        ]);

        $task = Task::create([
            'list_id' => $list->id,
            'title' => 'Task inside list',
            'created_by' => $this->user1->id,
        ]);

        $response = $this->withHeader('X-User-Id', (string) $this->user1->id)
            ->deleteJson('/api/lists/'.$list->id);

        $response->assertStatus(200)
            ->assertJsonPath('status', 'success');

        $this->assertDatabaseMissing('lists', ['id' => $list->id]);
        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }

    public function test_delete_lists_returns_403_if_user_is_not_owner(): void
    {
        $list = TodoList::create([
            'name' => 'User 1 List',
            'owner_id' => $this->user1->id,
        ]);

        $response = $this->withHeader('X-User-Id', (string) $this->user2->id)
            ->deleteJson('/api/lists/'.$list->id);

        $response->assertStatus(403);
        $this->assertDatabaseHas('lists', ['id' => $list->id]);
    }

    /*
    |--------------------------------------------------------------------------
    | Task Endpoints (FR-03 s.d. FR-06) Tests
    |--------------------------------------------------------------------------
    */

    public function test_get_tasks_returns_tasks_in_specified_list(): void
    {
        $list = TodoList::create(['name' => 'Work', 'owner_id' => $this->user1->id]);
        $otherList = TodoList::create(['name' => 'Personal', 'owner_id' => $this->user1->id]);

        Task::create(['list_id' => $list->id, 'title' => 'Task 1', 'created_by' => $this->user1->id]);
        Task::create(['list_id' => $list->id, 'title' => 'Task 2', 'created_by' => $this->user1->id]);
        Task::create(['list_id' => $otherList->id, 'title' => 'Other Task', 'created_by' => $this->user1->id]);

        $response = $this->getJson('/api/lists/'.$list->id.'/tasks');

        $response->assertStatus(200)
            ->assertJsonPath('status', 'success')
            ->assertJsonCount(2, 'data')
            ->assertJsonFragment(['title' => 'Task 1'])
            ->assertJsonFragment(['title' => 'Task 2'])
            ->assertJsonMissing(['title' => 'Other Task']);
    }

    public function test_post_tasks_creates_task_with_default_priority_and_status(): void
    {
        $list = TodoList::create(['name' => 'Sprint 1', 'owner_id' => $this->user1->id]);

        $response = $this->withHeader('X-User-Id', (string) $this->user1->id)
            ->postJson('/api/lists/'.$list->id.'/tasks', [
                'title' => 'Write documentation',
                'deadline' => '2026-09-15 17:00:00',
            ]);

        $response->assertStatus(201)
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('data.title', 'Write documentation')
            ->assertJsonPath('data.priority', TaskPriority::Medium->value)
            ->assertJsonPath('data.status', TaskStatus::NotDone->value)
            ->assertJsonPath('data.created_by', $this->user1->id);

        $this->assertDatabaseHas('tasks', [
            'list_id' => $list->id,
            'title' => 'Write documentation',
            'priority' => 'Medium',
            'status' => 'not done',
            'created_by' => $this->user1->id,
        ]);
    }

    public function test_post_tasks_with_custom_priority(): void
    {
        $list = TodoList::create(['name' => 'Sprint 1', 'owner_id' => $this->user1->id]);

        $response = $this->withHeader('X-User-Id', (string) $this->user1->id)
            ->postJson('/api/lists/'.$list->id.'/tasks', [
                'title' => 'Critical Bug Fix',
                'priority' => 'High',
            ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.priority', 'High');
    }

    public function test_put_tasks_updates_title_priority_and_deadline(): void
    {
        $list = TodoList::create(['name' => 'Sprint 1', 'owner_id' => $this->user1->id]);
        $task = Task::create([
            'list_id' => $list->id,
            'title' => 'Initial Title',
            'priority' => TaskPriority::Low,
            'created_by' => $this->user1->id,
        ]);

        $response = $this->putJson('/api/tasks/'.$task->id, [
            'title' => 'Updated Title',
            'priority' => 'High',
            'deadline' => '2026-09-20',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('data.title', 'Updated Title')
            ->assertJsonPath('data.priority', 'High');

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'title' => 'Updated Title',
            'priority' => 'High',
        ]);
    }

    public function test_patch_task_status_explicit_and_toggle(): void
    {
        $list = TodoList::create(['name' => 'Sprint 1', 'owner_id' => $this->user1->id]);
        $task = Task::create([
            'list_id' => $list->id,
            'title' => 'Task to complete',
            'status' => TaskStatus::NotDone,
            'created_by' => $this->user1->id,
        ]);

        // Explicit set to done
        $response = $this->patchJson('/api/tasks/'.$task->id.'/status', [
            'status' => 'done',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.status', 'done');
        $this->assertSame('done', $task->fresh()->status->value);

        // Toggle without body (done -> not done)
        $response = $this->patchJson('/api/tasks/'.$task->id.'/status', []);

        $response->assertStatus(200)
            ->assertJsonPath('data.status', 'not done');
        $this->assertSame('not done', $task->fresh()->status->value);

        // Toggle again without body (not done -> done)
        $response = $this->patchJson('/api/tasks/'.$task->id.'/status', []);

        $response->assertStatus(200)
            ->assertJsonPath('data.status', 'done');
        $this->assertSame('done', $task->fresh()->status->value);
    }

    public function test_delete_task_deletes_task(): void
    {
        $list = TodoList::create(['name' => 'Sprint 1', 'owner_id' => $this->user1->id]);
        $task = Task::create([
            'list_id' => $list->id,
            'title' => 'Task to be deleted',
            'created_by' => $this->user1->id,
        ]);

        $response = $this->deleteJson('/api/tasks/'.$task->id);

        $response->assertStatus(200)
            ->assertJsonPath('status', 'success');

        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }

    public function test_validation_fails_on_invalid_priority(): void
    {
        $list = TodoList::create(['name' => 'Sprint 1', 'owner_id' => $this->user1->id]);

        $response = $this->postJson('/api/lists/'.$list->id.'/tasks', [
            'title' => 'Task with invalid priority',
            'priority' => 'SuperUrgent',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['priority']);
    }
}

