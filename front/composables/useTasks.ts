import { ref } from 'vue'
import { ElMessage } from 'element-plus'
import type { Task, ApiResponse } from '@/types/todo'

// グローバルな状態をファイルレベルで定義（シングルトンパターン）
const tasks = ref<Task[]>([])
const isLoading = ref(false)

/**
 * 検索パラメータの型定義
 */
type SearchParams = {
  status?: number
  project_id?: number | null
}

const searchParams = ref<SearchParams>({})

/**
 * タスク管理のためのComposable
 */
export const useTasks = () => {
  
  /**
   * タスク一覧を取得する
   */
  const fetchTasks = async () => {
    // 既に取得中の場合はスキップ（重複リクエスト防止）
    if (isLoading.value) {
      console.log('タスク一覧の取得は既にリクエスト中です')
      return tasks.value
    }

    try {
      isLoading.value = true
      console.log('タスク一覧を取得中...', { params: searchParams.value })
      
      const { data, error } = await useFetch<ApiResponse>('http://localhost:9000/api/tasks', {
        method: 'GET',
        headers: {
          'Content-Type': 'application/json'
        },
        params: searchParams.value
      })
      
      console.log('Task API レスポンス:', { data: data.value, error: error.value })
      
      if (error.value) {
        console.error('Task API エラー:', error.value)
        throw new Error('APIリクエストに失敗しました')
      }

      if (!data.value?.tasks) {
        console.error('Task レスポンスデータが不正:', data.value)
        throw new Error('レスポンスデータが不正です')
      }

      tasks.value = data.value.tasks
      console.log('タスク一覧取得成功:', tasks.value.length + '件')
    } catch (error) {
      console.error('タスク取得エラー:', error)
      ElMessage.error(error instanceof Error ? error.message : 'タスクの取得に失敗しました')
      return []
    } finally {
      isLoading.value = false
    }
  }

  /**
   * タスクを作成する
   * @param {Object} taskData - 作成するタスクのデータ
   */
  const createTask = async (taskData: any) => {
    try {
      const { data, error } = await useFetch('http://localhost:9000/api/tasks/store', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify(taskData)
      })

      if (error.value) {
        throw new Error('タスクの作成に失敗しました')
      }

      ElMessage.success('タスクが作成されました')
      
      // 作成後に最新データを取得
      await fetchTasks()
    } catch (error) {
      ElMessage.error(error instanceof Error ? error.message : 'タスクの作成に失敗しました')
      throw error
    }
  }

  /**
   * タスクを更新する
   * @param {number} id - タスクID
   * @param {Object} taskData - 更新するタスクのデータ
   */
  const updateTask = async (id: number, taskData: any) => {
    try {
      const { data, error } = await useFetch(`http://localhost:9000/api/tasks/${id}`, {
        method: 'PATCH',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify(taskData)
      })

      if (error.value) {
        throw new Error('タスクの更新に失敗しました')
      }

      ElMessage.success('タスクが更新されました')
      
      // 更新後に最新データを取得
      await fetchTasks()
    } catch (error) {
      ElMessage.error(error instanceof Error ? error.message : 'タスクの更新に失敗しました')
      throw error
    }
  }

  /**
   * タスクを削除する
   * @param {number} id - タスクID
   */
  const deleteTask = async (id: number) => {
    try {
      const { data, error } = await useFetch(`http://localhost:9000/api/tasks/${id}`, {
        method: 'DELETE',
        headers: {
          'Content-Type': 'application/json'
        }
      })

      if (error.value) {
        throw new Error('タスクの削除に失敗しました')
      }

      ElMessage.success('タスクが削除されました')
      
      // 削除後に最新データを取得
      await fetchTasks()
    } catch (error) {
      ElMessage.error(error instanceof Error ? error.message : 'タスクの削除に失敗しました')
      throw error
    }
  }

  return {
    tasks,
    isLoading,
    searchParams,
    fetchTasks,
    createTask,
    updateTask,
    deleteTask
  }
} 