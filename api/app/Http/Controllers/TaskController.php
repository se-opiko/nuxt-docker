<?php

namespace App\Http\Controllers;

use App\Http\Requests\TaskStoreRequest;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\TaskSearchRequest;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(TaskSearchRequest $request): JsonResponse
    {
        try {
            $query = Task::query();
            if ($request->filled('status')) {
                $query->where('status', $request->input('status'));
            }
            $tasks = $query->get();
            return response()->json([
                'message' => 'タスク一覧の取得に成功しました',
                'tasks' => $tasks,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'タスク一覧の取得に失敗しました',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TaskStoreRequest $request)
    {
        $task = Task::create($request->validated());
        return response()->json([
            'message' => 'タスクが正常に作成されました',
            'task' => $task,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Task $task)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TaskStoreRequest $request, $id)
    {
        $task = Task::findOrFail($id);
        $task->update($request->validated());
        return response()->json([
            'message' => 'タスクが正常に更新されました',
            'task' => $task,
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, $id): JsonResponse
    {
        try {
            $task = Task::findOrFail($id);
            $task->delete();
            
            return response()->json([
                'message' => 'タスクが正常に削除されました',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'タスクの削除に失敗しました',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
