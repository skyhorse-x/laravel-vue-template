<script setup>
import { ref, onMounted } from 'vue'
import api from '@/api'
import { ElTable, ElTableColumn, ElPagination, ElButton, ElDialog, ElForm, ElFormItem, ElInput, ElMessage, ElSelect, ElOption as ElSelectOption } from 'element-plus'

const users = ref([])
const total = ref(0)
const page = ref(1)
const perPage = 10
const searchForm = ref({ email: '', name: '', status: '' })
const dialogVisible = ref(false)
const editForm = ref({ id: '', name: '', username: '', email: '', phone: '', status: 1, password: '' })
const selectedIds = ref([])

onMounted(async () => { await loadUsers() })

async function loadUsers() {
  const res = await api.users.list({ page: page.value, per_page: perPage, ...searchForm.value })
  if (res.code === 200) { users.value = res.data.data; total.value = res.data.total }
}

function handlePageChange(val) { page.value = val; loadUsers() }

function openAddDialog() {
  editForm.value = { id: '', name: '', username: '', email: '', phone: '', status: 1, password: '' }
  dialogVisible.value = true
}

function openEditDialog(user) {
  editForm.value = { id: user.id, name: user.name, username: user.username || '', email: user.email, phone: user.phone || '', status: user.status, password: '' }
  dialogVisible.value = true
}

async function saveUser() {
  let res = editForm.value.id ? await api.users.update(editForm.value.id, editForm.value) : await api.users.create(editForm.value)
  if (res.code === 200) { ElMessage.success(editForm.value.id ? '更新成功' : '创建成功'); dialogVisible.value = false; loadUsers() }
  else { ElMessage.error(res.message) }
}

async function deleteUser(id) {
  const res = await api.users.delete(id)
  if (res.code === 200) { ElMessage.success('删除成功'); loadUsers() } else { ElMessage.error(res.message) }
}

async function batchDelete() {
  if (selectedIds.value.length === 0) { ElMessage.warning('请选择要删除的用户'); return }
  const res = await api.users.batchDelete(selectedIds.value)
  if (res.code === 200) { ElMessage.success('批量删除成功'); selectedIds.value = []; loadUsers() } else { ElMessage.error(res.message) }
}

function handleSelectionChange(val) { selectedIds.value = val.map(item => item.id) }
function search() { page.value = 1; loadUsers() }
</script>

<template>
  <div class="page-container">
    <div class="page-header">
      <h2 class="page-title">
        用户管理
      </h2>
      <div class="page-actions">
        <ElButton type="primary" @click="openAddDialog">
          新增用户
        </ElButton>
        <ElButton
          type="danger"
          plain
          :disabled="selectedIds.length === 0"
          @click="batchDelete"
        >
          批量删除
        </ElButton>
      </div>
    </div>

    <div class="card">
      <div class="search-bar">
        <ElInput
          v-model="searchForm.email"
          placeholder="搜索邮箱"
          style="width: 180px;"
          @keyup.enter="search"
        />
        <ElInput
          v-model="searchForm.name"
          placeholder="搜索昵称"
          style="width: 180px;"
          @keyup.enter="search"
        />
        <ElSelect
          v-model="searchForm.status"
          placeholder="状态"
          style="width: 120px;"
          clearable
        >
          <ElSelectOption :value="1" label="正常" />
          <ElSelectOption :value="0" label="禁用" />
        </ElSelect>
        <ElButton type="primary" plain @click="search">
          搜索
        </ElButton>
      </div>

      <ElTable
        :data="users"
        :row-key="(row) => row.id"
        stripe
        @selection-change="handleSelectionChange"
      >
        <ElTableColumn type="selection" width="50" />
        <ElTableColumn prop="id" label="ID" width="70" />
        <ElTableColumn prop="username" label="用户名" min-width="100" />
        <ElTableColumn prop="name" label="昵称" min-width="100" />
        <ElTableColumn prop="email" label="邮箱" min-width="180" />
        <ElTableColumn prop="phone" label="手机号" min-width="130" />
        <ElTableColumn prop="status" label="状态" width="80">
          <template #default="scope">
            <span class="status-tag" :class="scope.row.status === 1 ? 'active' : 'disabled'">
              {{ scope.row.status === 1 ? '正常' : '禁用' }}
            </span>
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
              @click="deleteUser(scope.row.id)"
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
      v-model="dialogVisible"
      :title="editForm.id ? '编辑用户' : '新增用户'"
      width="480px"
      @close="dialogVisible = false"
    >
      <ElForm :model="editForm" label-width="80px">
        <ElFormItem label="用户名">
          <ElInput v-model="editForm.username" placeholder="请输入用户名（登录用）" />
        </ElFormItem>
        <ElFormItem label="昵称">
          <ElInput v-model="editForm.name" placeholder="请输入昵称" />
        </ElFormItem>
        <ElFormItem label="邮箱">
          <ElInput
            v-model="editForm.email"
            type="email"
            placeholder="请输入邮箱"
            :disabled="!!editForm.id"
          />
        </ElFormItem>
        <ElFormItem label="手机号">
          <ElInput v-model="editForm.phone" placeholder="请输入手机号" />
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
        <ElButton type="primary" @click="saveUser">
          保存
        </ElButton>
      </template>
    </ElDialog>
  </div>
</template>

<style scoped>
.page-container {}

.page-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 16px;
}

.page-title {
  font-size: 1.1rem;
  font-weight: 700;
  color: var(--color-text);
}

.page-actions {
  display: flex;
  gap: 8px;
}

.card {
  background: var(--color-card);
  border-radius: 12px;
  padding: 20px;
  border: 1px solid var(--border-light);
}

.search-bar {
  display: flex;
  gap: 10px;
  margin-bottom: 16px;
  flex-wrap: wrap;
}

.status-tag {
  display: inline-block;
  padding: 2px 10px;
  border-radius: 10px;
  font-size: 0.75rem;
  font-weight: 500;
}

.status-tag.active { background: #ecfdf5; color: #10b981; }
.status-tag.disabled { background: #fef2f2; color: #ef4444; }

.pagination-wrap {
  display: flex;
  justify-content: flex-end;
  margin-top: 16px;
}
</style>
