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
      :model="inputTask" 
      :rules="rules" 
      label-width="auto"
    >
      <el-form-item label="タイトル" prop="title">
        <el-input 
          v-model="inputTask.title" 
          type="text" 
          maxlength="50" 
          show-word-limit 
          placeholder="タスクのタイトルを入力" 
        />
      </el-form-item>
  
      <el-form-item label="説明">
        <el-input 
          v-model="inputTask.description" 
          type="textarea" 
          maxlength="1000" 
          show-word-limit 
          placeholder="タスクの説明（オプション）" 
        />
      </el-form-item>

      <el-form-item label="プロジェクト">
        <el-select 
          :model-value="projectIdValue" 
          @update:model-value="updateProjectId"
          placeholder="プロジェクトを選択（任意）"
          clearable
        >
          <el-option label="未分類" :value="''" />
          <el-option 
            v-for="project in projects" 
            :key="project.id" 
            :label="project.name" 
            :value="project.id.toString()" 
          />
        </el-select>
      </el-form-item>
  
      <el-form-item label="優先度">
        <el-select 
          v-model="inputTask.priority" 
          placeholder="優先度を選択"
        >
          <!-- TODO: 優先度の一覧をDBから取得する --> 
          <el-option label="低" :value="1" />
          <el-option label="中" :value="2" />
          <el-option label="高" :value="3" />
        </el-select>
      </el-form-item>
  
      <el-form-item label="ステータス">
        <el-select 
          v-model="inputTask.status" 
          placeholder="ステータスを選択"
        >
          <!-- TODO: ステータスの一覧をDBから取得する --> 
          <el-option label="未着手" :value="1" />
          <el-option label="進行中" :value="2" />
          <el-option label="完了" :value="3" />
        </el-select>
      </el-form-item>
  
      <el-form-item>
        <div style="display: flex; justify-content: center; width: 100%;">
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
import { ref, watch, computed } from 'vue'
import type { Task, RuleForm } from '@/types/todo'
import type { FormInstance, FormRules } from 'element-plus'
import { useProjects } from '@/composables/useProjects'

const props = defineProps({
  modelValue: {
    type: Boolean,
    required: true
  },
  title: {
    type: String,
    default: 'タスクを保存する'
  },
  saveButtonText: {
    type: String,
    default: '保存'
  },
  task: {
    type: Object as PropType<Task>,
    default: () => ({
      id: 0,
      title: '',
      description: '',
      priority: 2,
      status: 1,
      project_id: null,
      updated_at: '',
      created_at: '',
    })
  },
  onSave: {
    type: Function,
    required: true
  },
})

const emit = defineEmits<{
  'update:modelValue': [value: boolean]
  'save': [task: RuleForm]
}>()

const ruleFormRef = ref<FormInstance>()
const { projects, fetchProjects } = useProjects()

/**
 * モーダルが開かれた時のみプロジェクト一覧を取得
 */
watch(() => props.modelValue, async (newValue) => {
  if (newValue && projects.value.length === 0) {
    console.log('モーダルが開かれたため、プロジェクト一覧を取得します')
    await fetchProjects()
  }
})

/**
 * TaskのProject_idをフォーム用に変換する
 * nullをundefinedに変換してElement Plusで使いやすくする
 */
const convertTaskProjectIdForForm = (projectId: number | null): number | undefined => {
  return projectId === null ? undefined : projectId
}

const inputTask = reactive<RuleForm>({
  title: props.task?.title || '',
  description: props.task?.description || '',
  priority: props.task?.priority || 2,
  status: props.task?.status || 1,
  project_id: convertTaskProjectIdForForm(props.task?.project_id),
})

/**
 * Element Plus用のproject_id表示値
 */
const projectIdValue = computed(() => {
  return inputTask.project_id ? inputTask.project_id.toString() : ''
})

/**
 * プロジェクトIDの更新処理
 */
const updateProjectId = (value: string) => {
  inputTask.project_id = value === '' ? undefined : parseInt(value)
}

const clearInputTask = () => {
  inputTask.title = props.task?.title || ''
  inputTask.description = props.task?.description || ''
  inputTask.priority = props.task?.priority || 2
  inputTask.status = props.task?.status || 1
  inputTask.project_id = convertTaskProjectIdForForm(props.task?.project_id)
}

/** バリデーションルール */
const rules = reactive<FormRules<RuleForm>>({
  title: [{ required: true, message: 'タイトルを入力してください', trigger: 'blur' }],
});

// モーダルを閉じたときに呼ばれる
const handleClose = (done: () => void) => {
  ElMessageBox.confirm('閉じると入力した内容が消えますがよろしいでしょうか?')
    .then(() => {
      done()
      clearInputTask()
    })
    .catch(() => {
      // catch error
    })
}

// 保存処理
const onSave = async (formEl: FormInstance | undefined) => {
  if (!formEl) return
  
  await formEl.validate(async (valid: boolean) => {
    if (valid) {
      try {
        await props.onSave(inputTask)
        emit('update:modelValue', false)
        clearInputTask()
        ElMessage.success(props.saveButtonText + 'しました')
      } catch (error) {
        console.error(error)
        ElMessage.error(props.saveButtonText + 'に失敗しました')
      }
    }
  })
}

// taskプロパティの変更を監視してフォームを更新
watch(() => props.task, (newTask) => {
  if (newTask) {
    inputTask.title = newTask.title || ''
    inputTask.description = newTask.description || ''
    inputTask.priority = newTask.priority || 2
    inputTask.status = newTask.status || 1
    inputTask.project_id = convertTaskProjectIdForForm(newTask.project_id)
  }
}, { deep: true })
</script>