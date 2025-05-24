import { ref } from 'vue'
import { ElMessage } from 'element-plus'
import type { Task, ApiResponse } from '@/types/todo'

export const useTasks = () => {
  const tasks = ref<Task[]>([])
  const isLoading = ref(false)

  const fetchTasks = async () => {
    try {
      isLoading.value = true
      const { data, error } = await useFetch<ApiResponse>('http://localhost:9000/api/tasks', {
        method: 'GET',
        headers: {
          'Content-Type': 'application/json'
        }
      })
      
      if (error.value) {
        throw new Error('APIリクエストに失敗しました')
      }

      if (!data.value?.tasks) {
        throw new Error('レスポンスデータが不正です')
      }

      tasks.value = data.value.tasks
    } catch (error) {
      ElMessage.error(error instanceof Error ? error.message : 'タスクの取得に失敗しました')
      return []
    } finally {
      isLoading.value = false
    }
  }

  return {
    tasks,
    isLoading,
    fetchTasks
  }
} 