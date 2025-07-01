# フロントエンド ドキュメント

## 概要
nuxt-docker プロジェクトのフロントエンド（Nuxt.js）の完全なリファレンスドキュメントです。

## 技術スタック
- **Framework**: Nuxt.js 3
- **UI Library**: Element Plus
- **Language**: TypeScript
- **Styling**: UnoCSS + Sass
- **State Management**: Pinia (Composables)
- **Testing**: Vitest + Vue Test Utils

---

## 型定義

### `@/types/todo.ts`

#### Project 型
```typescript
export type Project = {
  id: number;
  name: string;
  description: string | null;
  color: string | null;
  created_at: string;
  updated_at: string;
  tasks?: Task[];
}
```

#### Task 型
```typescript
export type Task = {
  id: number;
  title: string;
  description: string;
  priority: number;
  status: number;
  project_id: number | null;
  updated_at: string;
  created_at: string;
  project?: Project;
}
```

#### RuleForm 型（タスクフォーム用）
```typescript
export type RuleForm = {
  title: string;
  description: string;
  priority: number;
  status: number;
  project_id?: number | undefined;
}
```

#### ProjectForm 型（プロジェクトフォーム用）
```typescript
export type ProjectForm = {
  name: string;
  description: string;
  color: string | null;
}
```

#### API レスポンス型
```typescript
export type ApiResponse = {
  message: string;
  tasks: Task[];
}

export type ProjectApiResponse = {
  message: string;
  projects: Project[];
}
```

---

## Composables

### `useTasks`

タスク管理のためのComposableです。

#### インポート
```typescript
import { useTasks } from '@/composables/useTasks'
```

#### 使用方法
```typescript
const { 
  tasks, 
  isLoading, 
  searchParams, 
  fetchTasks, 
  createTask, 
  updateTask, 
  deleteTask 
} = useTasks()
```

#### リアクティブな状態

##### `tasks`
- **型**: `Ref<Task[]>`
- **説明**: タスクの配列
- **初期値**: `[]`

##### `isLoading`
- **型**: `Ref<boolean>`
- **説明**: API通信中かどうか
- **初期値**: `false`

##### `searchParams`
- **型**: `Ref<SearchParams>`
- **説明**: 検索パラメータ
- **初期値**: `{}`

```typescript
type SearchParams = {
  status?: number
  project_id?: number | null
}
```

#### メソッド

##### `fetchTasks()`
タスク一覧を取得します。

```typescript
await fetchTasks()
```

- **戻り値**: `Promise<void>`
- **エラーハンドリング**: 失敗時にElMessage.errorでユーザーに通知

##### `createTask(taskData: any)`
新しいタスクを作成します。

```typescript
const taskData = {
  title: "新しいタスク",
  description: "説明",
  priority: 2,
  status: 1,
  project_id: 1
}
await createTask(taskData)
```

- **引数**: `taskData` - 作成するタスクのデータ
- **戻り値**: `Promise<void>`
- **副作用**: 作成後に自動的に`fetchTasks()`を実行

##### `updateTask(id: number, taskData: any)`
既存のタスクを更新します。

```typescript
await updateTask(1, {
  title: "更新されたタスク",
  priority: 3,
  status: 2
})
```

- **引数**: 
  - `id` - タスクID
  - `taskData` - 更新するデータ
- **戻り値**: `Promise<void>`
- **副作用**: 更新後に自動的に`fetchTasks()`を実行

##### `deleteTask(id: number)`
タスクを削除します。

```typescript
await deleteTask(1)
```

- **引数**: `id` - 削除するタスクID
- **戻り値**: `Promise<void>`
- **副作用**: 削除後に自動的に`fetchTasks()`を実行

#### 使用例
```vue
<script setup lang="ts">
const { tasks, isLoading, fetchTasks, createTask } = useTasks()

// ページ読み込み時にタスクを取得
onMounted(async () => {
  await fetchTasks()
})

// 新しいタスクを作成
const handleCreateTask = async (taskData: RuleForm) => {
  try {
    await createTask(taskData)
    // 成功時の処理
  } catch (error) {
    // エラー処理
  }
}
</script>

<template>
  <div>
    <div v-if="isLoading">読み込み中...</div>
    <div v-for="task in tasks" :key="task.id">
      {{ task.title }}
    </div>
  </div>
</template>
```

---

### `useProjects`

プロジェクト管理のためのComposableです。

#### インポート
```typescript
import { useProjects } from '@/composables/useProjects'
```

#### 使用方法
```typescript
const { 
  projects, 
  isLoading, 
  fetchProjects, 
  createProject, 
  updateProject, 
  deleteProject,
  clearProjectsCache,
  getProjectById
} = useProjects()
```

#### リアクティブな状態

##### `projects`
- **型**: `Ref<Project[]>`
- **説明**: プロジェクトの配列
- **初期値**: `[]`

##### `isLoading`
- **型**: `Ref<boolean>`
- **説明**: API通信中かどうか
- **初期値**: `false`

#### メソッド

##### `fetchProjects(forceRefresh?: boolean)`
プロジェクト一覧を取得します。

```typescript
// 初回取得または強制リフレッシュ
await fetchProjects(true)

// キャッシュがあれば使用
await fetchProjects()
```

- **引数**: `forceRefresh` - 強制的に再取得するか（デフォルト: false）
- **戻り値**: `Promise<Project[]>`
- **特徴**: 一度取得したデータはキャッシュされ、重複リクエストを防ぐ

##### `createProject(projectData: ProjectForm)`
新しいプロジェクトを作成します。

```typescript
const projectData = {
  name: "新しいプロジェクト",
  description: "説明",
  color: "#409eff"
}
await createProject(projectData)
```

- **引数**: `projectData` - 作成するプロジェクトのデータ
- **戻り値**: `Promise<void>`
- **副作用**: 作成後に自動的に`fetchProjects(true)`を実行

##### `updateProject(id: number, projectData: ProjectForm)`
既存のプロジェクトを更新します。

```typescript
await updateProject(1, {
  name: "更新されたプロジェクト",
  description: "新しい説明",
  color: "#67c23a"
})
```

- **引数**: 
  - `id` - プロジェクトID
  - `projectData` - 更新するデータ
- **戻り値**: `Promise<void>`
- **副作用**: 更新後に自動的に`fetchProjects(true)`を実行

##### `deleteProject(id: number, projectName: string)`
プロジェクトを削除します（確認ダイアログ付き）。

```typescript
await deleteProject(1, "プロジェクト名")
```

- **引数**: 
  - `id` - 削除するプロジェクトID
  - `projectName` - 確認ダイアログに表示するプロジェクト名
- **戻り値**: `Promise<void>`
- **特徴**: 削除前に確認ダイアログを表示
- **副作用**: 削除後に自動的に`fetchProjects(true)`を実行

##### `getProjectById(id: number)`
キャッシュからプロジェクトを検索します。

```typescript
const project = getProjectById(1)
```

- **引数**: `id` - プロジェクトID
- **戻り値**: `Project | undefined`

##### `clearProjectsCache()`
プロジェクトキャッシュをクリアします（開発・テスト用）。

```typescript
clearProjectsCache()
```

#### 使用例
```vue
<script setup lang="ts">
const { projects, isLoading, fetchProjects, deleteProject } = useProjects()

// ページ読み込み時にプロジェクトを取得
onMounted(async () => {
  await fetchProjects()
})

// プロジェクト削除
const handleDeleteProject = async (project: Project) => {
  try {
    await deleteProject(project.id, project.name)
    // 成功時の処理
  } catch (error) {
    // エラー処理（キャンセル含む）
  }
}
</script>

<template>
  <div>
    <div v-if="isLoading">読み込み中...</div>
    <div v-for="project in projects" :key="project.id">
      <h3>{{ project.name }}</h3>
      <button @click="handleDeleteProject(project)">削除</button>
    </div>
  </div>
</template>
```

---

## コンポーネント

### `TaskCard`

タスクの情報を表示するカードコンポーネントです。

#### ファイル
`@/components/task-card.vue`

#### Props

##### `task`
- **型**: `Task`
- **必須**: ✓
- **説明**: 表示するタスクのデータ

#### 使用方法
```vue
<template>
  <task-card :task="task" />
</template>

<script setup lang="ts">
import type { Task } from '@/types/todo'

const task: Task = {
  id: 1,
  title: "サンプルタスク",
  description: "タスクの説明",
  priority: 2,
  status: 1,
  project_id: 1,
  created_at: "2024-01-01T00:00:00.000000Z",
  updated_at: "2024-01-01T00:00:00.000000Z",
  project: {
    id: 1,
    name: "サンプルプロジェクト",
    color: "#409eff"
  }
}
</script>
```

#### 機能
- タスクの詳細情報表示
- 編集ボタン（編集モーダルを開く）
- 削除ボタン（確認モーダルを開く）
- プロジェクトタグ表示（カラー対応）
- 優先度表示（色分け）
- 更新日時表示

#### 内部で使用するコンポーネント
- `TaskInputModal` - タスク編集用
- `CommonConfirmModal` - 削除確認用

#### スタイル特徴
- Element Plus の `el-card` を使用
- レスポンシブデザイン
- ホバーエフェクト
- プロジェクトカラーに応じた文字色自動調整

---

### `TaskInputModal`

タスクの作成・編集を行うモーダルコンポーネントです。

#### ファイル
`@/components/task-input-modal.vue`

#### Props

##### `modelValue`
- **型**: `boolean`
- **必須**: ✓
- **説明**: モーダルの表示状態

##### `title`
- **型**: `string`
- **デフォルト**: `"タスクを保存する"`
- **説明**: モーダルのタイトル

##### `saveButtonText`
- **型**: `string`
- **デフォルト**: `"保存"`
- **説明**: 保存ボタンのテキスト

##### `task`
- **型**: `Task`
- **デフォルト**: 空のタスクオブジェクト
- **説明**: 編集対象のタスク（新規作成時は省略可能）

##### `onSave`
- **型**: `Function`
- **必須**: ✓
- **説明**: 保存時に実行される関数

#### Events

##### `update:modelValue`
- **引数**: `boolean`
- **説明**: モーダルの表示状態が変更された時

##### `save`
- **引数**: `RuleForm`
- **説明**: 保存ボタンがクリックされた時

#### 使用方法

##### 新規作成
```vue
<template>
  <task-input-modal 
    v-model="isCreateModalVisible"
    title="新しいタスクを作成"
    save-button-text="作成"
    :on-save="handleCreateTask"
  />
</template>

<script setup lang="ts">
const isCreateModalVisible = ref(false)

const handleCreateTask = async (taskData: RuleForm) => {
  // タスク作成処理
  await createTask(taskData)
}
</script>
```

##### 編集
```vue
<template>
  <task-input-modal 
    v-model="isEditModalVisible"
    title="タスクを編集"
    save-button-text="更新"
    :task="selectedTask"
    :on-save="handleUpdateTask"
  />
</template>

<script setup lang="ts">
const isEditModalVisible = ref(false)
const selectedTask = ref<Task>()

const handleUpdateTask = async (taskData: RuleForm) => {
  if (selectedTask.value) {
    await updateTask(selectedTask.value.id, taskData)
  }
}
</script>
```

#### フォームフィールド
- **タイトル**: 必須、最大50文字
- **説明**: オプション、最大1000文字
- **プロジェクト**: オプション、ドロップダウン選択
- **優先度**: 必須、1-3の選択
- **ステータス**: 必須、1-3の選択

#### バリデーション
- Element Plus のフォームバリデーション機能を使用
- リアルタイムバリデーション対応
- カスタムエラーメッセージ

#### 特徴
- プロジェクト一覧の遅延読み込み（モーダル表示時のみ）
- 閉じる時の確認ダイアログ
- フォーム状態のリセット機能

---

### `CommonConfirmModal`

汎用的な確認ダイアログコンポーネントです。

#### ファイル
`@/components/common/confirm-modal.vue`

#### Props

##### `modelValue`
- **型**: `boolean`
- **必須**: ✓
- **説明**: モーダルの表示状態

##### `title`
- **型**: `string`
- **必須**: ✓
- **説明**: モーダルのタイトル

##### `message`
- **型**: `string`
- **必須**: ✓
- **説明**: 確認メッセージ

##### `confirmButtonText`
- **型**: `string`
- **デフォルト**: `"OK"`
- **説明**: 確認ボタンのテキスト

##### `cancelButtonText`
- **型**: `string`
- **デフォルト**: `"キャンセル"`
- **説明**: キャンセルボタンのテキスト

#### Events

##### `update:modelValue`
- **引数**: `boolean`
- **説明**: モーダルの表示状態が変更された時

##### `confirm`
- **説明**: 確認ボタンがクリックされた時

##### `cancel`
- **説明**: キャンセルボタンがクリックされた時

#### 使用方法
```vue
<template>
  <common-confirm-modal
    v-model="isDeleteConfirmVisible"
    title="削除確認"
    message="このタスクを削除しますか？この操作は元に戻せません。"
    confirm-button-text="削除する"
    cancel-button-text="キャンセル"
    @confirm="handleDeleteConfirm"
    @cancel="handleDeleteCancel"
  />
</template>

<script setup lang="ts">
const isDeleteConfirmVisible = ref(false)

const handleDeleteConfirm = () => {
  // 削除処理
  console.log('削除が確認されました')
}

const handleDeleteCancel = () => {
  // キャンセル処理
  console.log('削除がキャンセルされました')
}
</script>
```

#### 特徴
- シンプルで再利用可能
- アクセシビリティ対応
- レスポンシブデザイン

---

## ユーティリティ関数

### `formatDate`

日付フォーマット用のユーティリティ関数です。

#### ファイル
`@/utils/date.ts`

#### 型定義
```typescript
export type SupportedDateFormat = 
  'YYYY/MM/DD' |
  'YYYY年M月D日(曜)'|
  'M/D(曜)' |
  'YYYY/MM/DD HH:mm:ss';
```

#### 関数シグネチャ
```typescript
function formatDate(date: Date, format: SupportedDateFormat): string
```

#### 使用方法
```typescript
import { formatDate } from '@/utils/date'

const date = new Date('2024-04-08T09:05:45')

// 基本的な日付フォーマット
formatDate(date, 'YYYY/MM/DD')           // "2024/04/08"
formatDate(date, 'YYYY年M月D日(曜)')        // "2024年4月8日(月)"
formatDate(date, 'M/D(曜)')               // "4/8(月)"
formatDate(date, 'YYYY/MM/DD HH:mm:ss')  // "2024/04/08 09:05:45"
```

#### サポートされるフォーマット

##### `'YYYY/MM/DD'`
- **出力例**: `2024/04/08`
- **用途**: 一般的な日付表示

##### `'YYYY年M月D日(曜)'`
- **出力例**: `2024年4月8日(月)`
- **用途**: 日本語での詳細な日付表示

##### `'M/D(曜)'`
- **出力例**: `4/8(月)`
- **用途**: 簡略化された日付表示

##### `'YYYY/MM/DD HH:mm:ss'`
- **出力例**: `2024/04/08 09:05:45`
- **用途**: 日時の詳細表示

#### エラーハンドリング
```typescript
try {
  const formatted = formatDate(date, 'YYYY/MM/DD')
} catch (error) {
  console.error('Unsupported format:', error.message)
}
```

#### 特徴
- TypeScript の型安全性
- 日本語ロケール対応
- 軽量で高速
- エラーハンドリング

---

## ページコンポーネント

### `pages/index.vue`

アプリケーションのホームページです。

#### 機能
- アプリケーションの概要表示
- 主要機能へのナビゲーション

### `pages/todo/index.vue`

Todoアプリケーションのメインページです。

#### 機能
- タスク一覧の表示
- タスクの作成・編集・削除
- プロジェクトによるフィルタリング
- ステータスによるフィルタリング

#### 使用しているコンポーネント
- `TaskCard`
- `TaskInputModal`
- `CommonConfirmModal`

#### 使用しているComposables
- `useTasks`
- `useProjects`

---

## 開発ガイドライン

### コンポーネント設計原則

1. **単一責任の原則**: 各コンポーネントは一つの明確な責任を持つ
2. **再利用性**: 汎用的で再利用可能なコンポーネントを作成
3. **型安全性**: TypeScript を活用した型安全なコンポーネント
4. **アクセシビリティ**: WAI-ARIA ガイドラインに準拠

### Composables設計原則

1. **状態管理の分離**: ロジックとUIの分離
2. **キャッシュ戦略**: 適切なデータキャッシュ
3. **エラーハンドリング**: 統一されたエラー処理
4. **リアクティビティ**: Vue 3 の Composition API を活用

### 命名規則

#### コンポーネント
- PascalCase を使用
- 機能を明確に表す名前
- 例: `TaskCard`, `TaskInputModal`

#### Composables
- `use` プレフィックス
- camelCase を使用
- 例: `useTasks`, `useProjects`

#### 型定義
- PascalCase を使用
- 明確で説明的な名前
- 例: `Task`, `Project`, `RuleForm`

### ディレクトリ構造
```
front/
├── components/          # Vue コンポーネント
│   ├── common/         # 共通コンポーネント
│   └── ...             # 機能別コンポーネント
├── composables/        # Composition API ロジック
├── pages/              # ページコンポーネント
├── types/              # TypeScript 型定義
├── utils/              # ユーティリティ関数
└── assets/             # 静的アセット
```

### テスト戦略

#### 単体テスト
- Vitest を使用
- コンポーネントの動作テスト
- Composables のロジックテスト

#### E2Eテスト
- 主要なユーザーフローのテスト
- ブラウザ間の互換性テスト

### パフォーマンス最適化

1. **遅延読み込み**: 必要な時のみデータを取得
2. **キャッシュ戦略**: 重複リクエストの防止
3. **仮想スクロール**: 大量データの効率的な表示
4. **コンポーネント分割**: 適切な粒度でのコンポーネント分割

---

## トラブルシューティング

### よくある問題

#### 1. タスクが表示されない
- API サーバーが起動しているか確認
- ネットワークタブでAPIレスポンスを確認
- `useTasks` の `fetchTasks()` が呼ばれているか確認

#### 2. プロジェクトが選択できない
- `useProjects` の `fetchProjects()` が呼ばれているか確認
- プロジェクトデータが正しく取得されているか確認

#### 3. フォームバリデーションが動作しない
- Element Plus のフォームコンポーネントが正しく設定されているか確認
- バリデーションルールが正しく定義されているか確認

### デバッグ方法

#### 1. Vue DevTools
- コンポーネントの状態確認
- Pinia ストアの状態確認

#### 2. ブラウザ開発者ツール
- ネットワークタブでAPI通信確認
- コンソールでエラーログ確認

#### 3. ログ出力
```typescript
// Composables でのデバッグログ
console.log('タスク一覧を取得中...', { params: searchParams.value })
console.log('API レスポンス:', { data: data.value, error: error.value })
```