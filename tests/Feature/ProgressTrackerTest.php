<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\TodoList;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProgressTrackerTest extends TestCase
{
    use RefreshDatabase;

    public function test_progress_stats_calculation(): void
    {
        $owner = User::factory()->create();
        $list = TodoList::create([
            'name' => 'Proyek Tracking',
            'owner_id' => $owner->id,
        ]);

        // Buat 4 tugas: 2 done, 1 not done, 1 canceled
        Task::create(['list_id' => $list->id, 'title' => 'T1', 'status' => 'done', 'created_by' => $owner->id]);
        Task::create(['list_id' => $list->id, 'title' => 'T2', 'status' => 'done', 'created_by' => $owner->id]);
        Task::create(['list_id' => $list->id, 'title' => 'T3', 'status' => 'not done', 'created_by' => $owner->id]);
        Task::create(['list_id' => $list->id, 'title' => 'T4', 'status' => 'canceled', 'created_by' => $owner->id]);

        $stats = $list->getProgressStats();

        $this->assertEquals(4, $stats['total']);
        $this->assertEquals(2, $stats['done']);
        $this->assertEquals(1, $stats['not_done']);
        $this->assertEquals(1, $stats['canceled']);
        $this->assertEquals(50, $stats['percentage']); // 2/4 = 50%
    }

    public function test_siapa_mengerjakan_apa_workload_breakdown(): void
    {
        $owner = User::factory()->create(['name' => 'Budi Owner']);
        $member = User::factory()->create(['name' => 'Siti Member']);

        $list = TodoList::create([
            'name' => 'Proyek Tracking',
            'owner_id' => $owner->id,
        ]);
        $list->members()->attach($member->id, ['joined_at' => now()]);

        // Tugas Budi: 1 done
        Task::create(['list_id' => $list->id, 'title' => 'Tugas Budi', 'status' => 'done', 'assignee_id' => $owner->id, 'created_by' => $owner->id]);

        // Tugas Siti: 1 done, 1 not done (50%)
        Task::create(['list_id' => $list->id, 'title' => 'Tugas Siti 1', 'status' => 'done', 'assignee_id' => $member->id, 'created_by' => $owner->id]);
        Task::create(['list_id' => $list->id, 'title' => 'Tugas Siti 2', 'status' => 'not done', 'assignee_id' => $member->id, 'created_by' => $owner->id]);

        $workload = $list->getMembersWorkload();

        $budiWorkload = $workload->firstWhere('user.id', $owner->id);
        $this->assertNotNull($budiWorkload);
        $this->assertEquals(1, $budiWorkload['total']);
        $this->assertEquals(1, $budiWorkload['done']);
        $this->assertEquals(100, $budiWorkload['percentage']);

        $sitiWorkload = $workload->firstWhere('user.id', $member->id);
        $this->assertNotNull($sitiWorkload);
        $this->assertEquals(2, $sitiWorkload['total']);
        $this->assertEquals(1, $sitiWorkload['done']);
        $this->assertEquals(50, $sitiWorkload['percentage']);
    }

    public function test_list_detail_renders_progress_tracker_ui(): void
    {
        $owner = User::factory()->create();
        $list = TodoList::create([
            'name' => 'Proyek Tracking UI',
            'owner_id' => $owner->id,
        ]);

        Task::create(['list_id' => $list->id, 'title' => 'T1 Selesai', 'status' => 'done', 'created_by' => $owner->id]);
        Task::create(['list_id' => $list->id, 'title' => 'T2 Belum', 'status' => 'not done', 'created_by' => $owner->id]);

        $response = $this->actingAs($owner)->get(route('lists.show', $list->id));

        $response->assertStatus(200);
        $response->assertSee('Progress Tracker List (FR-10)');
        $response->assertSee('50%');
        $response->assertSee('Siapa Mengerjakan Apa');
    }
}
