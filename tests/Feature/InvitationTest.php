<?php

namespace Tests\Feature;

use App\Models\Invitation;
use App\Models\TodoList;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvitationTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_can_generate_invitation_link_and_email(): void
    {
        $owner = User::factory()->create();
        $list = TodoList::create([
            'name' => 'Test List',
            'owner_id' => $owner->id,
        ]);

        $response = $this->actingAs($owner)->post(route('invitations.store', $list->id), [
            'invited_email' => 'colleague@example.com',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('invitations', [
            'list_id' => $list->id,
            'invited_email' => 'colleague@example.com',
            'status' => 'pending',
        ]);
    }

    public function test_non_owner_cannot_invite_members(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $list = TodoList::create([
            'name' => 'Test List',
            'owner_id' => $owner->id,
        ]);

        $response = $this->actingAs($otherUser)->post(route('invitations.store', $list->id), [
            'invited_email' => 'colleague@example.com',
        ]);

        $response->assertStatus(403);
    }

    public function test_user_can_accept_invitation_and_become_member(): void
    {
        $owner = User::factory()->create();
        $invitee = User::factory()->create();
        $list = TodoList::create([
            'name' => 'Test List',
            'owner_id' => $owner->id,
        ]);

        $invitation = Invitation::create([
            'list_id' => $list->id,
            'invited_email' => $invitee->email,
            'invited_by' => $owner->id,
            'token' => 'test-token-12345',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($invitee)->post(route('invitations.accept', 'test-token-12345'));

        $response->assertRedirect(route('lists.show', $list->id));
        $this->assertDatabaseHas('list_members', [
            'list_id' => $list->id,
            'user_id' => $invitee->id,
        ]);
        $this->assertEquals('accepted', $invitation->fresh()->status);
    }

    public function test_user_can_reject_invitation(): void
    {
        $owner = User::factory()->create();
        $invitee = User::factory()->create();
        $list = TodoList::create([
            'name' => 'Test List',
            'owner_id' => $owner->id,
        ]);

        $invitation = Invitation::create([
            'list_id' => $list->id,
            'invited_email' => $invitee->email,
            'invited_by' => $owner->id,
            'token' => 'test-reject-token',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($invitee)->post(route('invitations.reject', 'test-reject-token'));

        $response->assertRedirect(route('dashboard'));
        $this->assertEquals('rejected', $invitation->fresh()->status);
        $this->assertDatabaseMissing('list_members', [
            'list_id' => $list->id,
            'user_id' => $invitee->id,
        ]);
    }
}
