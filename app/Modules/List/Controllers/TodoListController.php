<?php

namespace App\Modules\List\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\List\Requests\CreateTodoListRequest;
use App\Modules\List\Requests\UpdateTodoListRequest;
use App\Modules\List\Services\TodoListService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TodoListController extends Controller
{
    public function __construct(
        protected TodoListService $todoListService
    ) {}

    /**
     * Display a listing of lists owned by the current user (FR-02).
     */
    public function index(Request $request): JsonResponse
    {
        $userId = (int) ($request->user()?->id ?? $request->attributes->get('current_user_id', 1));
        $lists = $this->todoListService->getUserLists($userId);

        return response()->json([
            'status' => 'success',
            'data' => $lists,
        ]);
    }

    /**
     * Store a newly created list in storage (FR-02).
     */
    public function store(CreateTodoListRequest $request): JsonResponse
    {
        $userId = (int) ($request->user()?->id ?? $request->attributes->get('current_user_id', 1));
        $list = $this->todoListService->createList($request->validated(), $userId);

        return response()->json([
            'status' => 'success',
            'message' => 'List created successfully.',
            'data' => $list,
        ], 201);
    }

    /**
     * Update the specified list (FR-02, owner only).
     */
    public function update(UpdateTodoListRequest $request, int $id): JsonResponse
    {
        $userId = (int) ($request->user()?->id ?? $request->attributes->get('current_user_id', 1));
        $list = $this->todoListService->updateList($id, $request->validated(), $userId);

        return response()->json([
            'status' => 'success',
            'message' => 'List updated successfully.',
            'data' => $list,
        ]);
    }

    /**
     * Remove the specified list and its tasks (FR-02, owner only).
     */
    public function destroy(Request $request, int $id): JsonResponse
    {
        $userId = (int) ($request->user()?->id ?? $request->attributes->get('current_user_id', 1));
        $this->todoListService->deleteList($id, $userId);

        return response()->json([
            'status' => 'success',
            'message' => 'List and its tasks deleted successfully.',
        ]);
    }
}
