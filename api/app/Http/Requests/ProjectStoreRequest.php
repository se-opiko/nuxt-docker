<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Project Store Request
 * 
 * プロジェクトの作成・更新時のバリデーションを定義する
 */
class ProjectStoreRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
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
            'name.required' => 'プロジェクト名は必須です。',
            'name.max' => 'プロジェクト名は255文字以内で入力してください。',
            'description.max' => '説明は1000文字以内で入力してください。',
            'color.regex' => 'カラーコードは#FFFFFFの形式で入力してください。',
        ];
    }
} 