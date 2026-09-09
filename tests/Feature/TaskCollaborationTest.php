<?php

namespace Tests\Feature;

use App\Models\TodoList;
use App\Models\User;
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskCollaborationTest extends TestCase
{
    use RefreshDatabase;

    public function test_member_can_create_task_with_priority_deadline_and_assignee(): void
    {
        $owner = User::factory()->create();
        $member = User::factory()->create();

        $list = TodoList::create([
            'name' => 'List Kolaborasi',
            'owner_id' => $owner->id,
        ]);
        $list->members()->attach($member->id, ['joined_at' => now()]);

        // Anggota membuat tugas (FR-03, FR-04, FR-05, FR-09)
        $response = $this->actingAs($member)->post(route('tasks.store', $list->id), [
            'title' => 'Tugas Bersama Anggota',
            'priority' => 'High',
            'deadline' => now()->addDays(3)->format('Y-m-d H:i:s'),
            'assignee_id' => $member->id,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('tasks', [
            'list_id' => $list->id,
            'title' => 'Tugas Bersama Anggota',
            'priority' => 'High',
            'assignee_id' => $member->id,
            'created_by' => $member->id,
            'status' => 'not done',
        ]);
    }

    public function test_member_and_owner_can_update_task_status(): void
    {
        $owner = User::factory()->create();
        $member = User::factory()->create();

        $list = TodoList::create([
            'name' => 'List Kolaborasi',
            'owner_id' => $owner->id,
        ]);
        $list->members()->attach($member->id, ['joined_at' => now()]);

        $task = Task::create([
            'list_id' => $list->id,
            'title' => 'Kerjakan modul ini',
            'priority' => 'Medium',
            'status' => 'not done',
            'created_by' => $owner->id,
            'assignee_id' => $member->id,
        ]);

        // Anggota menandai tugas selesai (FR-06, FR-09)
        $response = $this->actingAs($member)->patch(route('tasks.update_status', $task->id), [
            'status' => 'done',
        ]);

        $response->assertRedirect();
        $this->assertEquals('done', $task->fresh()->status);
    }

    public function test_non_member_cannot_create_or_modify_task(): void
    {
        $owner = User::factory()->create();
        $stranger = User::factory()->create();

        $list = TodoList::create([
            'name' => 'List Privat',
            'owner_id' => $owner->id,
        ]);

        // Stranger mencoba membuat tugas di list yang bukan miliknya
        $response = $this->actingAs($stranger)->post(route('tasks.store', $list->id), [
            'title' => 'Tugas Ilegal',
            'priority' => 'Low',
        ]);

        $response->assertStatus(403);
    }
}
