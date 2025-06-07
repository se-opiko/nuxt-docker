<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Task Store Request
 * 
 * タスクの作成・更新時のバリデーションを定義する
 */
class TaskStoreRequest extends FormRequest
{
    /**
     * リクエストが認証されているかどうかを判定する
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * リクエストに適用するバリデーションルールを取得する
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:50',
            'description' => 'nullable|string',
            'priority' => 'required|integer',
            'status' => 'required|integer',
            'project_id' => 'nullable|integer|exists:projects,id',
        ];
    }

    /**
     * バリデーションエラーメッセージをカスタマイズする
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'title.required' => 'タイトルは必須です。',
            'title.max' =>'タイトルは50文字以内で入力してください。',
            'description.max' => '説明は1000文字以内で入力してください。',
            'project_id.exists' => '指定されたプロジェクトが存在しません。',
        ];
    }
}
