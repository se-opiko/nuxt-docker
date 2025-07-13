<template>
  <el-dialog
    :model-value="modelValue"
    :title="title"
    width="500"
    :before-close="handleClose"
    @update:model-value="$emit('update:modelValue', $event)"
  >
    <el-form 
      ref="ruleFormRef" 
      :model="inputProject" 
      :rules="rules" 
      label-width="auto"
    >
      <el-form-item label="プロジェクト名" prop="name">
        <el-input 
          v-model="inputProject.name" 
          type="text" 
          maxlength="255" 
          show-word-limit 
          placeholder="プロジェクト名を入力（必須）" 
        />
      </el-form-item>
  
      <el-form-item label="説明">
        <el-input 
          v-model="inputProject.description" 
          type="textarea" 
          maxlength="1000" 
          show-word-limit 
          placeholder="プロジェクトの説明（オプション）" 
          :rows="4"
        />
      </el-form-item>

      <el-form-item label="カラー">
        <div class="color-picker-container">
          <el-color-picker 
            v-model="inputProject.color" 
            placeholder="プロジェクトのカラーを選択（オプション）"
          />
          <span class="color-picker-text">
            {{ inputProject.color || 'カラーを選択してください' }}
          </span>
        </div>
      </el-form-item>

      <el-form-item>
        <div class="form-actions">
          <el-button 
            type="primary" 
            @click="onSave(ruleFormRef)"
          >
            {{ saveButtonText }}
          </el-button>
        </div>
      </el-form-item>
    </el-form>
  </el-dialog>
</template>

<script setup lang="ts">
/**
 * プロジェクト作成・編集用モーダルコンポーネント
 * 
 * プロジェクトの作成と編集を行うためのモーダルダイアログ
 * バリデーション機能付きフォームを提供する
 */
import { ref, watch, reactive, type PropType } from 'vue'
import type { Project, ProjectForm } from '@/types/todo'
import type { FormInstance, FormRules } from 'element-plus'
import { ElMessage, ElMessageBox } from 'element-plus'

/**
 * コンポーネントのプロパティ定義
 */
const props = defineProps({
  /** モーダルの表示状態 */
  modelValue: {
    type: Boolean,
    required: true
  },
  /** モーダルのタイトル */
  title: {
    type: String,
    default: 'プロジェクトを作成する'
  },
  /** 保存ボタンのテキスト */
  saveButtonText: {
    type: String,
    default: '作成'
  },
  /** 編集時のプロジェクトデータ */
  project: {
    type: Object as PropType<Project>,
    default: () => ({
      id: 0,
      name: '',
      description: '',
      color: '',
      created_at: '',
      updated_at: '',
    })
  },
  /** 保存処理のコールバック関数 */
  onSave: {
    type: Function,
    required: true
  },
})

/**
 * コンポーネントのイベント定義
 */
const emit = defineEmits<{
  'update:modelValue': [value: boolean]
  'save': [project: ProjectForm]
}>()

/** フォーム参照 */
const ruleFormRef = ref<FormInstance>()

/**
 * プロジェクトのフォームデータ
 */
const inputProject = reactive<ProjectForm>({
  name: props.project?.name || '',
  description: props.project?.description || '',
  color: props.project?.color || '',
})

/**
 * フォームのバリデーションルール
 */
const rules = reactive<FormRules<ProjectForm>>({
  name: [
    { required: true, message: 'プロジェクト名を入力してください', trigger: 'blur' },
    { max: 255, message: 'プロジェクト名は255文字以内で入力してください', trigger: 'blur' }
  ],
  description: [
    { max: 1000, message: '説明は1000文字以内で入力してください', trigger: 'blur' }
  ],
})

/**
 * フォームデータをクリアする
 */
const clearInputProject = () => {
  inputProject.name = props.project?.name || ''
  inputProject.description = props.project?.description || ''
  inputProject.color = props.project?.color || ''
}

/**
 * モーダルを閉じる際の処理
 * 
 * @param done - 閉じる処理を実行するコールバック
 */
const handleClose = (done: () => void) => {
  ElMessageBox.confirm('閉じると入力した内容が消えますがよろしいでしょうか?')
    .then(() => {
      done()
      clearInputProject()
    })
    .catch(() => {
      // キャンセル時は何もしない
    })
}

/**
 * 保存処理
 * 
 * @param formEl - フォームのインスタンス
 */
const onSave = async (formEl: FormInstance | undefined) => {
  if (!formEl) return
  
  await formEl.validate(async (valid: boolean) => {
    if (valid) {
      try {
        await props.onSave(inputProject)
        emit('update:modelValue', false)
        clearInputProject()
        ElMessage.success(props.saveButtonText + 'しました')
      } catch (error) {
        console.error('プロジェクト保存エラー:', error)
        ElMessage.error(props.saveButtonText + 'に失敗しました')
      }
    }
  })
}

/**
 * プロジェクトプロパティの変更を監視してフォームを更新
 */
watch(() => props.project, (newProject) => {
  if (newProject) {
    inputProject.name = newProject.name || ''
    inputProject.description = newProject.description || ''
    inputProject.color = newProject.color || ''
  }
}, { deep: true })
</script>

<style scoped lang="css">
/**
 * カラーピッカーコンテナのスタイル
 */
.color-picker-container {
  display: flex;
  align-items: center;
  gap: 12px;
}

/**
 * カラーピッカーのテキスト表示
 */
.color-picker-text {
  color: #606266;
  font-size: 14px;
}

/**
 * フォームのアクションボタンのスタイル
 */
.form-actions {
  display: flex;
  justify-content: center;
  width: 100%;
}
</style> 