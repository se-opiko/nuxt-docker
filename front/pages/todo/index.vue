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
       <el-tabs v-model="activeTab">
          <el-tab-pane label="すべて" name="all">
            <el-skeleton :loading="isLoading" animated>
              <template #template>
                <div v-for="i in 3" :key="i" class="mb-3">
                  <el-skeleton-item variant="text" style="width: 100%; height: 100px" />
                </div>
              </template>
              <template #default>
                <template v-for="task in tasks" :key="task.id">
                  <task-card :task="task" class="mb-3" :on-fetch-tasks="fetchTasks" />
                </template>
              </template>
            </el-skeleton>
          </el-tab-pane>
          <el-tab-pane label="未着手" name="pending"></el-tab-pane>
          <el-tab-pane label="進行中" name="progress"></el-tab-pane>
          <el-tab-pane label="完了" name="complete"></el-tab-pane>
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
  const { tasks, isLoading, fetchTasks } = useTasks()

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

  /** タスク登録 */
  async function createTask(inputTask: RuleForm) {
    await useFetch('http://localhost:9000/api/tasks/store', {
      method: 'POST',
      body: JSON.stringify(inputTask)
    })
  }
</script>
<style scoped lang="css">
  .base {
    min-height: 100vh;
  }
</style>