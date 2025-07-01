<template>
  <el-container class="base bg-red">
    <!-- ヘッダー -->
    <el-header>
      <el-page-header icon="" title="戻る" color="black" @back="$router.back()">
        <template #content>
          <div class="flex items-center">
            <span class="text-large font-600 mr-3"> タスク管理 </span>
          </div>
        </template>
        <template #extra>
          <div class="flex items-center">
            <el-button type="primary" class="ml-2" @click="dialogVisible = true">作成</el-button>
            <task-input-modal v-model="dialogVisible" title="タスクを登録する" save-button-text="登録" :on-save="handleCreateTask" />
          </div>
        </template>
      </el-page-header>
    </el-header>
    <!-- メイン -->
    <el-main class="main-content">
      <el-form class="search-form">
        <el-row :gutter="16">
          <el-col :span="12">
            <el-form-item>
              <el-input v-model="searchWord" :prefix-icon="Search" placeholder="タスクを検索"></el-input>
            </el-form-item>
          </el-col>
          <el-col :span="8">
            <el-form-item>
              <el-select 
                v-model="selectedProjectId" 
                placeholder="プロジェクトでフィルタ"
                clearable
                @change="onProjectFilterChange"
              >
                <el-option label="すべてのプロジェクト" value="" />
                <el-option label="未分類" value="null" />
                <el-option 
                  v-for="project in projects" 
                  :key="project.id" 
                  :label="project.name" 
                  :value="project.id" 
                />
              </el-select>
            </el-form-item>
          </el-col>
          <el-col :span="4">
            <el-form-item>
              <!-- FIXME:リアルタイム検索にする？ -->
              <el-button type="primary" @click="onSearch">検索</el-button>
            </el-form-item>
          </el-col>
        </el-row>
      </el-form>
       <el-tabs v-model="activeTab" type="border-card" class="tabs-container" @tab-change="onTabChange">
          <el-tab-pane v-for="tab in tabPanels" :key="tab.name" :label="tab.label" :name="tab.name">
            <div class="tab-content">
              <el-skeleton :loading="isLoading" animated>
                <template #template>
                  <div v-for="i in 3" :key="i" class="mb-3">
                    <el-skeleton-item variant="text" style="width: 100%; height: 100px" />
                  </div>
                </template>
                <template #default>
                  <template v-for="(task, index) in tasks" :key="index">
                    <task-card :task="task" class="mb-3" />
                  </template>
                </template>
              </el-skeleton>
            </div>
          </el-tab-pane>
       </el-tabs>
    </el-main>
    <el-footer>

    </el-footer>
  </el-container>
</template>
<script setup lang="ts">
  import { ref, onMounted } from 'vue'
  import { Search, Plus } from '@element-plus/icons-vue'
  import type { RuleForm } from '@/types/todo'
  import { useTasks } from '@/composables/useTasks'
  import { useProjects } from '@/composables/useProjects'

  const searchWord = ref('');
  const activeTab = ref('all');
  const selectedProjectId = ref<string | number>('');
  const { tasks, isLoading, searchParams, fetchTasks, createTask } = useTasks()
  const { projects, fetchProjects } = useProjects()

  onMounted(async () => {
    await Promise.all([
      fetchTasks(),
      fetchProjects()
    ])
  })

  /** 検索 */
  async function onSearch() {
    await fetchTasks()
  }

  /**
   * プロジェクトフィルタの変更処理
   */
  async function onProjectFilterChange() {
    if (selectedProjectId.value === '') {
      // すべてのプロジェクト
      searchParams.value.project_id = undefined
    } else if (selectedProjectId.value === 'null') {
      // 未分類（project_idがnull）
      searchParams.value.project_id = null
    } else {
      // 特定のプロジェクト
      searchParams.value.project_id = Number(selectedProjectId.value)
    }
    await fetchTasks()
  }

  const dialogVisible = ref(false)

  const handleCreateTask = async (inputTask: RuleForm) => {
    await createTask(inputTask)
  }

  const tabPanels = [
    {
      label: 'すべて',
      name: 'all', 
      status: undefined,
    },
    {
      label: '未着手',
      name: 'pending',
      status: 1,
    },
    {
      label: '進行中', 
      name: 'progress',
      status: 2,
    },
    {
      label: '完了',
      name: 'complete',
      status: 3,
    }
  ] as const

  /**
   * タブが変更された時の処理
   */
  async function onTabChange() {
    const selectedTab = tabPanels.find(panel => panel.name === activeTab.value)
    if (selectedTab) {
      searchParams.value.status = selectedTab.status
    }
    await fetchTasks()
  }
</script>
<style scoped lang="css">
  .base {
    min-height: 100vh;
    background-color: red !important;
  }

  /* ヘッダーの上部に余白を追加 */
  .el-header {
    padding-top: 20px;
  }

  .main-content {
    padding: 20px;
  }

  .search-form {
    margin-bottom: 16px;
  }

  .tab-content {
    max-height: 60vh; /* ビューポートの60%の高さに制限 */
    overflow-y: auto;
    padding-right: 8px; /* スクロールバーとの間隔 */
  }

  /* スクロールバーのスタイリング（Webkit系ブラウザ） */
  .tab-content::-webkit-scrollbar {
    width: 6px;
  }

  .tab-content::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
  }

  .tab-content::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 3px;
  }

  .tab-content::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
  }
</style>