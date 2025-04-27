<template>
  <el-container class="base bg-white">
    <!-- ヘッダー -->
    <el-header class="pt-2">
      <el-page-header icon="" title="Back" color="black" @back="$router.back()">
        <template #content>
          <div class="flex items-center">
            <span class="text-large font-600 mr-3"> タスク管理 </span>
          </div>
        </template>
        <template #extra>
          <div class="flex items-center">
            <el-button type="primary" class="ml-2" @click="dialogVisible = true">Create</el-button>
            <el-dialog
              v-model="dialogVisible"
              title="新しいタスク"
              width="500"
              :before-close="handleClose"
            >
              <el-form ref="ruleFormRef" :model="inputTask" :rules="rules" label-width="auto">
                <div>
                  <el-form-item label="タイトル" prop="title">
                    <el-input v-model="inputTask.title" type="text" maxlength="50" show-word-limit placeholder="タスクのタイトルを入力" />
                  </el-form-item>
                </div>
                <div>
                  <el-form-item label="説明">
                    <el-input v-model="inputTask.description" type="textarea" maxlength="1000" show-word-limit placeholder="タスクの説明（オプション）" />
                  </el-form-item>
                </div>
                <div>
                  <el-form-item label="優先度">
                    <el-select v-model="inputTask.priority" placeholder="優先度を選択">
                      <!-- TODO: 優先度の一覧をDBから取得する --> 
                      <el-option label="低" :value=1 />
                      <el-option label="中" :value=2 />
                      <el-option label="高" :value=3 />
                    </el-select>
                  </el-form-item>
                  <el-form-item label="ステータス">
                    <el-select v-model="inputTask.status" placeholder="ステータスを選択">
                      <!-- TODO: ステータスの一覧をDBから取得する --> 
                      <el-option label="未着手" :value=1 />
                      <el-option label="進行中" :value=2 />
                      <el-option label="完了" :value=3 />
                    </el-select>
                  </el-form-item>
                </div>
                <div>
                  <el-form-item>
                    <el-button type="primary" @click="onSave(ruleFormRef)">登録</el-button>
                  </el-form-item>
                </div>
              </el-form>
            </el-dialog>
          </div>
        </template>
      </el-page-header>
    </el-header>
    <!-- メイン -->
    <el-main>
      <el-form>
        <el-form-item>
          <el-input v-model="searchWord" :prefix-icon="Search" placeholder="タスクを検索"></el-input>
        </el-form-item>
        <el-form-item>
          <!-- FIXME:リアルタイム検索にする？ -->
          <el-button type="primary" @click="onSearch">検索</el-button>
        </el-form-item>
      </el-form>
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
          <el-tab-pane label="未着手" name="pending"></el-tab-pane>
          <el-tab-pane label="進行中" name="progress"></el-tab-pane>
          <el-tab-pane label="完了" name="complete"></el-tab-pane>
       </el-tabs>
    </el-main>
    <el-footer>

    </el-footer>
  </el-container>
</template>
<script setup lang="ts">
  import { Search, Edit, Delete } from '@element-plus/icons-vue'
  import type { FormInstance, FormRules } from 'element-plus'

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

  /** 検索 */
  function onSearch() {
    
  }

  type  RuleForm = {
    title: string;
    description: string;
    priority: number;
    status: number;
  }

  const ruleFormRef = ref<FormInstance>()

  const inputTask = reactive<RuleForm>({
    title: '',
    description: '',
    priority: 2,
    status: 1,
  });

  /** バリデーションルール */
  const rules = reactive<FormRules<RuleForm>>({
    title: [{ required: true, message: 'タイトルを入力してください', trigger: 'blur' }],
  });

  const dialogVisible = ref(false)
  // モーダルを閉じたときに呼ばれる
  const handleClose = (done: () => void) => {
    ElMessageBox.confirm('閉じると入力した内容が消えますがよろしいでしょうか?')
      .then(() => {
        done()
      })
      .catch(() => {
        // catch error
      })
  }

  /** タスク登録 */
  async function createTask() {
    await useFetch('http://localhost:9000/api/tasks/store', {
      method: 'POST',
      body: JSON.stringify(inputTask)
    })
  }

  const onSave = async (formEl: FormInstance | undefined) => {
  if (!formEl) return
  await formEl.validate(async (valid, fields) => {
    if (valid) {
      await createTask()
    } else {
      console.log('error submit!', fields)
    }
  })
}
</script>
<style scoped lang="css">
  .base {
    min-height: 100vh;
  }
</style>