<?php

namespace Tests\Feature;

use App\Models\User;
use App\Modules\List\Models\TodoList;
use App\Modules\Task\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardViewTest extends TestCase
{
    use RefreshDatabase;

    public function test_root_renders_dashboard(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('Daftar List Saya');
    }

    public function test_dashboard_page_is_accessible_and_renders_correctly(): void
    {
        $response = $this->get('/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Daftar List Saya');
        $response->assertSee('Tambah List');
        // Placeholder container for Yustinus
        $response->assertSee('auth-placeholder-container', false);
        // Team boundary: Must not have 'List yang Diikuti' section
        $response->assertDontSee('List yang Diikuti');
    }

    public function test_list_detail_page_is_accessible(): void
    {
        $response = $this->get('/lists/1');

        $response->assertStatus(200);
        $response->assertSee('Detail List #1');
        $response->assertSee('Kembali ke Dashboard');
    }

    public function test_api_lists_includes_tasks_count(): void
    {
        $user = User::factory()->create();

        $list = TodoList::create([
            'name' => 'Sprint 1 Tasks',
            'owner_id' => $user->id,
        ]);

        Task::create([
            'list_id' => $list->id,
            'title' => 'First Task',
            'created_by' => $user->id,
        ]);

        Task::create([
            'list_id' => $list->id,
            'title' => 'Second Task',
            'created_by' => $user->id,
        ]);

        $response = $this->withHeader('X-User-Id', (string) $user->id)
            ->getJson('/api/lists');

        $response->assertStatus(200)
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('data.0.tasks_count', 2);
    }
}
