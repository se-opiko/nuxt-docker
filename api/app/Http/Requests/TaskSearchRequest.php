<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Task Search Request
 * 
 * タスク検索時のバリデーションを定義する
 */
class TaskSearchRequest extends FormRequest
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
            'title' => 'nullable|string|max:50',
            'status' => 'nullable|integer',
            'priority' => 'nullable|integer',
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
            'project_id.exists' => '指定されたプロジェクトが存在しません。',
        ];
    }
} 