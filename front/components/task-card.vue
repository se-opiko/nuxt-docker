<template>
  <el-card shadow="hover">
    <div class="flex justify-between">
      <div>
        <h3>{{ task.title }}</h3>
        <p>{{ task.description }}</p>
        <!-- 優先度 -->
        <el-tag class="mr-4" :type="priority === '高' ? 'danger' : priority === '中' ? 'warning' : 'success'">{{ priority }}</el-tag>
        <!-- 更新日 -->
        <small>{{ formatDate(new Date(task.updated_at || task.created_at), 'YYYY/MM/DD') }}</small>
      </div>
      <div>
        <el-button type="primary" icon="Edit" circle />
        <el-button type="danger" icon="Delete" circle />
      </div>
    </div>
  </el-card>
</template>
<script setup lang="ts">
import type { PropType } from 'vue'
import { Edit, Delete } from '@element-plus/icons-vue'
import type { Task } from '@/types/todo'

const props = defineProps({
  task: {
    type: Object as PropType<Task>,
    required: true
  }
})

// 優先度の表示
const priority = computed(() => {
  return props.task.priority === 1 ? '高' : props.task.priority === 2 ? '中' : '低'
})
</script>