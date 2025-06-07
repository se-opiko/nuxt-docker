<?php

namespace App\Http\Controllers;

use App\Http\Requests\TaskStoreRequest;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\TaskSearchRequest;

/**
 * Task コントローラー
 * 
 * タスクの CRUD 操作を管理する
 */
class TaskController extends Controller
{
    /**
     * タスク一覧を取得する
     * プロジェクト情報も含めて取得する
     *
     * @param TaskSearchRequest $request バリデート済みリクエスト
     * @return JsonResponse タスク一覧のJSONレスポンス
     */
    public function index(TaskSearchRequest $request): JsonResponse
    {
        try {
            $query = Task::with('project');
            
            if ($request->filled('status')) {
                $query->where('status', $request->input('status'));
            }
            
            if ($request->filled('project_id')) {
                $query->where('project_id', $request->input('project_id'));
            }
            
            $tasks = $query->orderBy('created_at', 'desc')->get();
            
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
     * 新しいタスクを作成する
     *
     * @param TaskStoreRequest $request バリデート済みリクエスト
     * @return JsonResponse 作成されたタスクのJSONレスポンス
     */
    public function store(TaskStoreRequest $request)
    {
        try {
            $task = Task::create($request->validated());
            $task->load('project'); // プロジェクト情報を含めて返す
            
            return response()->json([
                'message' => 'タスクが正常に作成されました',
                'task' => $task,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'タスクの作成に失敗しました',
                'error' => $e->getMessage(),
            ], 500);
        }
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
     * 指定されたタスクを更新する
     *
     * @param TaskStoreRequest $request バリデート済みリクエスト
     * @param int $id タスクID
     * @return JsonResponse 更新されたタスクのJSONレスポンス
     */
    public function update(TaskStoreRequest $request, $id)
    {
        try {
            $task = Task::findOrFail($id);
            $task->update($request->validated());
            $task->load('project'); // プロジェクト情報を含めて返す
            
            return response()->json([
                'message' => 'タスクが正常に更新されました',
                'task' => $task,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'タスクの更新に失敗しました',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * 指定されたタスクを削除する
     *
     * @param int $id タスクID
     * @return JsonResponse 削除結果のJSONレスポンス
     */
    public function destroy($id)
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
