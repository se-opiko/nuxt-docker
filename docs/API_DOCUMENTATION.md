# API ドキュメント

## 概要
nuxt-docker プロジェクトのバックエンド API の完全なリファレンスドキュメントです。

## ベース URL
```
http://localhost:9000/api
```

## 認証
現在のバージョンでは認証は実装されていません。

## エラーレスポンス形式
```json
{
  "message": "エラーメッセージ",
  "error": "詳細なエラー情報（開発環境のみ）"
}
```

---

## タスク API

### 1. タスク一覧取得

#### エンドポイント
```
GET /tasks
```

#### 説明
タスクの一覧を取得します。プロジェクト情報も含めて返されます。

#### クエリパラメータ
| パラメータ | 型 | 必須 | 説明 | 例 |
|-----------|---|------|------|-----|
| `status` | integer | × | ステータスでフィルタ | `1` (未着手) |
| `project_id` | integer | × | プロジェクトIDでフィルタ | `1` |

#### ステータス値
- `1`: 未着手
- `2`: 進行中  
- `3`: 完了

#### 優先度値
- `1`: 低
- `2`: 中
- `3`: 高

#### レスポンス例
```json
{
  "message": "タスク一覧の取得に成功しました",
  "tasks": [
    {
      "id": 1,
      "title": "サンプルタスク",
      "description": "タスクの説明",
      "priority": 2,
      "status": 1,
      "project_id": 1,
      "created_at": "2024-01-01T00:00:00.000000Z",
      "updated_at": "2024-01-01T00:00:00.000000Z",
      "project": {
        "id": 1,
        "name": "サンプルプロジェクト",
        "description": "プロジェクトの説明",
        "color": "#409eff",
        "created_at": "2024-01-01T00:00:00.000000Z",
        "updated_at": "2024-01-01T00:00:00.000000Z"
      }
    }
  ]
}
```

#### エラーレスポンス
- `500`: サーバーエラー

---

### 2. タスク作成

#### エンドポイント
```
POST /tasks/store
```

#### 説明
新しいタスクを作成します。

#### リクエストボディ
```json
{
  "title": "タスクタイトル",
  "description": "タスクの説明（オプション）",
  "priority": 2,
  "status": 1,
  "project_id": 1
}
```

#### バリデーションルール
| フィールド | 型 | 必須 | ルール | 説明 |
|-----------|---|------|-------|------|
| `title` | string | ✓ | max:50 | タスクタイトル |
| `description` | string | × | - | タスク説明 |
| `priority` | integer | ✓ | 1-3 | 優先度 |
| `status` | integer | ✓ | 1-3 | ステータス |
| `project_id` | integer | × | exists:projects,id | プロジェクトID |

#### レスポンス例
```json
{
  "message": "タスクが正常に作成されました",
  "task": {
    "id": 2,
    "title": "新しいタスク",
    "description": "タスクの説明",
    "priority": 2,
    "status": 1,
    "project_id": 1,
    "created_at": "2024-01-01T00:00:00.000000Z",
    "updated_at": "2024-01-01T00:00:00.000000Z",
    "project": {
      "id": 1,
      "name": "サンプルプロジェクト",
      "description": "プロジェクトの説明",
      "color": "#409eff",
      "created_at": "2024-01-01T00:00:00.000000Z",
      "updated_at": "2024-01-01T00:00:00.000000Z"
    }
  }
}
```

#### エラーレスポンス
- `422`: バリデーションエラー
- `500`: サーバーエラー

---

### 3. タスク更新

#### エンドポイント
```
PATCH /tasks/{id}
```

#### 説明
指定されたIDのタスクを更新します。

#### パスパラメータ
| パラメータ | 型 | 必須 | 説明 |
|-----------|---|------|------|
| `id` | integer | ✓ | タスクID |

#### リクエストボディ
タスク作成と同じ形式

#### レスポンス例
```json
{
  "message": "タスクが正常に更新されました",
  "task": {
    "id": 1,
    "title": "更新されたタスク",
    "description": "更新された説明",
    "priority": 3,
    "status": 2,
    "project_id": 1,
    "created_at": "2024-01-01T00:00:00.000000Z",
    "updated_at": "2024-01-01T01:00:00.000000Z",
    "project": {
      "id": 1,
      "name": "サンプルプロジェクト",
      "description": "プロジェクトの説明",
      "color": "#409eff",
      "created_at": "2024-01-01T00:00:00.000000Z",
      "updated_at": "2024-01-01T00:00:00.000000Z"
    }
  }
}
```

#### エラーレスポンス
- `404`: タスクが見つからない
- `422`: バリデーションエラー
- `500`: サーバーエラー

---

### 4. タスク削除

#### エンドポイント
```
DELETE /tasks/{id}
```

#### 説明
指定されたIDのタスクを削除します。

#### パスパラメータ
| パラメータ | 型 | 必須 | 説明 |
|-----------|---|------|------|
| `id` | integer | ✓ | タスクID |

#### レスポンス例
```json
{
  "message": "タスクが正常に削除されました"
}
```

#### エラーレスポンス
- `404`: タスクが見つからない
- `500`: サーバーエラー

---

## プロジェクト API

### 1. プロジェクト一覧取得

#### エンドポイント
```
GET /projects
```

#### 説明
プロジェクトの一覧を取得します。関連するタスクも含めて返されます。

#### レスポンス例
```json
{
  "message": "プロジェクト一覧の取得に成功しました",
  "projects": [
    {
      "id": 1,
      "name": "サンプルプロジェクト",
      "description": "プロジェクトの説明",
      "color": "#409eff",
      "created_at": "2024-01-01T00:00:00.000000Z",
      "updated_at": "2024-01-01T00:00:00.000000Z",
      "tasks": [
        {
          "id": 1,
          "title": "タスク1",
          "description": "タスクの説明",
          "priority": 2,
          "status": 1,
          "project_id": 1,
          "created_at": "2024-01-01T00:00:00.000000Z",
          "updated_at": "2024-01-01T00:00:00.000000Z"
        }
      ]
    }
  ]
}
```

#### エラーレスポンス
- `500`: サーバーエラー

---

### 2. プロジェクト作成

#### エンドポイント
```
POST /projects/store
```

#### 説明
新しいプロジェクトを作成します。

#### リクエストボディ
```json
{
  "name": "プロジェクト名",
  "description": "プロジェクトの説明（オプション）",
  "color": "#409eff"
}
```

#### バリデーションルール
| フィールド | 型 | 必須 | ルール | 説明 |
|-----------|---|------|-------|------|
| `name` | string | ✓ | max:255 | プロジェクト名 |
| `description` | string | × | max:1000 | プロジェクト説明 |
| `color` | string | × | regex:/^#[0-9A-Fa-f]{6}$/ | カラーコード |

#### レスポンス例
```json
{
  "message": "プロジェクトが正常に作成されました",
  "project": {
    "id": 2,
    "name": "新しいプロジェクト",
    "description": "プロジェクトの説明",
    "color": "#409eff",
    "created_at": "2024-01-01T00:00:00.000000Z",
    "updated_at": "2024-01-01T00:00:00.000000Z"
  }
}
```

#### エラーレスポンス
- `422`: バリデーションエラー
- `500`: サーバーエラー

---

### 3. プロジェクト詳細取得

#### エンドポイント
```
GET /projects/{project}
```

#### 説明
指定されたIDのプロジェクト詳細を取得します。関連するタスクも含めて返されます。

#### パスパラメータ
| パラメータ | 型 | 必須 | 説明 |
|-----------|---|------|------|
| `project` | integer | ✓ | プロジェクトID |

#### レスポンス例
```json
{
  "message": "プロジェクトの取得に成功しました",
  "project": {
    "id": 1,
    "name": "サンプルプロジェクト",
    "description": "プロジェクトの説明",
    "color": "#409eff",
    "created_at": "2024-01-01T00:00:00.000000Z",
    "updated_at": "2024-01-01T00:00:00.000000Z",
    "tasks": [
      {
        "id": 1,
        "title": "タスク1",
        "description": "タスクの説明",
        "priority": 2,
        "status": 1,
        "project_id": 1,
        "created_at": "2024-01-01T00:00:00.000000Z",
        "updated_at": "2024-01-01T00:00:00.000000Z"
      }
    ]
  }
}
```

#### エラーレスポンス
- `404`: プロジェクトが見つからない
- `500`: サーバーエラー

---

### 4. プロジェクト更新

#### エンドポイント
```
PATCH /projects/{id}
```

#### 説明
指定されたIDのプロジェクトを更新します。

#### パスパラメータ
| パラメータ | 型 | 必須 | 説明 |
|-----------|---|------|------|
| `id` | integer | ✓ | プロジェクトID |

#### リクエストボディ
プロジェクト作成と同じ形式

#### レスポンス例
```json
{
  "message": "プロジェクトが正常に更新されました",
  "project": {
    "id": 1,
    "name": "更新されたプロジェクト",
    "description": "更新された説明",
    "color": "#67c23a",
    "created_at": "2024-01-01T00:00:00.000000Z",
    "updated_at": "2024-01-01T01:00:00.000000Z"
  }
}
```

#### エラーレスポンス
- `404`: プロジェクトが見つからない
- `422`: バリデーションエラー
- `500`: サーバーエラー

---

### 5. プロジェクト削除

#### エンドポイント
```
DELETE /projects/{id}
```

#### 説明
指定されたIDのプロジェクトを削除します。関連するタスクがある場合は、それらのタスクは未分類（project_id = null）に移動されます。

#### パスパラメータ
| パラメータ | 型 | 必須 | 説明 |
|-----------|---|------|------|
| `id` | integer | ✓ | プロジェクトID |

#### レスポンス例（関連タスクがない場合）
```json
{
  "message": "プロジェクトが正常に削除されました"
}
```

#### レスポンス例（関連タスクがある場合）
```json
{
  "message": "プロジェクトが正常に削除されました。3件のタスクが未分類に移動されました。",
  "warning": "削除されたプロジェクトに関連していた3件のタスクは未分類になりました。",
  "affected_tasks_count": 3
}
```

#### エラーレスポンス
- `404`: プロジェクトが見つからない
- `500`: サーバーエラー

---

## モデル構造

### Task モデル
```php
class Task extends Model
{
    protected $fillable = [
        'title',
        'description', 
        'priority',
        'status',
        'project_id',
    ];

    // リレーション
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}
```

### Project モデル
```php
class Project extends Model
{
    protected $fillable = [
        'name',
        'description',
        'color',
    ];

    // リレーション
    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }
}
```

---

## 使用例

### タスクの作成から削除まで
```bash
# 1. プロジェクト作成
curl -X POST http://localhost:9000/api/projects/store \
  -H "Content-Type: application/json" \
  -d '{
    "name": "新規プロジェクト",
    "description": "サンプルプロジェクト",
    "color": "#409eff"
  }'

# 2. タスク作成
curl -X POST http://localhost:9000/api/tasks/store \
  -H "Content-Type: application/json" \
  -d '{
    "title": "新しいタスク",
    "description": "タスクの説明",
    "priority": 2,
    "status": 1,
    "project_id": 1
  }'

# 3. タスク一覧取得
curl http://localhost:9000/api/tasks

# 4. タスク更新
curl -X PATCH http://localhost:9000/api/tasks/1 \
  -H "Content-Type: application/json" \
  -d '{
    "title": "更新されたタスク",
    "description": "更新された説明",
    "priority": 3,
    "status": 2,
    "project_id": 1
  }'

# 5. タスク削除
curl -X DELETE http://localhost:9000/api/tasks/1
```

---

## 開発者向け情報

### バリデーションエラーの形式
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "title": [
      "タイトルは必須です。"
    ],
    "project_id": [
      "指定されたプロジェクトが存在しません。"
    ]
  }
}
```

### レート制限
現在、レート制限は実装されていません。

### CORS設定
開発環境では全てのオリジンからのアクセスが許可されています。