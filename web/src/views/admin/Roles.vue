<script setup>
import { ref, onMounted } from 'vue'
import api from '@/api'
import { ElTable, ElTableColumn, ElButton, ElDialog, ElForm, ElFormItem, ElInput, ElMessage, ElCheckbox, ElSelect, ElOption as ElSelectOption } from 'element-plus'

const roles = ref([])
const permissions = ref([])
const dialogVisible = ref(false)
const editForm = ref({ id: '', name: '', description: '', status: 1, permissions: [] })

onMounted(async () => { await loadRoles(); await loadPermissions() })

async function loadRoles() {
  const res = await api.roles.list()
  if (res.code === 200) roles.value = res.data.data
}

async function loadPermissions() {
  const res = await api.roles.permissions()
  if (res.code === 200) permissions.value = res.data
}

function openAddDialog() {
  editForm.value = { id: '', name: '', description: '', status: 1, permissions: [] }
  dialogVisible.value = true
}

function openEditDialog(role) {
  editForm.value = { id: role.id, name: role.name, description: role.description || '', status: role.status, permissions: role.permissions?.map(p => p.id) || [] }
  dialogVisible.value = true
}

async function saveRole() {
  let res = editForm.value.id ? await api.roles.update(editForm.value.id, editForm.value) : await api.roles.create(editForm.value)
  if (res.code === 200) { ElMessage.success(editForm.value.id ? '更新成功' : '创建成功'); dialogVisible.value = false; loadRoles() }
  else { ElMessage.error(res.message) }
}

async function deleteRole(id) {
  const res = await api.roles.delete(id)
  if (res.code === 200) { ElMessage.success('删除成功'); loadRoles() } else { ElMessage.error(res.message) }
}

const groupedPermissions = ref({})

function getGroupedPermissions() {
  const groups = {}
  permissions.value.forEach(p => {
    if (!groups[p.module]) groups[p.module] = []
    groups[p.module].push(p)
  })
  groupedPermissions.value = groups
  return groups
}
</script>

<template>
  <div class="page-container">
    <div class="page-header">
      <h2 class="page-title">
        角色管理
      </h2>
      <ElButton type="primary" @click="openAddDialog">
        新增角色
      </ElButton>
    </div>

    <div class="card">
      <ElTable :data="roles" stripe>
        <ElTableColumn prop="id" label="ID" width="70" />
        <ElTableColumn prop="name" label="角色名称" min-width="120" />
        <ElTableColumn prop="description" label="描述" min-width="180" />
        <ElTableColumn prop="status" label="状态" width="80">
          <template #default="scope">
            <span class="status-tag" :class="scope.row.status === 1 ? 'active' : 'disabled'">{{ scope.row.status === 1 ? '正常' : '禁用' }}</span>
          </template>
        </ElTableColumn>
        <ElTableColumn prop="permissions.length" label="权限数" width="80" />
        <ElTableColumn prop="created_at" label="创建时间" min-width="170" />
        <ElTableColumn label="操作" width="150" fixed="right">
          <template #default="scope">
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
              @click="deleteRole(scope.row.id)"
            >
              删除
            </ElButton>
          </template>
        </ElTableColumn>
      </ElTable>
    </div>

    <ElDialog
      v-model="dialogVisible"
      :title="editForm.id ? '编辑角色' : '新增角色'"
      width="500px"
    >
      <ElForm :model="editForm" label-width="80px">
        <ElFormItem label="名称">
          <ElInput v-model="editForm.name" placeholder="请输入角色名称" />
        </ElFormItem>
        <ElFormItem label="描述">
          <ElInput v-model="editForm.description" placeholder="请输入描述" />
        </ElFormItem>
        <ElFormItem label="状态">
          <ElSelect v-model="editForm.status">
            <ElSelectOption :value="1" label="正常" />
            <ElSelectOption :value="0" label="禁用" />
          </ElSelect>
        </ElFormItem>
        <ElFormItem label="权限">
          <div class="permission-groups">
            <div v-for="(items, module) in getGroupedPermissions()" :key="module" class="permission-group">
              <div class="group-title">
                {{ module }}
              </div>
              <div class="permission-items">
                <ElCheckbox
                  v-for="item in items"
                  :key="item.id"
                  v-model="editForm.permissions"
                  :label="item.display_name"
                  :value="item.id"
                />
              </div>
            </div>
          </div>
        </ElFormItem>
      </ElForm>
      <template #footer>
        <ElButton @click="dialogVisible = false">
          取消
        </ElButton>
        <ElButton type="primary" @click="saveRole">
          保存
        </ElButton>
      </template>
    </ElDialog>
  </div>
</template>

<style scoped>
.page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; }
.page-title { font-size: 1.1rem; font-weight: 700; color: #1e293b; }
.card { background: white; border-radius: 12px; padding: 20px; border: 1px solid #f1f5f9; }
.status-tag { display: inline-block; padding: 2px 10px; border-radius: 10px; font-size: 0.75rem; font-weight: 500; }
.status-tag.active { background: #ecfdf5; color: #10b981; }
.status-tag.disabled { background: #fef2f2; color: #ef4444; }
.permission-groups { max-height: 300px; overflow-y: auto; }
.permission-group { margin-bottom: 12px; }
.group-title { font-size: 0.8rem; font-weight: 600; color: #64748b; margin-bottom: 6px; }
.permission-items { display: flex; flex-wrap: wrap; gap: 8px; }
</style>
