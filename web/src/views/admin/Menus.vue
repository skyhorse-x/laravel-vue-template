<script setup>
import { ref, onMounted, computed } from 'vue'
import api from '@/api'
import { ElTable, ElTableColumn, ElButton, ElDialog, ElForm, ElFormItem, ElInput, ElMessage, ElSelect, ElOption as ElSelectOption, ElTree } from 'element-plus'

const menus = ref([])
const dialogVisible = ref(false)
const editForm = ref({ id: '', name: '', route: '', icon: '', parent_id: 0, sort: 0, status: 1, module: '' })

const parentMenus = computed(() => {
  return [{ id: 0, name: '顶级菜单' }, ...menus.value.filter(m => m.parent_id === 0)]
})

const treeData = computed(() => {
  const topMenus = menus.value.filter(m => m.parent_id === 0).sort((a, b) => a.sort - b.sort)
  return topMenus.map(m => ({
    id: m.id,
    label: m.name,
    route: m.route,
    isParent: true
  }))
})

const tableData = computed(() => {
  const result = []
  const topMenus = menus.value.filter(m => m.parent_id === 0).sort((a, b) => a.sort - b.sort)
  for (const menu of topMenus) {
    result.push({ ...menu, isParent: true })
    const children = menus.value.filter(m => m.parent_id === menu.id).sort((a, b) => a.sort - b.sort)
    children.forEach(child => result.push({ ...child, isParent: false, parentName: menu.name }))
  }
  return result
})

onMounted(async () => { await loadMenus() })

async function loadMenus() {
  const res = await api.menus.all()
  if (res.code === 200) { menus.value = res.data }
}

function openAddDialog(parentId = 0) {
  editForm.value = { id: '', name: '', route: '', icon: '', parent_id: parentId, sort: 0, status: 1, module: '' }
  dialogVisible.value = true
}

function openEditDialog(menu) {
  editForm.value = {
    id: menu.id, name: menu.name, route: menu.route || '', icon: menu.icon || '',
    parent_id: menu.parent_id || 0, sort: menu.sort, status: menu.status, module: menu.module || ''
  }
  dialogVisible.value = true
}

async function saveMenu() {
  if (!editForm.value.name) { ElMessage.warning('请输入菜单名称'); return }
  let res = editForm.value.id ? await api.menus.update(editForm.value.id, editForm.value) : await api.menus.create(editForm.value)
  if (res.code === 200) { ElMessage.success(editForm.value.id ? '更新成功' : '创建成功'); dialogVisible.value = false; loadMenus() }
  else { ElMessage.error(res.message) }
}

async function deleteMenu(id) {
  const hasChildren = menus.value.some(m => m.parent_id === id)
  if (hasChildren) { ElMessage.warning('请先删除子菜单'); return }
  const res = await api.menus.delete(id)
  if (res.code === 200) { ElMessage.success('删除成功'); loadMenus() } else { ElMessage.error(res.message) }
}
</script>

<template>
  <div class="page-container">
    <div class="page-header">
      <h2 class="page-title">
        菜单管理
      </h2>
      <div style="display: flex; gap: 8px;">
        <ElButton type="primary" @click="openAddDialog(0)">
          新增一级菜单
        </ElButton>
      </div>
    </div>

    <div class="grid-layout">
      <div class="card tree-card">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
          <h3 class="card-subtitle" style="margin: 0;">
            菜单结构
          </h3>
        </div>
        <ElTree :data="treeData" node-key="id" default-expand-all>
          <template #default="{ data }">
            <div style="display: flex; align-items: center; gap: 6px; font-size: 0.85rem;">
              <span style="font-weight: 600;">{{ data.label }}</span>
            </div>
          </template>
        </ElTree>
        <div v-if="treeData.length === 0" style="color: #94a3b8; font-size: 0.85rem; text-align: center; padding: 20px 0;">
          暂无菜单数据
        </div>
      </div>

      <div class="card table-card">
        <ElTable
          :data="tableData"
          stripe
          row-key="id"
          :tree-props="{ children: 'children' }"
        >
          <ElTableColumn prop="name" label="菜单名称" min-width="160">
            <template #default="scope">
              <span :style="{ fontWeight: scope.row.isParent ? 600 : 400, paddingLeft: scope.row.isParent ? 0 : '24px' }">
                {{ scope.row.name }}
              </span>
            </template>
          </ElTableColumn>
          <ElTableColumn prop="route" label="路由" min-width="140">
            <template #default="scope">
              <span style="color: #6366f1; font-size: 0.8rem;">{{ scope.row.route || '-' }}</span>
            </template>
          </ElTableColumn>
          <ElTableColumn prop="icon" label="图标" width="80">
            <template #default="scope">
              <span style="color: #94a3b8; font-size: 0.8rem;">{{ scope.row.icon || '-' }}</span>
            </template>
          </ElTableColumn>
          <ElTableColumn
            prop="sort"
            label="排序"
            width="60"
            align="center"
          />
          <ElTableColumn
            prop="status"
            label="状态"
            width="70"
            align="center"
          >
            <template #default="scope">
              <span class="status-tag" :class="scope.row.status === 1 ? 'active' : 'disabled'">{{ scope.row.status === 1 ? '启用' : '禁用' }}</span>
            </template>
          </ElTableColumn>
          <ElTableColumn label="操作" width="160" fixed="right">
            <template #default="scope">
              <template v-if="scope.row.isParent">
                <ElButton
                  link
                  type="primary"
                  size="small"
                  @click="openAddDialog(scope.row.id)"
                >
                  添加子菜单
                </ElButton>
              </template>
              <ElButton
                link
                type="primary"
                size="small"
                @click="openEditDialog(scope.row)"
              >
                编辑
              </ElButton>
              <ElButton
                link
                type="danger"
                size="small"
                @click="deleteMenu(scope.row.id)"
              >
                删除
              </ElButton>
            </template>
          </ElTableColumn>
        </ElTable>
      </div>
    </div>

    <ElDialog
      v-model="dialogVisible"
      :title="editForm.id ? '编辑菜单' : '新增菜单'"
      width="480px"
      @close="dialogVisible = false"
    >
      <ElForm :model="editForm" label-width="80px">
        <ElFormItem label="名称">
          <ElInput v-model="editForm.name" placeholder="请输入菜单名称" />
        </ElFormItem>
        <ElFormItem label="父级">
          <ElSelect v-model="editForm.parent_id" style="width: 100%;">
            <ElSelectOption
              v-for="menu in parentMenus"
              :key="menu.id"
              :value="menu.id"
              :label="menu.name"
            />
          </ElSelect>
        </ElFormItem>
        <ElFormItem label="路由">
          <ElInput v-model="editForm.route" placeholder="如 /admin/users（无子菜单时必填）" />
        </ElFormItem>
        <ElFormItem label="图标">
          <ElInput v-model="editForm.icon" placeholder="如 Dashboard, Users, Article, Setting, Financial" />
        </ElFormItem>
        <ElFormItem label="排序">
          <ElInput v-model.number="editForm.sort" type="number" placeholder="数值越小越靠前" />
        </ElFormItem>
        <ElFormItem label="模块">
          <ElInput v-model="editForm.module" placeholder="模块标识" />
        </ElFormItem>
        <ElFormItem label="状态">
          <ElSelect v-model="editForm.status">
            <ElSelectOption :value="1" label="启用" />
            <ElSelectOption :value="0" label="禁用" />
          </ElSelect>
        </ElFormItem>
      </ElForm>
      <template #footer>
        <ElButton @click="dialogVisible = false">
          取消
        </ElButton>
        <ElButton type="primary" @click="saveMenu">
          保存
        </ElButton>
      </template>
    </ElDialog>
  </div>
</template>

<style scoped>
.page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; }
.page-title { font-size: 1.1rem; font-weight: 700; color: #1e293b; }
.grid-layout { display: grid; grid-template-columns: 280px 1fr; gap: 16px; }
.card { background: white; border-radius: 12px; padding: 20px; border: 1px solid #f1f5f9; }
.card-subtitle { font-size: 0.9rem; font-weight: 600; color: #475569; margin-bottom: 12px; }
.tree-card { align-self: start; }
.status-tag { display: inline-block; padding: 2px 10px; border-radius: 10px; font-size: 0.75rem; font-weight: 500; }
.status-tag.active { background: #ecfdf5; color: #10b981; }
.status-tag.disabled { background: #fef2f2; color: #ef4444; }

@media (max-width: 768px) {
  .grid-layout { grid-template-columns: 1fr; }
}
</style>
