<template>
  <div class="combo-tree">
    <div class="move-buttons">
      <!-- 技ボタン一覧 -->
      <div v-for="move in moves" :key="move.id" class="move-button"
           @click="addMoveToCombo(move)">
        {{ move.name }}
        <span class="command">{{ move.command }}</span>
      </div>
    </div>

    <div class="tree-view">
      <!-- ツリー表示 -->
      <div v-for="node in treeNodes" :key="node.id" 
           :style="{ marginLeft: `${node.level * 20}px` }"
           class="tree-node">
        <div class="node-content">
          {{ node.move.name }}
          <span class="command">{{ node.move.command }}</span>
          <button @click="removeNode(node.id)">削除</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
interface Move {
  id: number;
  name: string;
  command: string;
  moveType: string;
  damage: number;
}

interface TreeNode {
  id: number;
  move: Move;
  parentId: number | null;
  level: number;
}

const moves = ref<Move[]>([]);
const treeNodes = ref<TreeNode[]>([]);
const selectedNodeId = ref<number | null>(null);

// 技を取得
const fetchMoves = async () => {
  const { data } = await useFetch('/api/moves');
  moves.value = data.value as Move[];
};

// 技をコンボに追加
const addMoveToCombo = (move: Move) => {
  const newNode: TreeNode = {
    id: Date.now(), // 仮のID
    move,
    parentId: selectedNodeId.value,
    level: selectedNodeId.value ? getNodeLevel(selectedNodeId.value) + 1 : 0
  };
  treeNodes.value.push(newNode);
};

// ノードの階層レベルを取得
const getNodeLevel = (nodeId: number): number => {
  const node = treeNodes.value.find(n => n.id === nodeId);
  if (!node || !node.parentId) return 0;
  return getNodeLevel(node.parentId) + 1;
};

// ノードを削除
const removeNode = (nodeId: number) => {
  const index = treeNodes.value.findIndex(n => n.id === nodeId);
  if (index !== -1) {
    treeNodes.value.splice(index, 1);
    // 子ノードも削除
    treeNodes.value = treeNodes.value.filter(n => n.parentId !== nodeId);
  }
};

onMounted(() => {
  fetchMoves();
});
</script>

<style scoped>
.combo-tree {
  display: flex;
  gap: 2rem;
}

.move-buttons {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
  gap: 0.5rem;
}

.move-button {
  padding: 0.5rem;
  border: 1px solid #ccc;
  border-radius: 4px;
  cursor: pointer;
}

.tree-view {
  flex-grow: 1;
}

.tree-node {
  margin: 0.5rem 0;
  padding: 0.5rem;
  border: 1px solid #ccc;
  border-radius: 4px;
}

.command {
  color: #666;
  font-size: 0.8em;
  margin-left: 0.5rem;
}
</style> 