<template>
  <el-container class="base bg-white">
    <!-- ヘッダー -->
    <el-header class="p-2">
      <el-page-header :icon="ArrowLeft" color="black" title="トップに戻る" @back="$router.back()">
        <template #content>
          <span class="text-large font-600 mr-3 text-black"> タスク管理 </span>
        </template>
      </el-page-header>
    </el-header>
    <!-- メイン -->
    <el-main>
      <!-- TOOD:検索エリアを作成する -->
      <el-form>
        <el-form-item>
          <el-input v-model="searchWord" :prefix-icon="Search" placeholder="タスクを検索"></el-input>
        </el-form-item>
        <el-form-item>
          <!-- FIXME:リアルタイム検索にする？ -->
          <el-button type="primary" @click="onSubmit">検索</el-button>
        </el-form-item>
      </el-form>
      <!-- TODO:タブを追加する -->
       <el-tabs v-model="activeTab">
          <el-tab-pane label="すべて" name="all">
            <template v-for="task in tasks" :key="task.id">
              <el-card>
                <div>
                  <h3>{{ task.title }}</h3>
                  <p>{{ task.description }}</p>
                  <!-- 優先度 -->
                  <span>{{ task.priority }}</span>
                  <!-- 更新日 -->
                  <span>{{ task.updatedAt }}</span>
                </div>
                <div>
                  <el-button type="primary" icon="Edit" circle />
                  <el-button type="danger" icon="Delete" circle />
                </div>
              </el-card>
            </template>
          </el-tab-pane>
          <el-tab-pane label="進行中" name="progress"></el-tab-pane>
          <el-tab-pane label="完了" name="complete"></el-tab-pane>
       </el-tabs>
      <!-- TODO:カードのリストを追加する -->
    </el-main>
    <el-footer>

    </el-footer>
  </el-container>
</template>
<script setup lang="ts">
  import { ArrowLeft, Search, Edit, Delete } from '@element-plus/icons-vue'

  const searchWord = ref('');
  const activeTab = ref('all');
  type Task = {
    id: number;
    title: string;
    description: string;
    priority: number;
    status: number;
    updatedAt: string;
  }

  const tasks = ref<Task[]>([
    {
      id: 1,
      title: 'タイトル1',
      description: '説明文が入ります',
      priority: 1,
      status: 1,
      updatedAt: '2024-04-26'
    }
  ])

  function onSubmit() {
    
  }
</script>
<style lang="css">
  .base {
    min-height: 100vh;
  }
</style>