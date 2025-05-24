<template>
  <el-dialog :model-value="modelValue" @update:model-value="$emit('update:modelValue', $event)"  width="30%">
    <template #header>
      <div class="text-lg font-bold">{{ title }}</div>
    </template>
    <div class="text-center">
      <p class="text-sm text-gray-500">{{ message }}</p>
    </div>
    <template #footer>
      <el-button type="danger" @click="handleCancel">
        {{ cancelButtonText }}
      </el-button>
      <el-button type="primary" @click="handleConfirm">
        {{ confirmButtonText }}
      </el-button>
    </template>
  </el-dialog>
</template>

<script setup lang="ts">
import { ref } from 'vue'

const props = defineProps({
  modelValue: {
    type: Boolean,
    required: true
  },
  title: {
    type: String,
    required: true
  },
  message: {
    type: String,
    required: true
  },
  confirmButtonText: {
    type: String,
    default: 'OK'
  },
  cancelButtonText: {
    type: String,
    default: 'キャンセル'
  }
})

const emit = defineEmits(['update:modelValue', 'confirm', 'cancel'])

const handleConfirm = () => {
  emit('update:modelValue', false)
  emit('confirm')
}

const handleCancel = () => {
  emit('update:modelValue', false)
  emit('cancel')
}
</script>