<?php

namespace App\Modules\List\Services;

use App\Modules\List\Models\TodoList;
use Illuminate\Database\Eloquent\Collection;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class TodoListService
{
    /**
     * Get all lists owned by the specified user.
     *
     * @return Collection<int, TodoList>
     */
    public function getUserLists(int $userId): Collection
    {
        return TodoList::where('owner_id', $userId)->get();
    }

    /**
     * Create a new list for the specified user.
     *
     * @param  array{name: string}  $data
     */
    public function createList(array $data, int $userId): TodoList
    {
        return TodoList::create([
            'name' => $data['name'],
            'owner_id' => $userId,
        ]);
    }

    /**
     * Update an existing list's name (only if the user is the owner).
     *
     * @param  array{name: string}  $data
     *
     * @throws AccessDeniedHttpException
     */
    public function updateList(int $id, array $data, int $userId): TodoList
    {
        $list = TodoList::findOrFail($id);

        if ((int) $list->owner_id !== (int) $userId) {
            throw new AccessDeniedHttpException('Unauthorized. Only the owner can modify this list.');
        }

        $list->update([
            'name' => $data['name'],
        ]);

        return $list;
    }

    /**
     * Delete a list and all its associated tasks (only if the user is the owner).
     *
     * @throws AccessDeniedHttpException
     */
    public function deleteList(int $id, int $userId): void
    {
        $list = TodoList::findOrFail($id);

        if ((int) $list->owner_id !== (int) $userId) {
            throw new AccessDeniedHttpException('Unauthorized. Only the owner can delete this list.');
        }

        $list->delete();
    }
}

