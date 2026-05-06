<script setup>
import { ref, onMounted } from 'vue'
import api from '@/api'
import { ElMessage, ElMessageBox, ElSelect, ElOption as ElSelectOption } from 'element-plus'

const records = ref([])
const total = ref(0)
const page = ref(1)
const perPage = 20
const loading = ref(false)
const dialogVisible = ref(false)
const editForm = ref({ ip: '', type: 'block', reason: '', expired_days: '' })
const isEdit = ref(false)
const searchForm = ref({ type: '', ip: '' })

onMounted(async () => { await loadData() })

async function loadData() {
  loading.value = true
  const res = await api.admin.ipBlacklist.list({ page: page.value, per_page: perPage, ...searchForm.value })
  if (res.code === 200) {
    records.value = res.data.data
    total.value = res.data.total
  }
  loading.value = false
}

function handlePageChange(val) {
  page.value = val
  loadData()
}

function search() {
  page.value = 1
  loadData()
}

function resetSearch() {
  searchForm.value = { type: '', ip: '' }
  page.value = 1
  loadData()
}

function openAddDialog() {
  editForm.value = { id: '', ip: '', type: 'block', reason: '', expired_days: '' }
  isEdit.value = false
  dialogVisible.value = true
}

function openEditDialog(record) {
  editForm.value = {
    id: record.id,
    ip: record.ip,
    type: record.type,
    reason: record.reason || '',
    expired_days: record.expired_days || ''
  }
  isEdit.value = true
  dialogVisible.value = true
}

async function saveRecord() {
  if (!editForm.value.ip) {
    ElMessage.warning('请输入IP地址')
    return
  }
  const data = {
    ip: editForm.value.ip,
    type: editForm.value.type,
    reason: editForm.value.reason,
    expired_days: editForm.value.expired_days || null
  }
  let res
  if (isEdit.value) {
    res = await api.admin.ipBlacklist.update(editForm.value.id, data)
  } else {
    res = await api.admin.ipBlacklist.create(data)
  }
  if (res.code === 200) {
    ElMessage.success(isEdit.value ? '更新成功' : '添加成功')
    dialogVisible.value = false
    loadData()
  } else {
    ElMessage.error(res.message || '操作失败')
  }
}

async function deleteRecord(id) {
  try {
    await ElMessageBox.confirm('确定要删除这条记录吗？', '提示', { type: 'warning' })
    const res = await api.admin.ipBlacklist.delete(id)
    if (res.code === 200) {
      ElMessage.success('删除成功')
      loadData()
    }
  } catch (error) {
    if (error !== 'cancel') {
      console.error(error)
    }
  }
}

async function batchDelete() {
  const selected = records.value.filter(r => r.checked)
  if (selected.length === 0) {
    ElMessage.warning('请选择要删除的记录')
    return
  }
  try {
    await ElMessageBox.confirm(`确定要删除选中的 ${selected.length} 条记录吗？`, '提示', { type: 'warning' })
    const res = await api.admin.ipBlacklist.batchDelete({ ids: selected.map(r => r.id) })
    if (res.code === 200) {
      ElMessage.success('删除成功')
      loadData()
    }
  } catch (error) {
    if (error !== 'cancel') {
      console.error(error)
    }
  }
}

function formatDate(date) {
  if (!date) return '永久'
  return new Date(date).toLocaleString('zh-CN')
}

function getTypeName(type) {
  const map = { block: '内容封禁', login: '登录限制' }
  return map[type] || type
}

function getTypeTagClass(type) {
  const map = { block: 'danger', login: 'warning' }
  return map[type] || ''
}
</script>

<template>
  <div class="page-container">
    <div class="page-header">
      <h2 class="page-title">
        IP黑名单
      </h2>
      <div style="display: flex; gap: 8px;">
        <ElButton type="primary" @click="openAddDialog">
          添加IP
        </ElButton>
        <ElButton type="danger" @click="batchDelete">
          批量删除
        </ElButton>
      </div>
    </div>

    <div class="search-bar">
      <ElSelect
        v-model="searchForm.type"
        placeholder="类型"
        clearable
        style="width: 120px"
      >
        <ElSelectOption value="block" label="内容封禁" />
        <ElSelectOption value="login" label="登录限制" />
      </ElSelect>
      <ElInput
        v-model="searchForm.ip"
        placeholder="IP地址"
        clearable
        style="width: 160px"
      />
      <ElButton type="primary" @click="search">
        搜索
      </ElButton>
      <ElButton @click="resetSearch">
        重置
      </ElButton>
    </div>

    <div class="card">
      <ElTable
        v-loading="loading"
        :data="records"
        stripe
        row-key="id"
      >
        <ElTableColumn type="selection" width="40" />
        <ElTableColumn prop="id" label="ID" width="60" />
        <ElTableColumn prop="ip" label="IP地址" width="160" />
        <ElTableColumn prop="type" label="类型" width="100">
          <template #default="scope">
            <ElTag :type="getTypeTagClass(scope.row.type)" size="small">
              {{ getTypeName(scope.row.type) }}
            </ElTag>
          </template>
        </ElTableColumn>
        <ElTableColumn
          prop="reason"
          label="原因"
          min-width="150"
          show-overflow-tooltip
        />
        <ElTableColumn prop="expired_at" label="过期时间" width="180">
          <template #default="scope">
            {{ formatDate(scope.row.expired_at) }}
          </template>
        </ElTableColumn>
        <ElTableColumn prop="operator_name" label="操作人" width="100" />
        <ElTableColumn prop="created_at" label="添加时间" width="160">
          <template #default="scope">
            {{ formatDate(scope.row.created_at) }}
          </template>
        </ElTableColumn>
        <ElTableColumn label="操作" width="120" fixed="right">
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
              @click="deleteRecord(scope.row.id)"
            >
              删除
            </ElButton>
          </template>
        </ElTableColumn>
      </ElTable>

      <div v-if="total > perPage" class="pagination">
        <ElPagination
          v-model:current-page="page"
          :page-size="perPage"
          :total="total"
          layout="prev, pager, next"
          @current-change="handlePageChange"
        />
      </div>
    </div>

    <ElDialog v-model="dialogVisible" :title="isEdit ? '编辑IP' : '添加IP'" width="450px">
      <ElForm :model="editForm" label-width="80px">
        <ElFormItem label="IP地址">
          <ElInput v-model="editForm.ip" placeholder="如: 192.168.1.1 或 192.168.1.*" />
        </ElFormItem>
        <ElFormItem label="类型">
          <ElSelect v-model="editForm.type" style="width: 100%;">
            <ElSelectOption value="block" label="内容封禁" />
            <ElSelectOption value="login" label="登录限制" />
          </ElSelect>
        </ElFormItem>
        <ElFormItem label="封禁原因">
          <ElInput
            v-model="editForm.reason"
            type="textarea"
            rows="2"
            placeholder="请输入封禁原因"
          />
        </ElFormItem>
        <ElFormItem label="过期天数">
          <ElInput v-model.number="editForm.expired_days" type="number" placeholder="留空表示永久" />
        </ElFormItem>
      </ElForm>
      <template #footer>
        <ElButton @click="dialogVisible = false">
          取消
        </ElButton>
        <ElButton type="primary" @click="saveRecord">
          保存
        </ElButton>
      </template>
    </ElDialog>
  </div>
</template>

<style scoped>
.page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; }
.page-title { font-size: 1.1rem; font-weight: 700; color: var(--color-text-primary); margin: 0; }
.search-bar { display: flex; gap: 8px; margin-bottom: 16px; flex-wrap: wrap; align-items: center; }
.card { background: var(--bg-card); border-radius: 12px; padding: 20px; border: 1px solid var(--border-light); }
.pagination { display: flex; justify-content: center; margin-top: 16px; }
</style>
