<script setup>
import { ref, onMounted } from 'vue'
import api from '@/api'
import { ElTable, ElTableColumn, ElPagination, ElButton, ElDialog, ElForm, ElFormItem, ElInput, ElMessage, ElSelect, ElOption as ElSelectOption } from 'element-plus'

const admins = ref([])
const total = ref(0)
const page = ref(1)
const perPage = 10
const roles = ref([])
const dialogVisible = ref(false)
const editForm = ref({ id: '', username: '', name: '', email: '', role_id: 2, status: 1, password: '' })

onMounted(async () => { await loadAdmins(); await loadRoles() })

async function loadAdmins() {
  const res = await api.admins.list({ page: page.value, per_page: perPage })
  if (res.code === 200) { admins.value = res.data.data; total.value = res.data.total }
}

async function loadRoles() {
  const res = await api.roles.list()
  if (res.code === 200) roles.value = res.data.data
}

function handlePageChange(val) { page.value = val; loadAdmins() }

function openAddDialog() {
  editForm.value = { id: '', username: '', name: '', email: '', role_id: 2, status: 1, password: '' }
  dialogVisible.value = true
}

function openEditDialog(admin) {
  editForm.value = { id: admin.id, username: admin.username, name: admin.name, email: admin.email, role_id: admin.role_id, status: admin.status, password: '' }
  dialogVisible.value = true
}

async function saveAdmin() {
  let res = editForm.value.id ? await api.admins.update(editForm.value.id, editForm.value) : await api.admins.create(editForm.value)
  if (res.code === 200) { ElMessage.success(editForm.value.id ? '更新成功' : '创建成功'); dialogVisible.value = false; loadAdmins() }
  else { ElMessage.error(res.message) }
}

async function deleteAdmin(id) {
  const res = await api.admins.delete(id)
  if (res.code === 200) { ElMessage.success('删除成功'); loadAdmins() } else { ElMessage.error(res.message) }
}
</script>

<template>
  <div class="page-container">
    <div class="page-header">
      <h2 class="page-title">
        管理员管理
      </h2>
      <ElButton type="primary" @click="openAddDialog">
        新增管理员
      </ElButton>
    </div>

    <div class="card">
      <ElTable :data="admins" stripe>
        <ElTableColumn prop="id" label="ID" width="70" />
        <ElTableColumn prop="username" label="用户名" min-width="100" />
        <ElTableColumn prop="name" label="姓名" min-width="100" />
        <ElTableColumn prop="email" label="邮箱" min-width="180" />
        <ElTableColumn prop="role.name" label="角色" min-width="100" />
        <ElTableColumn prop="status" label="状态" width="80">
          <template #default="scope">
            <span class="status-tag" :class="scope.row.status === 1 ? 'active' : 'disabled'">{{ scope.row.status === 1 ? '正常' : '禁用' }}</span>
          </template>
        </ElTableColumn>
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
              @click="deleteAdmin(scope.row.id)"
            >
              删除
            </ElButton>
          </template>
        </ElTableColumn>
      </ElTable>

      <div class="pagination-wrap">
        <ElPagination
          v-model:current-page="page"
          :page-size="perPage"
          :total="total"
          layout="total, prev, pager, next"
          size="small"
          @current-change="handlePageChange"
        />
      </div>
    </div>

    <ElDialog
      :visible="dialogVisible"
      :title="editForm.id ? '编辑管理员' : '新增管理员'"
      width="480px"
      @close="dialogVisible = false"
    >
      <ElForm :model="editForm" label-width="80px">
        <ElFormItem label="用户名">
          <ElInput v-model="editForm.username" placeholder="请输入用户名" :disabled="!!editForm.id" />
        </ElFormItem>
        <ElFormItem label="姓名">
          <ElInput v-model="editForm.name" placeholder="请输入姓名" />
        </ElFormItem>
        <ElFormItem label="邮箱">
          <ElInput v-model="editForm.email" type="email" placeholder="请输入邮箱" />
        </ElFormItem>
        <ElFormItem label="角色">
          <ElSelect v-model="editForm.role_id">
            <ElSelectOption
              v-for="role in roles"
              :key="role.id"
              :value="role.id"
              :label="role.name"
            />
          </ElSelect>
        </ElFormItem>
        <ElFormItem label="密码">
          <ElInput v-model="editForm.password" type="password" :placeholder="editForm.id ? '不填则不修改' : '请输入密码'" />
        </ElFormItem>
        <ElFormItem label="状态">
          <ElSelect v-model="editForm.status">
            <ElSelectOption :value="1" label="正常" />
            <ElSelectOption :value="0" label="禁用" />
          </ElSelect>
        </ElFormItem>
      </ElForm>
      <template #footer>
        <ElButton @click="dialogVisible = false">
          取消
        </ElButton>
        <ElButton type="primary" @click="saveAdmin">
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
.pagination-wrap { display: flex; justify-content: flex-end; margin-top: 16px; }
</style>
