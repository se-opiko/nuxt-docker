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
     * @param int $id プロジェクトID
     * @return JsonResponse 更新されたプロジェクトのJSONレスポンス
     */
    public function update(ProjectStoreRequest $request, int $id): JsonResponse
    {
        try {
            $project = Project::findOrFail($id);
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
     * @param int $id プロジェクトID
     * @return JsonResponse 削除結果のJSONレスポンス
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $project = Project::findOrFail($id);
            
            // 関連するタスクのproject_idをnullに設定
            $project->tasks()->update(['project_id' => null]);
            
            $project->delete();
            
            return response()->json([
                'message' => 'プロジェクトが正常に削除されました',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'プロジェクトの削除に失敗しました',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
} 