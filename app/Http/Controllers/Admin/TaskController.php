<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\TaskRequest;
use App\Models\Task;

class TaskController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', Task::class);
    }

    public function create(TaskRequest $request)
    {
        $this->authorize('create', Task::class);
        $validated = $request->validated();
        $task = Task::create($validated);
        return response()->json($task);
    }

    public function show(Task $task)
    {
        $this->authorize('view', $task);
        return response()->json($task);
    }

    public function update(Task $task)
    {
        $this->authorize('update', $task);
        $validated = $request->validated();
        $task->update($validated);
        return response()->json($task);
    }

    public function destroy(Task $task)
    {
        $this->authorize('delete', $task);
        $task->delete();
        return response()->json(['message' => 'Task deleted']);
    }
}
