<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Project モデルクラス
 * 
 * プロジェクトの管理を行うモデル
 * タスクをグループ分けするために使用される
 * 
 * @property int $id プロジェクトID
 * @property string $name プロジェクト名
 * @property string|null $description プロジェクト説明
 * @property string|null $color カラーコード (#FFFFFF形式)
 * @property \Carbon\Carbon $created_at 作成日時
 * @property \Carbon\Carbon $updated_at 更新日時
 */
class Project extends Model
{
    /**
     * 一括代入で更新可能な属性を指定する
     *
     * @var array<string>
     */
    protected $fillable = [
        'name',
        'description',
        'color',
    ];

    /**
     * プロジェクトに関連するタスクの関係性を定義
     *
     * @return HasMany
     */
    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }
} 