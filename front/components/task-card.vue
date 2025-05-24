<template>
  <el-card shadow="hover">
    <template #header>
      <div class="flex justify-between">
        <span class="text-lg font-bold">{{ task.title }}</span>
        <div>
          <el-button type="primary" circle class="mr-2" @click="isEditTaskModalVisible = true">
            <el-icon><Edit /></el-icon>
          </el-button>
          <task-input-modal 
            v-model="isEditTaskModalVisible" 
            title="タスクを更新する" 
            save-button-text="更新" 
            :task="task" 
            :on-save="editTask"
          />
          <el-button type="danger" circle @click="isDeleteConfirmModalVisible = true">
            <el-icon><Delete /></el-icon>
          </el-button>
          <common-confirm-modal
            v-model="isDeleteConfirmModalVisible"
            title="タスクを削除しますか？"
            message="この操作は元に戻すことはできません。"
            confirm-button-text="削除する"
            @confirm="handleDeleteConfirm"
          />
        </div>
      </div>
    </template>
    <div>
      <p>{{ task.description }}</p>
      <!-- 優先度 -->
      <el-tag class="mr-4" :type="priority === '高' ? 'danger' : priority === '中' ? 'warning' : 'success'">{{ priority }}</el-tag>
      <!-- 更新日 -->
      <small>{{ formatDate(new Date(task.updated_at || task.created_at), 'YYYY/MM/DD') }}</small>
    </div>
  </el-card>
</template>
<script setup lang="ts">
import type { PropType } from 'vue'
import { Edit, Delete } from '@element-plus/icons-vue'
import type { Task, RuleForm } from '@/types/todo'
import type { ApiResponse } from '@/types/todo'
import { ElMessage } from 'element-plus'

const props = defineProps({
  task: {
    type: Object as PropType<Task>,
    required: true
  },
  onFetchTasks: {
    type: Function,
    required: true
  }
})

const isDeleteConfirmModalVisible = ref(false)
const isEditTaskModalVisible = ref(false)

async function handleDeleteConfirm() {
  await deleteTask()
}

// タスクを削除する
async function deleteTask() {
  try {
    const { data, error } = await useFetch<ApiResponse>(`http://localhost:9000/api/tasks/${props.task.id}/destroy`, {
        method: 'DELETE',
        headers: {
          'Content-Type': 'application/json'
        }
      });
    if (error.value) {
      throw new Error('タスクの削除に失敗しました');
    }
    ElMessage.success('タスクを削除しました');
    await props.onFetchTasks()
  } catch (error) {
    console.error(error)
    ElMessage.error('タスクの削除に失敗しました');
  }
}

// タスクを編集する
async function editTask(inputTask: RuleForm) {
  const { data, error } = await useFetch<ApiResponse>(`http://localhost:9000/api/tasks/${props.task.id}/update`, {
    method: 'PATCH',
    headers: {
      'Content-Type': 'application/json'
    },
    body: {
      title: inputTask.title,
      description: inputTask.description,
      priority: inputTask.priority,
      status: inputTask.status,
    }
  });
  if (error.value) {
    throw new Error('タスクの編集に失敗しました');
  }
  await props.onFetchTasks()
}

// 優先度の表示
const priority = computed(() => {
  return props.task.priority === 1 ? '高' : props.task.priority === 2 ? '中' : '低'
})
</script>