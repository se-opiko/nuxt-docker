<template>
  <div>
    <el-container>
      <el-header class="header">
        <h3 class="title">
          HUB
        </h3>
      </el-header>
      <el-main class="main">
        <h3>アプリコレクション</h3>
        <el-row :gutter="20">
          <el-col :span="6">
            <el-card shadow="hover" @click="openAppCollection">
              <!-- <el-image src="/images/sf6-combo.png" /> -->
              <template #footer>
                <h4>ストリートファイター6 コンボツリー</h4>
              </template>
            </el-card>
          </el-col>
          <el-col :span="6">
            <el-card shadow="hover" @click="$router.push('/todo')">
              <el-image>
                <template #error>
                  <div class="image-slot">
                    <el-icon><icon-picture /></el-icon>
                  </div>
                </template>
              </el-image>
              <template #footer>
                <h4>アプリ２</h4>
              </template>
            </el-card>
          </el-col>
          <el-col :span="6">
            <el-card shadow="hover">
              <el-image>
                <template #error>
                  <div class="image-slot">
                    <el-icon><icon-picture /></el-icon>
                  </div>
                </template>
              </el-image>
              <template #footer>
                <h4>アプリ３</h4>
              </template>
            </el-card>
          </el-col>
          <el-col :span="6">
            <el-card shadow="hover">
              <el-image>
                <template #error>
                  <div class="image-slot">
                    <el-icon><icon-picture /></el-icon>
                  </div>
                </template>
              </el-image>
              <template #footer>
                <h4>アプリ４</h4>
              </template>
            </el-card>
          </el-col>
        </el-row>
      </el-main>
      <el-footer class="footer" align="middle">
        <span>© 2025 HUB</span>
      </el-footer>
    </el-container>
  </div>
</template>
<script setup lang="ts">
import { Picture as IconPicture } from '@element-plus/icons-vue'
/* eslint-disable */
const router = useRouter();

function openAppCollection() {
  router.push('/sf6-combo');
}
</script>
<style scoped>
.header {
  background-color: #27224d;
}
.header .title {
  color: #e6492e;
}
.main {
  background-color: #1a1833;
  min-height: calc(100vh - 120px); /* ヘッダーとフッターの高さを引いた値 */
}
.footer {
  background-color: #27224d;
  position: fixed;
  bottom: 0;
  width: 100%;
  z-index: 100;
}
</style>
