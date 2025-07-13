import { ref } from 'vue'
import { ElMessage, ElMessageBox } from 'element-plus'
import type { Project, ProjectApiResponse, ProjectForm } from '@/types/todo'

// グローバルな状態をファイルレベルで定義（シングルトンパターン）
const projects = ref<Project[]>([])
const isLoading = ref(false)

/**
 * プロジェクト管理のためのComposable
 */
export const useProjects = () => {

  /**
   * プロジェクト一覧を取得する
   */
  const fetchProjects = async () => {
    // リクエスト中の場合はスキップ
    if (isLoading.value) {
      console.log('プロジェクト一覧のリクエスト中です')
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
      
      await fetchProjects()
    } catch (error) {
      ElMessage.error(error instanceof Error ? error.message : 'プロジェクトの更新に失敗しました')
      throw error
    }
  }

  /**
   * プロジェクトを削除する（確認ダイアログ付き）
   * @param id プロジェクトID
   * @param projectName プロジェクト名（確認ダイアログ用）
   */
  const deleteProject = async (id: number, projectName: string) => {
    try {
      // 削除確認ダイアログ
      await ElMessageBox.confirm(
        `プロジェクト「${projectName}」を削除しますか？\n関連するタスクがある場合は未分類に移動されます。`,
        '削除確認',
        {
          confirmButtonText: '削除する',
          cancelButtonText: 'キャンセル',
          type: 'warning',
        }
      )

      const { data, error } = await useFetch(`http://localhost:9000/api/projects/${id}`, {
        method: 'DELETE',
        headers: {
          'Content-Type': 'application/json'
        }
      })

      if (error.value) {
        throw new Error('プロジェクトの削除に失敗しました')
      }

      // サーバーからの警告メッセージを表示
      const responseData = data.value as any
      if (responseData?.warning) {
        ElMessage({
          message: responseData.warning,
          type: 'warning',
          duration: 5000,
        })
      } else {
        ElMessage.success('プロジェクトが削除されました')
      }
      
      await fetchProjects()
    } catch (error) {
      if (error === 'cancel') {
        // ユーザーがキャンセルした場合
        return
      }
      ElMessage.error(error instanceof Error ? error.message : 'プロジェクトの削除に失敗しました')
      throw error
    }
  }

  /**
   * 特定のプロジェクトをキャッシュから検索する
   * @param id プロジェクトID
   */
  const getProjectById = (id: number): Project | undefined => {
    return projects.value.find(project => project.id === id)
  }

  return {
    projects,
    isLoading,
    fetchProjects,
    createProject,
    updateProject,
    deleteProject,
    getProjectById
  }
} 