import { ref } from 'vue'
import { ElMessage } from 'element-plus'
import type { Project, ProjectApiResponse, ProjectForm } from '@/types/todo'

// グローバルな状態をファイルレベルで定義（シングルトンパターン）
const projects = ref<Project[]>([])
const isLoading = ref(false)
let isFetched = false // 一度取得したかのフラグ

/**
 * プロジェクト管理のためのComposable
 */
export const useProjects = () => {

  /**
   * プロジェクト一覧を取得する
   */
  const fetchProjects = async () => {
    // 既に取得済みまたは取得中の場合はスキップ
    if (isFetched || isLoading.value) {
      console.log('プロジェクト一覧は既に取得済み、またはリクエスト中です')
      return projects.value
    }

    try {
      isLoading.value = true
      console.log('プロジェクト一覧を取得中...')
      
      const { data, error } = await useFetch<ProjectApiResponse>('http://localhost:9000/api/projects', {
        method: 'GET',
        headers: {
          'Content-Type': 'application/json'
        }
      })
      
      console.log('API レスポンス:', { data: data.value, error: error.value })
      
      if (error.value) {
        console.error('API エラー:', error.value)
        throw new Error('APIリクエストに失敗しました')
      }

      if (!data.value?.projects) {
        console.error('レスポンスデータが不正:', data.value)
        throw new Error('レスポンスデータが不正です')
      }

      projects.value = data.value.projects
      isFetched = true // 取得完了フラグを設定
      console.log('プロジェクト一覧取得成功:', projects.value)
    } catch (error) {
      console.error('プロジェクト取得エラー:', error)
      ElMessage.error(error instanceof Error ? error.message : 'プロジェクトの取得に失敗しました')
      return []
    } finally {
      isLoading.value = false
    }
  }

  /**
   * プロジェクトを作成する
   * @param projectData 作成するプロジェクトのデータ
   */
  const createProject = async (projectData: ProjectForm) => {
    try {
      const { data, error } = await useFetch('http://localhost:9000/api/projects/store', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify(projectData)
      })

      if (error.value) {
        throw new Error('プロジェクトの作成に失敗しました')
      }

      ElMessage.success('プロジェクトが作成されました')
      
      // 作成後にキャッシュをクリアして最新データを取得
      isFetched = false
      await fetchProjects()
    } catch (error) {
      ElMessage.error(error instanceof Error ? error.message : 'プロジェクトの作成に失敗しました')
      throw error
    }
  }

  /**
   * プロジェクトを更新する
   * @param id プロジェクトID
   * @param projectData 更新するプロジェクトのデータ
   */
  const updateProject = async (id: number, projectData: ProjectForm) => {
    try {
      const { data, error } = await useFetch(`http://localhost:9000/api/projects/${id}`, {
        method: 'PATCH',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify(projectData)
      })

      if (error.value) {
        throw new Error('プロジェクトの更新に失敗しました')
      }

      ElMessage.success('プロジェクトが更新されました')
      
      // 更新後にキャッシュをクリアして最新データを取得
      isFetched = false
      await fetchProjects()
    } catch (error) {
      ElMessage.error(error instanceof Error ? error.message : 'プロジェクトの更新に失敗しました')
      throw error
    }
  }

  /**
   * プロジェクトを削除する
   * @param id プロジェクトID
   */
  const deleteProject = async (id: number) => {
    try {
      const { data, error } = await useFetch(`http://localhost:9000/api/projects/${id}`, {
        method: 'DELETE',
        headers: {
          'Content-Type': 'application/json'
        }
      })

      if (error.value) {
        throw new Error('プロジェクトの削除に失敗しました')
      }

      ElMessage.success('プロジェクトが削除されました')
      
      // 削除後にキャッシュをクリアして最新データを取得
      isFetched = false
      await fetchProjects()
    } catch (error) {
      ElMessage.error(error instanceof Error ? error.message : 'プロジェクトの削除に失敗しました')
      throw error
    }
  }

  /**
   * プロジェクトキャッシュをクリアする（テスト用）
   */
  const clearProjectsCache = () => {
    isFetched = false
    projects.value = []
    console.log('プロジェクトキャッシュをクリアしました')
  }

  return {
    projects,
    isLoading,
    fetchProjects,
    createProject,
    updateProject,
    deleteProject,
    clearProjectsCache
  }
} 