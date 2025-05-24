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
        <el-button 
          type="primary" 
          @click="onSave(ruleFormRef)"
        >
          {{ saveButtonText }}
        </el-button>
      </el-form-item>
    </el-form>
  </el-dialog>
</template>
<script setup lang="ts">
import { ref } from 'vue'
import type { Task, RuleForm } from '@/types/todo'
import type { FormInstance, FormRules } from 'element-plus'

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

const inputTask = reactive<RuleForm>({
  title: props.task?.title || '',
  description: props.task?.description || '',
  priority: props.task?.priority || 2,
  status: props.task?.status || 1,
})

const clearInputTask = () => {
  inputTask.title = props.task?.title || ''
  inputTask.description = props.task?.description || ''
  inputTask.priority = props.task?.priority || 2
  inputTask.status = props.task?.status || 1
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

const onSave = async (formEl: FormInstance | undefined) => {
  if (!formEl) return
  await formEl.validate(async (valid, fields) => {
    if (valid) {
      try {
        // emitの代わりに、親コンポーネントから渡された関数を直接呼び出す
        await props.onSave(inputTask)
        ElMessage.success(`タスクを${props.saveButtonText}しました`)
        emit('update:modelValue', false)
        clearInputTask()
      } catch (error) {
        ElMessage.error(`タスクの${props.saveButtonText}に失敗しました`)
      }
    } else {
      console.log('error submit!', fields)
    }
  })
}
</script>