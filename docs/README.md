# nuxt-docker プロジェクト ドキュメント

## 📋 概要
このディレクトリには、nuxt-docker プロジェクトの包括的なドキュメントが含まれています。フルスタックTodoアプリケーションの全ての公開API、関数、コンポーネントの詳細な使用方法とサンプルコードを提供します。

## 📚 ドキュメント一覧

### 🔧 [API ドキュメント](./API_DOCUMENTATION.md)
バックエンド API の完全なリファレンス
- **対象**: Laravel バックエンド API
- **内容**:
  - 全エンドポイントの詳細仕様
  - リクエスト/レスポンス形式
  - バリデーションルール
  - エラーハンドリング
  - 実際のcURL使用例

### 🎨 [フロントエンド ドキュメント](./FRONTEND_DOCUMENTATION.md)
フロントエンド（Nuxt.js）の完全なリファレンス
- **対象**: Nuxt.js フロントエンド
- **内容**:
  - 全コンポーネントの詳細仕様
  - Composables の使用方法
  - 型定義の説明
  - ユーティリティ関数
  - 開発ガイドライン

### 💡 [使用例とサンプルコード](./USAGE_EXAMPLES.md)
実践的な使用例とベストプラクティス
- **対象**: 開発者向け実装ガイド
- **内容**:
  - 基本的な使用フロー
  - API操作の実例
  - フロントエンド実装例
  - カスタムコンポーネント作成
  - テスト例
  - パフォーマンス最適化

---

## 🚀 クイックスタート

### 1. 環境セットアップ
```bash
# リポジトリクローン
git clone https://github.com/se-opiko/nuxt-docker.git
cd nuxt-docker

# 環境変数設定
cp api/.env.example api/.env

# Docker起動
docker compose up -d

# 依存関係インストール
docker compose exec -T api composer install

# データベースセットアップ
docker compose exec api php artisan migrate
docker compose exec api php artisan db:seed
```

### 2. アクセス確認
- **フロントエンド**: http://localhost
- **API**: http://localhost:9000
- **API ドキュメント**: この docs ディレクトリ

---

## 📖 主要機能の使用方法

### API を使用したタスク管理

#### タスク作成
```bash
curl -X POST http://localhost:9000/api/tasks/store \
  -H "Content-Type: application/json" \
  -d '{
    "title": "新しいタスク",
    "description": "タスクの説明",
    "priority": 2,
    "status": 1,
    "project_id": 1
  }'
```

#### タスク一覧取得
```bash
# 全タスク取得
curl http://localhost:9000/api/tasks

# フィルタ付き取得
curl "http://localhost:9000/api/tasks?status=1&project_id=1"
```

### フロントエンドでのタスク管理

#### Composable を使用した基本操作
```vue
<script setup lang="ts">
import { useTasks } from '@/composables/useTasks'

const { tasks, isLoading, fetchTasks, createTask } = useTasks()

// タスク一覧取得
onMounted(async () => {
  await fetchTasks()
})

// 新しいタスク作成
const handleCreateTask = async (taskData) => {
  await createTask(taskData)
}
</script>

<template>
  <div>
    <div v-if="isLoading">読み込み中...</div>
    <task-card v-for="task in tasks" :key="task.id" :task="task" />
  </div>
</template>
```

---

## 🏗️ アーキテクチャ概要

### システム構成
```
┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐
│   Frontend      │    │   Backend API   │    │   Database      │
│   (Nuxt.js)     │◄──►│   (Laravel)     │◄──►│   (MySQL)       │
│   Port: 80      │    │   Port: 9000    │    │   Port: 3306    │
└─────────────────┘    └─────────────────┘    └─────────────────┘
```

### 技術スタック

#### フロントエンド
- **Framework**: Nuxt.js 3
- **UI Library**: Element Plus
- **Language**: TypeScript
- **Styling**: UnoCSS + Sass
- **State Management**: Pinia (Composables)

#### バックエンド
- **Framework**: Laravel 12
- **Language**: PHP 8.2+
- **Database**: MySQL 8.0
- **Authentication**: Laravel Sanctum

---

## 📋 API エンドポイント一覧

### タスク管理
| メソッド | エンドポイント | 説明 |
|---------|---------------|------|
| GET | `/api/tasks` | タスク一覧取得 |
| POST | `/api/tasks/store` | タスク作成 |
| PATCH | `/api/tasks/{id}` | タスク更新 |
| DELETE | `/api/tasks/{id}` | タスク削除 |

### プロジェクト管理
| メソッド | エンドポイント | 説明 |
|---------|---------------|------|
| GET | `/api/projects` | プロジェクト一覧取得 |
| POST | `/api/projects/store` | プロジェクト作成 |
| GET | `/api/projects/{project}` | プロジェクト詳細取得 |
| PATCH | `/api/projects/{id}` | プロジェクト更新 |
| DELETE | `/api/projects/{id}` | プロジェクト削除 |

---

## 🧩 主要コンポーネント

### フロントエンド Composables
- **`useTasks`**: タスク管理のためのComposable
- **`useProjects`**: プロジェクト管理のためのComposable

### Vue コンポーネント
- **`TaskCard`**: タスク表示カード
- **`TaskInputModal`**: タスク作成/編集モーダル
- **`CommonConfirmModal`**: 汎用確認ダイアログ

### ユーティリティ関数
- **`formatDate`**: 日付フォーマット関数
- **タスク関連ユーティリティ**: 優先度、ステータス表示など

---

## 🔍 型定義

### 主要な型

#### Task 型
```typescript
type Task = {
  id: number;
  title: string;
  description: string;
  priority: number; // 1: 低, 2: 中, 3: 高
  status: number;   // 1: 未着手, 2: 進行中, 3: 完了
  project_id: number | null;
  created_at: string;
  updated_at: string;
  project?: Project;
}
```

#### Project 型
```typescript
type Project = {
  id: number;
  name: string;
  description: string | null;
  color: string | null; // #FFFFFF 形式
  created_at: string;
  updated_at: string;
  tasks?: Task[];
}
```

---

## 🧪 テスト

### テスト実行
```bash
# バックエンドテスト
docker compose exec api vendor/bin/phpunit

# フロントエンドテスト
docker compose exec nuxt npm run test

# カバレッジ付きテスト
docker compose exec nuxt npm run test -- --coverage
```

### テスト例
```typescript
// Composable のテスト例
import { useTasks } from '@/composables/useTasks'

describe('useTasks', () => {
  it('should fetch tasks successfully', async () => {
    const { tasks, fetchTasks } = useTasks()
    await fetchTasks()
    expect(tasks.value).toHaveLength(0)
  })
})
```

---

## 🔧 開発ツール

### 便利なコマンド

#### Docker 関連
```bash
# 全サービス起動
docker compose up -d

# ログ確認
docker compose logs -f

# コンテナに入る
docker compose exec nuxt bash
docker compose exec api bash
```

#### Laravel 関連
```bash
# マイグレーション
docker compose exec api php artisan migrate

# シーダー実行
docker compose exec api php artisan db:seed

# コードフォーマット
docker compose exec api vendor/bin/pint
```

#### Nuxt 関連
```bash
# 開発サーバー起動
docker compose exec nuxt npm run dev

# ビルド
docker compose exec nuxt npm run build

# Linting
docker compose exec nuxt npm run lint
```

---

## 📝 コントリビューション

### 開発フロー
1. Issue の作成
2. フィーチャーブランチの作成
3. 実装・テスト
4. Pull Request の作成
5. コードレビュー
6. マージ

### コーディング規約
- **TypeScript**: 型安全性を重視
- **Vue 3 Composition API**: 関数型プログラミングスタイル
- **Laravel**: PSR-12 準拠
- **テスト**: 新機能には必ずテストを追加

---

## 📞 サポート

### 問い合わせ先
- **Issues**: [GitHub Issues](https://github.com/se-opiko/nuxt-docker/issues)
- **メンテナー**: [@se-opiko](https://github.com/se-opiko)

### よくある質問

#### Q: タスクが表示されない
**A**: 以下を確認してください
1. API サーバーが起動しているか
2. データベースが正しく設定されているか
3. ブラウザの開発者ツールでエラーがないか

#### Q: プロジェクトが作成できない
**A**: バリデーションエラーの可能性があります
1. プロジェクト名が255文字以内か
2. カラーコードが正しい形式（#FFFFFF）か
3. API レスポンスのエラーメッセージを確認

#### Q: Docker が起動しない
**A**: 以下を確認してください
1. Docker Desktop が起動しているか
2. ポート80、3306、9000が使用されていないか
3. `.env` ファイルが正しく設定されているか

---

## 📄 ライセンス
MIT License

---

## 📅 更新履歴
- **2024-01-01**: 初回ドキュメント作成
- **2024-01-02**: API ドキュメント追加
- **2024-01-03**: フロントエンドドキュメント追加
- **2024-01-04**: 使用例とサンプルコード追加

---

**最終更新**: 2024年1月4日