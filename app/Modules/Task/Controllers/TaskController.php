<?php

namespace App\Modules\Task\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Task\Requests\CreateTaskRequest;
use App\Modules\Task\Requests\UpdateTaskRequest;
use App\Modules\Task\Requests\UpdateTaskStatusRequest;
use App\Modules\Task\Services\TaskService;
use Illuminate\Http\JsonResponse;

class TaskController extends Controller
{
    public function __construct(
        protected TaskService $taskService
    ) {}

    /**
     * Display a listing of tasks in a specific list (FR-03).
     */
    public function index(int $list_id): JsonResponse
    {
        $tasks = $this->taskService->getTasksByList($list_id);

        return response()->json([
            'status' => 'success',
            'data' => $tasks,
        ]);
    }

    /**
     * Store a newly created task in a list (FR-03, FR-04, FR-05).
     */
    public function store(CreateTaskRequest $request, int $list_id): JsonResponse
    {
        $userId = (int) ($request->user()?->id ?? $request->attributes->get('current_user_id', 1));
        $task = $this->taskService->createTask($list_id, $request->validated(), $userId);

        return response()->json([
            'status' => 'success',
            'message' => 'Task created successfully.',
            'data' => $task,
        ], 201);
    }

    /**
     * Update the specified task (FR-04, FR-05).
     */
    public function update(UpdateTaskRequest $request, int $id): JsonResponse
    {
        $task = $this->taskService->updateTask($id, $request->validated());

        return response()->json([
            'status' => 'success',
            'message' => 'Task updated successfully.',
            'data' => $task,
        ]);
    }

    /**
     * Set or toggle the task status (FR-06).
     */
    public function updateStatus(UpdateTaskStatusRequest $request, int $id): JsonResponse
    {
        $task = $this->taskService->toggleOrSetStatus($id, $request->input('status'));

        return response()->json([
            'status' => 'success',
            'message' => 'Task status updated successfully.',
            'data' => $task,
        ]);
    }

    /**
     * Remove the specified task.
     */
    public function destroy(int $id): JsonResponse
    {
        $this->taskService->deleteTask($id);

        return response()->json([
            'status' => 'success',
            'message' => 'Task deleted successfully.',
        ]);
    }
}
