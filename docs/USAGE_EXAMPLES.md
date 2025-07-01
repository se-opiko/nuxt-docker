# 使用例とサンプルコード

## 概要
nuxt-docker プロジェクトの実践的な使用例とサンプルコードを示すドキュメントです。

---

## 基本的な使用フロー

### 1. プロジェクトの開始

```bash
# プロジェクトのクローン
git clone https://github.com/se-opiko/nuxt-docker.git
cd nuxt-docker

# 環境設定
cp api/.env.example api/.env

# Docker環境の起動
docker compose up -d

# 依存関係のインストール
docker compose exec -T api composer install

# データベースのセットアップ
docker compose exec api php artisan migrate
docker compose exec api php artisan db:seed

# アプリケーションにアクセス
# フロントエンド: http://localhost
# API: http://localhost:9000
```

---

## API使用例

### cURLを使用したAPI操作

#### 1. プロジェクトの作成から削除まで

```bash
# プロジェクト作成
curl -X POST http://localhost:9000/api/projects/store \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Webアプリ開発",
    "description": "新しいWebアプリケーションの開発プロジェクト",
    "color": "#409eff"
  }'

# レスポンス例
{
  "message": "プロジェクトが正常に作成されました",
  "project": {
    "id": 1,
    "name": "Webアプリ開発",
    "description": "新しいWebアプリケーションの開発プロジェクト",
    "color": "#409eff",
    "created_at": "2024-01-01T00:00:00.000000Z",
    "updated_at": "2024-01-01T00:00:00.000000Z"
  }
}

# プロジェクト一覧取得
curl http://localhost:9000/api/projects

# プロジェクト更新
curl -X PATCH http://localhost:9000/api/projects/1 \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Webアプリ開発（更新版）",
    "description": "機能追加されたWebアプリケーション",
    "color": "#67c23a"
  }'

# プロジェクト削除
curl -X DELETE http://localhost:9000/api/projects/1
```

#### 2. タスクの作成から削除まで

```bash
# タスク作成
curl -X POST http://localhost:9000/api/tasks/store \
  -H "Content-Type: application/json" \
  -d '{
    "title": "ユーザー認証機能の実装",
    "description": "ログイン・ログアウト・ユーザー登録機能を実装する",
    "priority": 3,
    "status": 1,
    "project_id": 1
  }'

# レスポンス例
{
  "message": "タスクが正常に作成されました",
  "task": {
    "id": 1,
    "title": "ユーザー認証機能の実装",
    "description": "ログイン・ログアウト・ユーザー登録機能を実装する",
    "priority": 3,
    "status": 1,
    "project_id": 1,
    "created_at": "2024-01-01T00:00:00.000000Z",
    "updated_at": "2024-01-01T00:00:00.000000Z",
    "project": {
      "id": 1,
      "name": "Webアプリ開発",
      "description": "新しいWebアプリケーションの開発プロジェクト",
      "color": "#409eff",
      "created_at": "2024-01-01T00:00:00.000000Z",
      "updated_at": "2024-01-01T00:00:00.000000Z"
    }
  }
}

# タスク一覧取得（フィルタなし）
curl http://localhost:9000/api/tasks

# タスク一覧取得（ステータスフィルタ）
curl "http://localhost:9000/api/tasks?status=1"

# タスク一覧取得（プロジェクトフィルタ）
curl "http://localhost:9000/api/tasks?project_id=1"

# タスク一覧取得（複数フィルタ）
curl "http://localhost:9000/api/tasks?status=2&project_id=1"

# タスク更新（ステータス変更）
curl -X PATCH http://localhost:9000/api/tasks/1 \
  -H "Content-Type: application/json" \
  -d '{
    "title": "ユーザー認証機能の実装",
    "description": "ログイン・ログアウト・ユーザー登録機能を実装する（進行中）",
    "priority": 3,
    "status": 2,
    "project_id": 1
  }'

# タスク削除
curl -X DELETE http://localhost:9000/api/tasks/1
```

### JavaScriptでのAPI操作

#### Fetch APIを使用した例

```javascript
// プロジェクト作成
async function createProject(projectData) {
  try {
    const response = await fetch('http://localhost:9000/api/projects/store', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify(projectData)
    });
    
    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`);
    }
    
    const data = await response.json();
    console.log('プロジェクト作成成功:', data);
    return data;
  } catch (error) {
    console.error('プロジェクト作成エラー:', error);
    throw error;
  }
}

// 使用例
const newProject = {
  name: "モバイルアプリ開発",
  description: "React Nativeを使用したモバイルアプリケーション",
  color: "#f56c6c"
};

createProject(newProject);

// タスク一覧取得（検索パラメータ付き）
async function fetchTasks(filters = {}) {
  try {
    const params = new URLSearchParams();
    
    if (filters.status) {
      params.append('status', filters.status);
    }
    
    if (filters.project_id) {
      params.append('project_id', filters.project_id);
    }
    
    const url = `http://localhost:9000/api/tasks${params.toString() ? '?' + params.toString() : ''}`;
    
    const response = await fetch(url);
    
    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`);
    }
    
    const data = await response.json();
    console.log('タスク取得成功:', data);
    return data.tasks;
  } catch (error) {
    console.error('タスク取得エラー:', error);
    throw error;
  }
}

// 使用例
fetchTasks({ status: 1, project_id: 1 }); // 未着手のタスクをプロジェクト1から取得
fetchTasks({ status: 2 }); // 進行中のタスクを全て取得
fetchTasks(); // 全てのタスクを取得
```

---

## フロントエンド使用例

### 基本的なページコンポーネント

#### タスク管理ページの実装例

```vue
<template>
  <div class="task-management">
    <div class="header">
      <h1>タスク管理</h1>
      <el-button type="primary" @click="openCreateModal">
        新しいタスクを作成
      </el-button>
    </div>

    <!-- フィルタリング -->
    <div class="filters">
      <el-select 
        v-model="selectedStatus" 
        placeholder="ステータスで絞り込み"
        clearable
        @change="handleStatusFilter"
      >
        <el-option label="未着手" :value="1" />
        <el-option label="進行中" :value="2" />
        <el-option label="完了" :value="3" />
      </el-select>

      <el-select 
        v-model="selectedProject" 
        placeholder="プロジェクトで絞り込み"
        clearable
        @change="handleProjectFilter"
      >
        <el-option 
          v-for="project in projects" 
          :key="project.id"
          :label="project.name" 
          :value="project.id" 
        />
      </el-select>
    </div>

    <!-- ローディング表示 -->
    <div v-if="isTasksLoading" class="loading">
      <el-icon class="is-loading"><Loading /></el-icon>
      タスクを読み込み中...
    </div>

    <!-- タスク一覧 -->
    <div v-else class="task-list">
      <div v-if="tasks.length === 0" class="empty-state">
        <p>タスクがありません</p>
      </div>
      <div v-else class="task-grid">
        <task-card 
          v-for="task in tasks" 
          :key="task.id" 
          :task="task" 
        />
      </div>
    </div>

    <!-- タスク作成モーダル -->
    <task-input-modal 
      v-model="isCreateModalVisible"
      title="新しいタスクを作成"
      save-button-text="作成"
      :on-save="handleCreateTask"
    />
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'
import { Loading } from '@element-plus/icons-vue'
import { useTasks } from '@/composables/useTasks'
import { useProjects } from '@/composables/useProjects'
import type { RuleForm } from '@/types/todo'

// Composables
const { 
  tasks, 
  isLoading: isTasksLoading, 
  searchParams,
  fetchTasks, 
  createTask 
} = useTasks()

const { 
  projects, 
  isLoading: isProjectsLoading, 
  fetchProjects 
} = useProjects()

// リアクティブな状態
const isCreateModalVisible = ref(false)
const selectedStatus = ref<number>()
const selectedProject = ref<number>()

// 初期化
onMounted(async () => {
  await Promise.all([
    fetchTasks(),
    fetchProjects()
  ])
})

// イベントハンドラー
const openCreateModal = () => {
  isCreateModalVisible.value = true
}

const handleCreateTask = async (taskData: RuleForm) => {
  try {
    await createTask(taskData)
    isCreateModalVisible.value = false
  } catch (error) {
    console.error('タスク作成エラー:', error)
  }
}

const handleStatusFilter = (status: number) => {
  searchParams.value.status = status
  fetchTasks()
}

const handleProjectFilter = (projectId: number) => {
  searchParams.value.project_id = projectId
  fetchTasks()
}
</script>

<style scoped>
.task-management {
  padding: 20px;
}

.header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
}

.filters {
  display: flex;
  gap: 10px;
  margin-bottom: 20px;
}

.loading {
  text-align: center;
  padding: 40px;
  color: #666;
}

.empty-state {
  text-align: center;
  padding: 40px;
  color: #999;
}

.task-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap: 20px;
}
</style>
```

### カスタムComposableの作成例

#### タスク統計用Composable

```typescript
// composables/useTaskStats.ts
import { computed } from 'vue'
import { useTasks } from '@/composables/useTasks'
import type { Task } from '@/types/todo'

export const useTaskStats = () => {
  const { tasks } = useTasks()

  // ステータス別の統計
  const statusStats = computed(() => {
    const stats = {
      todo: 0,      // 未着手
      inProgress: 0, // 進行中
      completed: 0   // 完了
    }

    tasks.value.forEach(task => {
      switch (task.status) {
        case 1:
          stats.todo++
          break
        case 2:
          stats.inProgress++
          break
        case 3:
          stats.completed++
          break
      }
    })

    return stats
  })

  // 優先度別の統計
  const priorityStats = computed(() => {
    const stats = {
      low: 0,    // 低
      medium: 0, // 中
      high: 0    // 高
    }

    tasks.value.forEach(task => {
      switch (task.priority) {
        case 1:
          stats.low++
          break
        case 2:
          stats.medium++
          break
        case 3:
          stats.high++
          break
      }
    })

    return stats
  })

  // プロジェクト別の統計
  const projectStats = computed(() => {
    const stats: Record<string, number> = {}

    tasks.value.forEach(task => {
      const projectName = task.project?.name || '未分類'
      stats[projectName] = (stats[projectName] || 0) + 1
    })

    return stats
  })

  // 完了率の計算
  const completionRate = computed(() => {
    const total = tasks.value.length
    if (total === 0) return 0
    
    const completed = statusStats.value.completed
    return Math.round((completed / total) * 100)
  })

  // 今日作成されたタスクの数
  const todayTasksCount = computed(() => {
    const today = new Date().toDateString()
    return tasks.value.filter(task => {
      const taskDate = new Date(task.created_at).toDateString()
      return taskDate === today
    }).length
  })

  return {
    statusStats,
    priorityStats,
    projectStats,
    completionRate,
    todayTasksCount
  }
}
```

#### 統計ダッシュボードコンポーネント

```vue
<template>
  <div class="dashboard">
    <h2>タスク統計ダッシュボード</h2>
    
    <!-- 概要カード -->
    <div class="stats-cards">
      <el-card class="stat-card">
        <div class="stat-content">
          <div class="stat-number">{{ tasks.length }}</div>
          <div class="stat-label">総タスク数</div>
        </div>
      </el-card>

      <el-card class="stat-card">
        <div class="stat-content">
          <div class="stat-number">{{ completionRate }}%</div>
          <div class="stat-label">完了率</div>
        </div>
      </el-card>

      <el-card class="stat-card">
        <div class="stat-content">
          <div class="stat-number">{{ todayTasksCount }}</div>
          <div class="stat-label">今日作成</div>
        </div>
      </el-card>
    </div>

    <!-- ステータス別統計 -->
    <el-card class="chart-card">
      <template #header>
        <h3>ステータス別統計</h3>
      </template>
      <div class="status-chart">
        <div class="status-item">
          <div class="status-bar">
            <div 
              class="status-fill todo"
              :style="{ width: getPercentage(statusStats.todo) + '%' }"
            ></div>
          </div>
          <div class="status-info">
            <span>未着手: {{ statusStats.todo }}件</span>
          </div>
        </div>

        <div class="status-item">
          <div class="status-bar">
            <div 
              class="status-fill in-progress"
              :style="{ width: getPercentage(statusStats.inProgress) + '%' }"
            ></div>
          </div>
          <div class="status-info">
            <span>進行中: {{ statusStats.inProgress }}件</span>
          </div>
        </div>

        <div class="status-item">
          <div class="status-bar">
            <div 
              class="status-fill completed"
              :style="{ width: getPercentage(statusStats.completed) + '%' }"
            ></div>
          </div>
          <div class="status-info">
            <span>完了: {{ statusStats.completed }}件</span>
          </div>
        </div>
      </div>
    </el-card>

    <!-- プロジェクト別統計 -->
    <el-card class="chart-card">
      <template #header>
        <h3>プロジェクト別統計</h3>
      </template>
      <div class="project-stats">
        <div 
          v-for="(count, projectName) in projectStats" 
          :key="projectName"
          class="project-item"
        >
          <span class="project-name">{{ projectName }}</span>
          <span class="project-count">{{ count }}件</span>
        </div>
      </div>
    </el-card>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import { useTasks } from '@/composables/useTasks'
import { useTaskStats } from '@/composables/useTaskStats'

const { tasks } = useTasks()
const { 
  statusStats, 
  priorityStats, 
  projectStats, 
  completionRate, 
  todayTasksCount 
} = useTaskStats()

const getPercentage = (count: number) => {
  const total = tasks.value.length
  return total > 0 ? (count / total) * 100 : 0
}
</script>

<style scoped>
.dashboard {
  padding: 20px;
}

.stats-cards {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 20px;
  margin-bottom: 20px;
}

.stat-card {
  text-align: center;
}

.stat-content {
  padding: 20px;
}

.stat-number {
  font-size: 2.5em;
  font-weight: bold;
  color: #409eff;
}

.stat-label {
  font-size: 0.9em;
  color: #666;
  margin-top: 5px;
}

.chart-card {
  margin-bottom: 20px;
}

.status-chart {
  space-y: 15px;
}

.status-item {
  margin-bottom: 15px;
}

.status-bar {
  width: 100%;
  height: 20px;
  background-color: #f0f0f0;
  border-radius: 10px;
  overflow: hidden;
  margin-bottom: 5px;
}

.status-fill {
  height: 100%;
  transition: width 0.3s ease;
}

.status-fill.todo {
  background-color: #e6a23c;
}

.status-fill.in-progress {
  background-color: #409eff;
}

.status-fill.completed {
  background-color: #67c23a;
}

.status-info {
  font-size: 0.9em;
  color: #666;
}

.project-stats {
  space-y: 10px;
}

.project-item {
  display: flex;
  justify-content: space-between;
  padding: 10px 0;
  border-bottom: 1px solid #f0f0f0;
}

.project-name {
  font-weight: 500;
}

.project-count {
  color: #666;
}
</style>
```

### カスタムユーティリティ関数の例

#### タスク関連のユーティリティ

```typescript
// utils/task.ts
import type { Task } from '@/types/todo'
import { formatDate } from '@/utils/date'

/**
 * タスクの優先度を文字列で取得
 */
export function getPriorityText(priority: number): string {
  switch (priority) {
    case 1: return '低'
    case 2: return '中'
    case 3: return '高'
    default: return '不明'
  }
}

/**
 * タスクのステータスを文字列で取得
 */
export function getStatusText(status: number): string {
  switch (status) {
    case 1: return '未着手'
    case 2: return '進行中'
    case 3: return '完了'
    default: return '不明'
  }
}

/**
 * タスクの優先度に応じたカラーを取得
 */
export function getPriorityColor(priority: number): string {
  switch (priority) {
    case 1: return '#67c23a'  // 緑（低）
    case 2: return '#e6a23c'  // オレンジ（中）
    case 3: return '#f56c6c'  // 赤（高）
    default: return '#909399' // グレー
  }
}

/**
 * タスクのステータスに応じたカラーを取得
 */
export function getStatusColor(status: number): string {
  switch (status) {
    case 1: return '#909399'  // グレー（未着手）
    case 2: return '#409eff'  // 青（進行中）
    case 3: return '#67c23a'  // 緑（完了）
    default: return '#909399' // グレー
  }
}

/**
 * タスクが期限切れかどうかを判定
 */
export function isTaskOverdue(task: Task, dueDateField?: string): boolean {
  if (!dueDateField || !task[dueDateField as keyof Task]) {
    return false
  }
  
  const dueDate = new Date(task[dueDateField as keyof Task] as string)
  const now = new Date()
  
  return dueDate < now && task.status !== 3 // 完了していない場合のみ
}

/**
 * タスクの作成日からの経過日数を取得
 */
export function getDaysFromCreated(task: Task): number {
  const createdDate = new Date(task.created_at)
  const now = new Date()
  const diffTime = Math.abs(now.getTime() - createdDate.getTime())
  const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24))
  
  return diffDays
}

/**
 * タスクリストをソートする
 */
export function sortTasks(
  tasks: Task[], 
  sortBy: 'priority' | 'status' | 'created_at' | 'updated_at',
  order: 'asc' | 'desc' = 'desc'
): Task[] {
  return [...tasks].sort((a, b) => {
    let aValue: any
    let bValue: any
    
    switch (sortBy) {
      case 'priority':
      case 'status':
        aValue = a[sortBy]
        bValue = b[sortBy]
        break
      case 'created_at':
      case 'updated_at':
        aValue = new Date(a[sortBy]).getTime()
        bValue = new Date(b[sortBy]).getTime()
        break
      default:
        return 0
    }
    
    if (order === 'asc') {
      return aValue > bValue ? 1 : -1
    } else {
      return aValue < bValue ? 1 : -1
    }
  })
}

/**
 * タスクリストをフィルタリングする
 */
export function filterTasks(
  tasks: Task[],
  filters: {
    status?: number[]
    priority?: number[]
    projectId?: number
    searchText?: string
  }
): Task[] {
  return tasks.filter(task => {
    // ステータスフィルタ
    if (filters.status && filters.status.length > 0) {
      if (!filters.status.includes(task.status)) {
        return false
      }
    }
    
    // 優先度フィルタ
    if (filters.priority && filters.priority.length > 0) {
      if (!filters.priority.includes(task.priority)) {
        return false
      }
    }
    
    // プロジェクトフィルタ
    if (filters.projectId !== undefined) {
      if (task.project_id !== filters.projectId) {
        return false
      }
    }
    
    // テキスト検索
    if (filters.searchText) {
      const searchLower = filters.searchText.toLowerCase()
      const titleMatch = task.title.toLowerCase().includes(searchLower)
      const descriptionMatch = task.description.toLowerCase().includes(searchLower)
      
      if (!titleMatch && !descriptionMatch) {
        return false
      }
    }
    
    return true
  })
}

/**
 * タスクの詳細情報を含む文字列を生成
 */
export function getTaskSummary(task: Task): string {
  const priority = getPriorityText(task.priority)
  const status = getStatusText(task.status)
  const project = task.project?.name || '未分類'
  const createdDate = formatDate(new Date(task.created_at), 'YYYY/MM/DD')
  
  return `${task.title} | ${priority}優先度 | ${status} | ${project} | 作成日: ${createdDate}`
}
```

#### 使用例

```vue
<template>
  <div class="enhanced-task-list">
    <!-- 検索とフィルタ -->
    <div class="controls">
      <el-input 
        v-model="searchText"
        placeholder="タスクを検索..."
        clearable
        @input="handleSearch"
      />
      
      <el-select 
        v-model="selectedStatuses"
        multiple
        placeholder="ステータス"
        @change="handleFilter"
      >
        <el-option label="未着手" :value="1" />
        <el-option label="進行中" :value="2" />
        <el-option label="完了" :value="3" />
      </el-select>
      
      <el-select 
        v-model="sortBy"
        placeholder="並び順"
        @change="handleSort"
      >
        <el-option label="作成日順" value="created_at" />
        <el-option label="更新日順" value="updated_at" />
        <el-option label="優先度順" value="priority" />
        <el-option label="ステータス順" value="status" />
      </el-select>
    </div>

    <!-- タスク一覧 -->
    <div class="task-list">
      <div 
        v-for="task in filteredAndSortedTasks" 
        :key="task.id"
        class="task-item"
        :class="{ 'overdue': isTaskOverdue(task) }"
      >
        <div class="task-header">
          <h3>{{ task.title }}</h3>
          <div class="task-badges">
            <el-tag 
              :color="getPriorityColor(task.priority)"
              size="small"
            >
              {{ getPriorityText(task.priority) }}
            </el-tag>
            <el-tag 
              :color="getStatusColor(task.status)"
              size="small"
            >
              {{ getStatusText(task.status) }}
            </el-tag>
          </div>
        </div>
        
        <p class="task-description">{{ task.description }}</p>
        
        <div class="task-meta">
          <span>{{ getDaysFromCreated(task) }}日前に作成</span>
          <span v-if="task.project">{{ task.project.name }}</span>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'
import { useTasks } from '@/composables/useTasks'
import { 
  getPriorityText, 
  getStatusText, 
  getPriorityColor, 
  getStatusColor,
  isTaskOverdue,
  getDaysFromCreated,
  sortTasks,
  filterTasks
} from '@/utils/task'

const { tasks } = useTasks()

const searchText = ref('')
const selectedStatuses = ref<number[]>([])
const sortBy = ref<'priority' | 'status' | 'created_at' | 'updated_at'>('created_at')

const filteredAndSortedTasks = computed(() => {
  let result = filterTasks(tasks.value, {
    status: selectedStatuses.value.length > 0 ? selectedStatuses.value : undefined,
    searchText: searchText.value || undefined
  })
  
  result = sortTasks(result, sortBy.value)
  
  return result
})

const handleSearch = () => {
  // 検索は computed で自動的に処理される
}

const handleFilter = () => {
  // フィルタリングは computed で自動的に処理される
}

const handleSort = () => {
  // ソートは computed で自動的に処理される
}
</script>
```

---

## テスト例

### Composableのテスト

```typescript
// tests/composables/useTasks.test.ts
import { describe, it, expect, beforeEach, vi } from 'vitest'
import { useTasks } from '@/composables/useTasks'

// モックの設定
vi.mock('#app', () => ({
  useFetch: vi.fn()
}))

describe('useTasks', () => {
  beforeEach(() => {
    vi.clearAllMocks()
  })

  it('should initialize with empty tasks', () => {
    const { tasks, isLoading } = useTasks()
    
    expect(tasks.value).toEqual([])
    expect(isLoading.value).toBe(false)
  })

  it('should fetch tasks successfully', async () => {
    const mockTasks = [
      {
        id: 1,
        title: 'Test Task',
        description: 'Test Description',
        priority: 2,
        status: 1,
        project_id: 1,
        created_at: '2024-01-01T00:00:00.000000Z',
        updated_at: '2024-01-01T00:00:00.000000Z'
      }
    ]

    const mockUseFetch = vi.mocked(useFetch)
    mockUseFetch.mockResolvedValueOnce({
      data: ref({ tasks: mockTasks, message: 'Success' }),
      error: ref(null)
    })

    const { tasks, fetchTasks } = useTasks()
    
    await fetchTasks()
    
    expect(tasks.value).toEqual(mockTasks)
  })
})
```

### コンポーネントのテスト

```typescript
// tests/components/TaskCard.test.ts
import { describe, it, expect } from 'vitest'
import { mount } from '@vue/test-utils'
import TaskCard from '@/components/task-card.vue'
import type { Task } from '@/types/todo'

const mockTask: Task = {
  id: 1,
  title: 'Test Task',
  description: 'Test Description',
  priority: 2,
  status: 1,
  project_id: 1,
  created_at: '2024-01-01T00:00:00.000000Z',
  updated_at: '2024-01-01T00:00:00.000000Z',
  project: {
    id: 1,
    name: 'Test Project',
    description: 'Test Project Description',
    color: '#409eff',
    created_at: '2024-01-01T00:00:00.000000Z',
    updated_at: '2024-01-01T00:00:00.000000Z'
  }
}

describe('TaskCard', () => {
  it('should render task information correctly', () => {
    const wrapper = mount(TaskCard, {
      props: {
        task: mockTask
      }
    })

    expect(wrapper.text()).toContain('Test Task')
    expect(wrapper.text()).toContain('Test Description')
    expect(wrapper.text()).toContain('Test Project')
  })

  it('should display correct priority badge', () => {
    const wrapper = mount(TaskCard, {
      props: {
        task: mockTask
      }
    })

    expect(wrapper.text()).toContain('中') // priority 2 = 中
  })

  it('should emit edit event when edit button is clicked', async () => {
    const wrapper = mount(TaskCard, {
      props: {
        task: mockTask
      }
    })

    const editButton = wrapper.find('[data-test="edit-button"]')
    await editButton.trigger('click')

    // モーダルの表示状態をテスト
    expect(wrapper.vm.isEditTaskModalVisible).toBe(true)
  })
})
```

---

## パフォーマンス最適化の例

### 大量データの効率的な表示

```vue
<template>
  <div class="virtual-task-list">
    <div class="list-header">
      <h2>タスク一覧（{{ totalTasks }}件）</h2>
    </div>
    
    <!-- 仮想スクロール -->
    <div 
      ref="scrollContainer"
      class="scroll-container"
      @scroll="handleScroll"
    >
      <div 
        class="virtual-list"
        :style="{ height: totalHeight + 'px' }"
      >
        <div 
          class="visible-items"
          :style="{ transform: `translateY(${offsetY}px)` }"
        >
          <task-card
            v-for="task in visibleTasks"
            :key="task.id"
            :task="task"
            class="task-item"
          />
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useTasks } from '@/composables/useTasks'

const { tasks } = useTasks()

// 仮想スクロールの設定
const ITEM_HEIGHT = 200 // 各タスクカードの高さ
const VISIBLE_COUNT = 10 // 一度に表示するアイテム数

const scrollContainer = ref<HTMLElement>()
const scrollTop = ref(0)

// 計算されたプロパティ
const totalTasks = computed(() => tasks.value.length)
const totalHeight = computed(() => totalTasks.value * ITEM_HEIGHT)

const startIndex = computed(() => {
  return Math.floor(scrollTop.value / ITEM_HEIGHT)
})

const endIndex = computed(() => {
  return Math.min(startIndex.value + VISIBLE_COUNT, totalTasks.value)
})

const visibleTasks = computed(() => {
  return tasks.value.slice(startIndex.value, endIndex.value)
})

const offsetY = computed(() => {
  return startIndex.value * ITEM_HEIGHT
})

// スクロールハンドラー
const handleScroll = (event: Event) => {
  const target = event.target as HTMLElement
  scrollTop.value = target.scrollTop
}

// リサイズハンドラー
const handleResize = () => {
  // ウィンドウサイズ変更時の処理
}

onMounted(() => {
  window.addEventListener('resize', handleResize)
})

onUnmounted(() => {
  window.removeEventListener('resize', handleResize)
})
</script>

<style scoped>
.virtual-task-list {
  height: 100vh;
  display: flex;
  flex-direction: column;
}

.list-header {
  padding: 20px;
  border-bottom: 1px solid #eee;
}

.scroll-container {
  flex: 1;
  overflow-y: auto;
}

.virtual-list {
  position: relative;
}

.visible-items {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
}

.task-item {
  height: 200px;
  margin-bottom: 10px;
}
</style>
```

---

## 実装のベストプラクティス

### 1. エラーハンドリングの統一

```typescript
// utils/errorHandler.ts
import { ElMessage } from 'element-plus'

export class ApiError extends Error {
  constructor(
    message: string,
    public status: number,
    public response?: any
  ) {
    super(message)
    this.name = 'ApiError'
  }
}

export function handleApiError(error: unknown, context?: string) {
  console.error(`API Error ${context ? `in ${context}` : ''}:`, error)
  
  if (error instanceof ApiError) {
    switch (error.status) {
      case 400:
        ElMessage.error('リクエストが無効です')
        break
      case 401:
        ElMessage.error('認証が必要です')
        break
      case 403:
        ElMessage.error('アクセス権限がありません')
        break
      case 404:
        ElMessage.error('リソースが見つかりません')
        break
      case 422:
        ElMessage.error('入力内容に問題があります')
        break
      case 500:
        ElMessage.error('サーバーエラーが発生しました')
        break
      default:
        ElMessage.error('予期しないエラーが発生しました')
    }
  } else {
    ElMessage.error('ネットワークエラーが発生しました')
  }
}
```

### 2. 型安全なAPI呼び出し

```typescript
// utils/api.ts
import type { Task, Project, ApiResponse, ProjectApiResponse } from '@/types/todo'

class ApiClient {
  private baseURL = 'http://localhost:9000/api'

  private async request<T>(
    endpoint: string, 
    options: RequestInit = {}
  ): Promise<T> {
    const url = `${this.baseURL}${endpoint}`
    
    const response = await fetch(url, {
      headers: {
        'Content-Type': 'application/json',
        ...options.headers,
      },
      ...options,
    })

    if (!response.ok) {
      throw new ApiError(
        `HTTP ${response.status}: ${response.statusText}`,
        response.status,
        await response.json().catch(() => null)
      )
    }

    return response.json()
  }

  // タスク関連のAPI
  async getTasks(params?: { status?: number; project_id?: number }): Promise<ApiResponse> {
    const searchParams = new URLSearchParams()
    if (params?.status) searchParams.append('status', params.status.toString())
    if (params?.project_id) searchParams.append('project_id', params.project_id.toString())
    
    const query = searchParams.toString()
    return this.request<ApiResponse>(`/tasks${query ? `?${query}` : ''}`)
  }

  async createTask(task: Omit<Task, 'id' | 'created_at' | 'updated_at' | 'project'>): Promise<{ task: Task; message: string }> {
    return this.request<{ task: Task; message: string }>('/tasks/store', {
      method: 'POST',
      body: JSON.stringify(task),
    })
  }

  async updateTask(id: number, task: Partial<Task>): Promise<{ task: Task; message: string }> {
    return this.request<{ task: Task; message: string }>(`/tasks/${id}`, {
      method: 'PATCH',
      body: JSON.stringify(task),
    })
  }

  async deleteTask(id: number): Promise<{ message: string }> {
    return this.request<{ message: string }>(`/tasks/${id}`, {
      method: 'DELETE',
    })
  }

  // プロジェクト関連のAPI
  async getProjects(): Promise<ProjectApiResponse> {
    return this.request<ProjectApiResponse>('/projects')
  }

  async createProject(project: Omit<Project, 'id' | 'created_at' | 'updated_at' | 'tasks'>): Promise<{ project: Project; message: string }> {
    return this.request<{ project: Project; message: string }>('/projects/store', {
      method: 'POST',
      body: JSON.stringify(project),
    })
  }

  async updateProject(id: number, project: Partial<Project>): Promise<{ project: Project; message: string }> {
    return this.request<{ project: Project; message: string }>(`/projects/${id}`, {
      method: 'PATCH',
      body: JSON.stringify(project),
    })
  }

  async deleteProject(id: number): Promise<{ message: string; warning?: string; affected_tasks_count?: number }> {
    return this.request<{ message: string; warning?: string; affected_tasks_count?: number }>(`/projects/${id}`, {
      method: 'DELETE',
    })
  }
}

export const apiClient = new ApiClient()
```

これらの例を参考に、プロジェクトの要件に応じてカスタマイズしてください。