<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProjectStoreRequest;
use App\Models\Project;
use Illuminate\Http\JsonResponse;

/**
 * Project コントローラー
 * 
 * プロジェクトの CRUD 操作を管理する
 */
class ProjectController extends Controller
{
    /**
     * プロジェクト一覧を取得する
     *
     * @return JsonResponse プロジェクト一覧のJSONレスポンス
     */
    public function index(): JsonResponse
    {
        try {
            $projects = Project::with('tasks')->orderBy('created_at', 'desc')->get();
            
            return response()->json([
                'message' => 'プロジェクト一覧の取得に成功しました',
                'projects' => $projects,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'プロジェクト一覧の取得に失敗しました',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * 新しいプロジェクトを作成する
     *
     * @param ProjectStoreRequest $request バリデート済みリクエスト
     * @return JsonResponse 作成されたプロジェクトのJSONレスポンス
     */
    public function store(ProjectStoreRequest $request): JsonResponse
    {
        try {
            $project = Project::create($request->validated());
            
            return response()->json([
                'message' => 'プロジェクトが正常に作成されました',
                'project' => $project,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'プロジェクトの作成に失敗しました',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * 指定されたプロジェクトを取得する
     *
     * @param Project $project プロジェクトモデル
     * @return JsonResponse プロジェクトのJSONレスポンス
     */
    public function show(Project $project): JsonResponse
    {
        try {
            $project->load('tasks');
            
            return response()->json([
                'message' => 'プロジェクトの取得に成功しました',
                'project' => $project,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'プロジェクトの取得に失敗しました',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * 指定されたプロジェクトを更新する
     *
     * @param ProjectStoreRequest $request バリデート済みリクエスト
     * @param Project $project プロジェクトモデル（Route Model Binding）
     * @return JsonResponse 更新されたプロジェクトのJSONレスポンス
     */
    public function update(ProjectStoreRequest $request, Project $project): JsonResponse
    {
        try {
            $project->update($request->validated());
            
            return response()->json([
                'message' => 'プロジェクトが正常に更新されました',
                'project' => $project,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'プロジェクトの更新に失敗しました',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * 指定されたプロジェクトを削除する
     *
     * @param Project $project プロジェクトモデル（Route Model Binding）
     * @return JsonResponse 削除結果のJSONレスポンス
     */
    public function destroy(Project $project): JsonResponse
    {
        try {
            // 関連するタスクの数を確認
            $taskCount = $project->tasks()->count();
            
            if ($taskCount > 0) {
                // 関連するタスクがある場合は警告を含むレスポンス
                $project->tasks()->update(['project_id' => null]);
                $project->delete();
                
                return response()->json([
                    'message' => "プロジェクトが正常に削除されました。{$taskCount}件のタスクが未分類に移動されました。",
                    'warning' => "削除されたプロジェクトに関連していた{$taskCount}件のタスクは未分類になりました。",
                    'affected_tasks_count' => $taskCount,
                ], 200);
            } else {
                // 関連タスクがない場合は通常の削除
                $project->delete();
                
                return response()->json([
                    'message' => 'プロジェクトが正常に削除されました',
                ], 200);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'プロジェクトの削除に失敗しました',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
} 