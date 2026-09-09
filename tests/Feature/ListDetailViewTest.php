<?php

namespace Tests\Feature;

use App\Models\User;
use App\Modules\List\Models\TodoList;
use App\Modules\Task\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ListDetailViewTest extends TestCase
{
    use RefreshDatabase;

    public function test_list_detail_page_renders_with_list_name_and_back_button(): void
    {
        $user = User::factory()->create();
        $list = TodoList::create([
            'name' => 'Kuliah PPK Sprint 1',
            'owner_id' => $user->id,
        ]);

        $response = $this->get('/lists/'.$list->id);

        $response->assertStatus(200);
        $response->assertSee('Kuliah PPK Sprint 1');
        $response->assertSee('Kembali ke Dashboard');
        $response->assertSee('Tambah tugas baru...');
        $response->assertSee('quick-task-priority', false);
        $response->assertSee('quick-task-deadline', false);
        $response->assertSee('modal-task-detail', false);

        // Verify team boundary: No invite members or progress tracker
        $response->assertDontSee('Undang anggota');
        $response->assertDontSee('Progress tracker');
    }

    public function test_list_detail_page_returns_404_if_not_found(): void
    {
        $response = $this->get('/lists/99999');
        $response->assertStatus(404);
    }

    public function test_api_lists_show_returns_single_list(): void
    {
        $user = User::factory()->create();
        $list = TodoList::create([
            'name' => 'Single List Test',
            'owner_id' => $user->id,
        ]);

        Task::create([
            'list_id' => $list->id,
            'title' => 'Single List Task 1',
            'created_by' => $user->id,
        ]);

        $response = $this->withHeader('X-User-Id', (string) $user->id)
            ->getJson('/api/lists/'.$list->id);

        $response->assertStatus(200)
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('data.id', $list->id)
            ->assertJsonPath('data.name', 'Single List Test')
            ->assertJsonPath('data.tasks_count', 1);
    }
}

