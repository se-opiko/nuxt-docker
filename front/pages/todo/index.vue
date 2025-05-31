<template>
  <el-container class="base bg-white">
    <!-- ヘッダー -->
    <el-header class="pt-2">
      <el-page-header icon="" title="Back" color="black" @back="$router.back()">
        <template #content>
          <div class="flex items-center">
            <span class="text-large font-600 mr-3"> タスク管理 </span>
          </div>
        </template>
        <template #extra>
          <div class="flex items-center">
            <el-button type="primary" class="ml-2" @click="dialogVisible = true">Create</el-button>
            <task-input-modal v-model="dialogVisible" title="タスクを登録する" save-button-text="登録" :on-save="handleCreateTask" />
          </div>
        </template>
      </el-page-header>
    </el-header>
    <!-- メイン -->
    <el-main>
      <el-form>
        <el-form-item>
          <el-input v-model="searchWord" :prefix-icon="Search" placeholder="タスクを検索"></el-input>
        </el-form-item>
        <el-form-item>
          <!-- FIXME:リアルタイム検索にする？ -->
          <el-button type="primary" @click="onSearch">検索</el-button>
        </el-form-item>
      </el-form>
       <el-tabs v-model="activeTab"  type="border-card" @tab-change="onTabChange">
          <el-tab-pane v-for="tab in tabPanels" :key="tab.name" :label="tab.label" :name="tab.name">
            <el-skeleton :loading="isLoading" animated>
              <template #template>
                <div v-for="i in 3" :key="i" class="mb-3">
                  <el-skeleton-item variant="text" style="width: 100%; height: 100px" />
                </div>
              </template>
              <template #default>
                <template v-for="(task, index) in tasks" :key="index">
                  <task-card :task="task" class="mb-3" :on-fetch-tasks="fetchTasks" />
                </template>
              </template>
            </el-skeleton>
          </el-tab-pane>
       </el-tabs>
    </el-main>
    <el-footer>

    </el-footer>
  </el-container>
</template>
<script setup lang="ts">
  import { ref, onMounted } from 'vue'
  import { Search } from '@element-plus/icons-vue'
  import type { RuleForm } from '@/types/todo'
  import { useTasks } from '@/composables/useTasks'

  const searchWord = ref('');
  const activeTab = ref('all');
  const { tasks, isLoading, searchParams, fetchTasks } = useTasks()

  onMounted(async () => {
    await fetchTasks()
  })

  /** 検索 */
  async function onSearch() {
    await fetchTasks()
  }

  const dialogVisible = ref(false)

  const handleCreateTask = async (inputTask: RuleForm) => {
    await createTask(inputTask)
    await fetchTasks()
  }

  /**
   * タスクを作成する
   * @param {RuleForm} inputTask - 作成するタスクの情報
   * @throws {Error} タスクの作成に失敗した場合
   */
  async function createTask(inputTask: RuleForm) {
    await useFetch('http://localhost:9000/api/tasks/store', {
      method: 'POST',
      body: JSON.stringify(inputTask)
    })
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
  }
</style>