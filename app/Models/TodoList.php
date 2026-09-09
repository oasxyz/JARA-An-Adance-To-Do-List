<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

class TodoList extends Model
{
    use HasFactory;

    protected $table = 'lists';

    protected $fillable = [
        'name',
        'owner_id',
    ];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'list_members', 'list_id', 'user_id')
            ->withPivot('joined_at')
            ->withTimestamps();
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class, 'list_id');
    }

    public function invitations(): HasMany
    {
        return $this->hasMany(Invitation::class, 'list_id');
    }

    public function isOwner($user): bool
    {
        $userId = $user instanceof User ? $user->id : $user;
        return (int) $this->owner_id === (int) $userId;
    }

    public function isMember($user): bool
    {
        $userId = $user instanceof User ? $user->id : $user;
        return $this->members()->where('users.id', $userId)->exists();
    }

    public function hasAccess($user): bool
    {
        return $this->isOwner($user) || $this->isMember($user);
    }

    /**
     * Mengembalikan seluruh partisipan list (Pemilik + Anggota)
     */
    public function allParticipants(): Collection
    {
        $participants = collect([$this->owner]);
        foreach ($this->members as $member) {
            if (!$participants->contains('id', $member->id)) {
                $participants->push($member);
            }
        }
        return $participants;
    }

    /**
     * Hitung statistik progress keseluruhan (FR-10)
     */
    public function getProgressStats(): array
    {
        $total = $this->tasks()->count();
        $done = $this->tasks()->where('status', 'done')->count();
        $notDone = $this->tasks()->where('status', 'not done')->count();
        $canceled = $this->tasks()->where('status', 'canceled')->count();

        $percentage = $total > 0 ? round(($done / $total) * 100) : 0;

        return [
            'total' => $total,
            'done' => $done,
            'not_done' => $notDone,
            'canceled' => $canceled,
            'percentage' => $percentage,
        ];
    }

    /**
     * Breakdown 'Siapa mengerjakan apa' (FR-10)
     */
    public function getMembersWorkload(): Collection
    {
        $participants = $this->allParticipants();
        $workload = collect();

        foreach ($participants as $person) {
            $userTasks = $this->tasks()->where('assignee_id', $person->id)->get();
            $total = $userTasks->count();
            $done = $userTasks->where('status', 'done')->count();
            $notDone = $userTasks->where('status', 'not done')->count();
            $canceled = $userTasks->where('status', 'canceled')->count();
            $percent = $total > 0 ? round(($done / $total) * 100) : 0;

            $workload->push([
                'user' => $person,
                'role' => $this->isOwner($person->id) ? 'Pemilik' : 'Anggota',
                'total' => $total,
                'done' => $done,
                'not_done' => $notDone,
                'canceled' => $canceled,
                'percentage' => $percent,
                'tasks' => $userTasks,
            ]);
        }

        // Tugas yang belum di-assign ke siapapun
        $unassignedTasks = $this->tasks()->whereNull('assignee_id')->get();
        if ($unassignedTasks->count() > 0) {
            $workload->push([
                'user' => (object) ['name' => 'Belum Ditugaskan', 'email' => '-'],
                'role' => 'Unassigned',
                'total' => $unassignedTasks->count(),
                'done' => $unassignedTasks->where('status', 'done')->count(),
                'not_done' => $unassignedTasks->where('status', 'not done')->count(),
                'canceled' => $unassignedTasks->where('status', 'canceled')->count(),
                'percentage' => $unassignedTasks->count() > 0 ? round(($unassignedTasks->where('status', 'done')->count() / $unassignedTasks->count()) * 100) : 0,
                'tasks' => $unassignedTasks,
            ]);
        }

        return $workload;
    }
}
