<template>
  <el-card shadow="hover">
    <template #header>
      <div class="flex justify-between">
        <div class="flex flex-col">
          <span class="text-lg font-bold">{{ task.title }}</span>
          <!-- プロジェクト表示 -->
          <div v-if="task.project" class="flex items-center mt-1">
            <el-tag 
              :color="task.project.color || '#409eff'" 
              :style="{ color: getTextColor(task.project.color) }"
              size="small"
              class="mr-2"
            >
              {{ task.project.name }}
            </el-tag>
          </div>
        </div>
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
      <div class="flex items-center justify-between mt-2">
        <div class="flex items-center">
          <!-- 優先度 -->
          <el-tag class="mr-4" :type="priority === '高' ? 'danger' : priority === '中' ? 'warning' : 'success'">{{ priority }}</el-tag>
          <!-- 更新日 -->
          <small>{{ formatDate(new Date(task.updated_at || task.created_at), 'YYYY/MM/DD') }}</small>
        </div>
      </div>
    </div>
  </el-card>
</template>
<script setup lang="ts">
import type { PropType } from 'vue'
import { Edit, Delete } from '@element-plus/icons-vue'
import type { Task, RuleForm } from '@/types/todo'
import type { ApiResponse } from '@/types/todo'
import { ElMessage } from 'element-plus'
import { useTasks } from '@/composables/useTasks'

const props = defineProps({
  task: {
    type: Object as PropType<Task>,
    required: true
  }
})

const isDeleteConfirmModalVisible = ref(false)
const isEditTaskModalVisible = ref(false)
const { updateTask, deleteTask } = useTasks()

async function handleDeleteConfirm() {
  await deleteTask(props.task.id)
}

/**
 * タスクを更新する
 * @param {RuleForm} inputTask - 更新するタスクの情報
 * @throws {Error} タスクの更新に失敗した場合
 */
async function editTask(inputTask: RuleForm) {
  const taskData = {
    title: inputTask.title,
    description: inputTask.description,
    priority: inputTask.priority,
    status: inputTask.status,
    project_id: inputTask.project_id,
  }
  await updateTask(props.task.id, taskData)
}

/**
 * 優先度の表示文字列を取得する
 */
const priority = computed(() => {
  return props.task.priority === 1 ? '低' : props.task.priority === 2 ? '中' : '高'
})

/**
 * プロジェクトのカラーに応じた文字色を取得する
 * @param {string | null} color - プロジェクトのカラーコード
 * @returns {string} 文字色
 */
function getTextColor(color: string | null): string {
  if (!color) return '#ffffff'
  
  // カラーコードから明度を計算して、適切な文字色を決定
  const hex = color.replace('#', '')
  const r = parseInt(hex.substr(0, 2), 16)
  const g = parseInt(hex.substr(2, 2), 16)
  const b = parseInt(hex.substr(4, 2), 16)
  
  // 明度計算（WCAG基準）
  const brightness = (r * 299 + g * 587 + b * 114) / 1000
  
  return brightness > 128 ? '#000000' : '#ffffff'
}
</script>