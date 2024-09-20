<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\TaskRequest;
use App\Http\Resources\TaskCollection;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    use AuthorizesRequests;


    public function index(Request $request)
    {
        $query = Task::query();

        if ($request->has('search') && !empty($request->input('search'))) {
            $query->where('title', 'LIKE', '%' . $request->input('search') . '%');
        }

        if ($request->has('priorities') && !empty($request->input('priorities'))) {
            $priorities = explode(',', $request->input('priorities'));
            $query->whereIn('priority', $priorities);
        }

        if ($request->has('statuses') && !empty($request->input('statuses'))) {
            $statuses = explode(',', $request->input('statuses'));
            $query->whereIn('status', $statuses);
        }

        if ($request->has('start_date') && !empty($request->input('start_date'))) {
            $query->whereDate('start_date', '>=', $request->input('start_date'));
        }

        if ($request->has('end_date') && !empty($request->input('end_date'))) {
            $query->whereDate('end_date', '<=', $request->input('end_date'));
        }

        $tasks = $query->paginate(10);

        return response()->json($tasks);
    }

    public function store(TaskRequest $request)
    {
        $data = $request->validated();
        $data['auth_id'] = Auth::id();
        $task = Task::create(array_merge($data, ['auth_id' => auth()->id()]));
        return response()->json($task, 201);
    }

    public function show($id)
    {
        $task = Task::findOrFail($id);
        return new TaskResource($task);
    }

    public function update(TaskRequest $request, Task $task)
    {
        $this->authorize('update', $task);
        $task->update($request->validated());
        return new TaskResource($task);
    }

    public function destroy($id)
    {
        $task = Task::find($id);
        if (!$task) {
                return response()->json(['message' => 'Task not found'], 404);
            }
        if ($task->auth_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        $task->delete();
        return response()->json(['message'=> 'Task deleted successfully'], 200);
//        try {
//            $task = Task::findOrFail($id);
//            $this->authorize('delete', $task);
//
//            $task->delete();
//            return response()->json(['message' => 'Task deleted successfully'], 200);
//        } catch (\Exception $e) {
//            return response()->json(['message' => 'Task cannot be deleted'], 500);
//        }
    }
}
