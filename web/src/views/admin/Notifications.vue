<script setup>
import { ref, onMounted } from 'vue'
import api from '@/api'
import { ElMessage, ElMessageBox, ElSelect, ElOption as ElSelectOption } from 'element-plus'

const notifications = ref([])
const total = ref(0)
const page = ref(1)
const perPage = 20
const loading = ref(false)
const dialogVisible = ref(false)
const isEdit = ref(false)
const sendForm = ref({ id: '', type: 'system', title: '', content: '', link: '', user_ids: [] })
const users = ref([])
const searchForm = ref({ type: '', keyword: '', user_id: '' })

onMounted(async () => { await loadData() })

async function loadData() {
  loading.value = true
  const res = await api.admin.notifications.list({ page: page.value, per_page: perPage, ...searchForm.value })
  if (res.code === 200) {
    notifications.value = res.data.data
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
  searchForm.value = { type: '', keyword: '', user_id: '' }
  page.value = 1
  loadData()
}

async function openSendDialog() {
  const res = await api.users.list({ per_page: 100 })
  if (res.code === 200) {
    users.value = res.data.data
  }
  sendForm.value = { id: '', type: 'system', title: '', content: '', link: '', user_ids: [] }
  isEdit.value = false
  dialogVisible.value = true
}

function openEditDialog(notification) {
  sendForm.value = {
    id: notification.id,
    type: notification.type,
    title: notification.title,
    content: notification.content,
    link: notification.link || '',
    user_ids: notification.user_id ? [notification.user_id] : []
  }
  isEdit.value = true
  dialogVisible.value = true
}

async function sendNotification() {
  if (!sendForm.value.title) {
    ElMessage.warning('请输入通知标题')
    return
  }
  let res
  if (isEdit.value) {
    res = await api.admin.notifications.update(sendForm.value.id, sendForm.value)
  } else {
    res = await api.admin.notifications.create(sendForm.value)
  }
  if (res.code === 200) {
    ElMessage.success(isEdit.value ? '更新成功' : '发送成功')
    dialogVisible.value = false
    loadData()
  } else {
    ElMessage.error(res.message || '操作失败')
  }
}

async function deleteNotification(id) {
  try {
    await ElMessageBox.confirm('确定要删除这条通知吗？', '提示', { type: 'warning' })
    const res = await api.admin.notifications.delete(id)
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
  const selected = notifications.value.filter(n => n.checked)
  if (selected.length === 0) {
    ElMessage.warning('请选择要删除的通知')
    return
  }
  try {
    await ElMessageBox.confirm(`确定要删除选中的 ${selected.length} 条通知吗？`, '提示', { type: 'warning' })
    const res = await api.admin.notifications.batchDelete({ ids: selected.map(n => n.id) })
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
  if (!date) return '-'
  return new Date(date).toLocaleString('zh-CN')
}

function getTypeName(type) {
  const map = { system: '系统通知', order: '订单通知', article: '文章通知', message: '消息通知' }
  return map[type] || type
}

function getTypeTagClass(type) {
  const map = { system: '', order: 'success', article: 'warning', message: 'primary' }
  return map[type] || ''
}
</script>

<template>
  <div class="page-container">
    <div class="page-header">
      <h2 class="page-title">
        通知管理
      </h2>
      <div style="display: flex; gap: 8px;">
        <ElButton type="primary" @click="openSendDialog">
          发送通知
        </ElButton>
        <ElButton type="danger" @click="batchDelete">
          批量删除
        </ElButton>
      </div>
    </div>

    <div class="search-bar">
      <ElSelect
        v-model="searchForm.type"
        placeholder="通知类型"
        clearable
        style="width: 120px"
      >
        <ElSelectOption value="system" label="系统通知" />
        <ElSelectOption value="order" label="订单通知" />
        <ElSelectOption value="article" label="文章通知" />
        <ElSelectOption value="message" label="消息通知" />
      </ElSelect>
      <ElInput
        v-model="searchForm.keyword"
        placeholder="搜索标题/内容"
        clearable
        style="width: 200px"
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
        :data="notifications"
        stripe
        row-key="id"
      >
        <ElTableColumn type="selection" width="40" />
        <ElTableColumn prop="id" label="ID" width="60" />
        <ElTableColumn prop="type" label="类型" width="100">
          <template #default="scope">
            <ElTag :type="getTypeTagClass(scope.row.type)" size="small">
              {{ getTypeName(scope.row.type) }}
            </ElTag>
          </template>
        </ElTableColumn>
        <ElTableColumn
          prop="title"
          label="标题"
          min-width="180"
          show-overflow-tooltip
        />
        <ElTableColumn
          prop="content"
          label="内容"
          min-width="200"
          show-overflow-tooltip
        />
        <ElTableColumn prop="user_id" label="接收人" width="100">
          <template #default="scope">
            {{ scope.row.user_id === 0 ? '全部用户' : (scope.row.user?.name || scope.row.user_id) }}
          </template>
        </ElTableColumn>
        <ElTableColumn prop="is_read" label="状态" width="80">
          <template #default="scope">
            <ElTag :type="scope.row.is_read === 1 ? 'info' : 'danger'" size="small">
              {{ scope.row.is_read === 1 ? '已读' : '未读' }}
            </ElTag>
          </template>
        </ElTableColumn>
        <ElTableColumn prop="created_at" label="发送时间" width="160">
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
            >编辑</ElButton>
            <ElButton
              link
              type="danger"
              size="small"
              @click="deleteNotification(scope.row.id)"
            >删除</ElButton>
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

    <ElDialog v-model="dialogVisible" :title="isEdit ? '编辑通知' : '发送通知'" width="500px">
      <ElForm :model="sendForm" label-width="80px">
        <ElFormItem label="通知类型">
          <ElSelect v-model="sendForm.type" style="width: 100%;">
            <ElSelectOption value="system" label="系统通知" />
            <ElSelectOption value="order" label="订单通知" />
            <ElSelectOption value="article" label="文章通知" />
            <ElSelectOption value="message" label="消息通知" />
          </ElSelect>
        </ElFormItem>
        <ElFormItem label="通知标题">
          <ElInput v-model="sendForm.title" placeholder="请输入通知标题" />
        </ElFormItem>
        <ElFormItem label="通知内容">
          <ElInput
            v-model="sendForm.content"
            type="textarea"
            rows="3"
            placeholder="请输入通知内容"
          />
        </ElFormItem>
        <ElFormItem label="跳转链接">
          <ElInput v-model="sendForm.link" placeholder="可选，填写跳转链接" />
        </ElFormItem>
        <ElFormItem label="接收人">
          <ElSelect
            v-model="sendForm.user_ids"
            multiple
            placeholder="留空发送给全部用户"
            style="width: 100%;"
          >
            <ElSelectOption
              v-for="user in users"
              :key="user.id"
              :value="user.id"
              :label="user.name"
            />
          </ElSelect>
        </ElFormItem>
      </ElForm>
      <template #footer>
        <ElButton @click="dialogVisible = false">
          取消
        </ElButton>
        <ElButton type="primary" @click="sendNotification">
          发送
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
