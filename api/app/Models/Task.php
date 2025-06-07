<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Task モデルクラス
 * 
 * タスクの管理を行うモデル
 * 
 * @property int $id タスクID
 * @property string $title タスクタイトル
 * @property string|null $description タスク説明
 * @property int $priority 優先度
 * @property int $status ステータス
 * @property int|null $project_id プロジェクトID
 * @property \Carbon\Carbon $created_at 作成日時
 * @property \Carbon\Carbon $updated_at 更新日時
 */
class Task extends Model
{
    /**
     * 一括代入で更新可能な属性を指定する
     *
     * @var list<string>
     */
    protected $fillable = [
        'title',
        'description',
        'priority',
        'status',
        'project_id',
    ];

    /**
     * タスクが属するプロジェクトの関係性を定義
     *
     * @return BelongsTo
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}
