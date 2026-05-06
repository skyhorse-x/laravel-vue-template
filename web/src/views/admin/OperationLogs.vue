<script setup>
import { ref, onMounted } from 'vue'
import api from '@/api'
import { ElMessage, ElMessageBox } from 'element-plus'

const logs = ref([])
const total = ref(0)
const page = ref(1)
const perPage = 20
const loading = ref(false)
const modules = ref([])
const searchForm = ref({ username: '', module: '', ip: '', start_date: '', end_date: '' })
const detailVisible = ref(false)
const currentLog = ref(null)

onMounted(async () => { await loadData(); await loadModules() })

async function loadData() {
  loading.value = true
  const res = await api.admin.operationLogs.list({ page: page.value, per_page: perPage, ...searchForm.value })
  if (res.code === 200) {
    logs.value = res.data.data
    total.value = res.data.total
  }
  loading.value = false
}

async function loadModules() {
  const res = await api.admin.operationLogs.modules()
  if (res.code === 200) {
    modules.value = res.data
  }
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
  searchForm.value = { username: '', module: '', ip: '', start_date: '', end_date: '' }
  page.value = 1
  loadData()
}

function viewDetail(log) {
  currentLog.value = log
  detailVisible.value = true
}

async function clearLogs() {
  try {
    await ElMessageBox.confirm('确定要清理30天前的日志吗？', '提示', { type: 'warning' })
    const res = await api.admin.operationLogs.clear({ days: 30 })
    if (res.code === 200) {
      ElMessage.success(res.message)
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

function formatMethod(method) {
  const map = { GET: 'info', POST: 'success', PUT: 'warning', DELETE: 'danger', PATCH: 'warning' }
  return map[method] || ''
}

function formatParams(params) {
  if (!params) return '-'
  try {
    const obj = typeof params === 'string' ? JSON.parse(params) : params
    return JSON.stringify(obj, null, 2)
  } catch {
    return params
  }
}
</script>

<template>
  <div class="page-container">
    <div class="page-header">
      <h2 class="page-title">
        操作日志
      </h2>
      <div style="display: flex; gap: 8px;">
        <ElButton type="warning" @click="clearLogs">
          清理日志
        </ElButton>
      </div>
    </div>

    <div class="search-bar">
      <ElInput
        v-model="searchForm.username"
        placeholder="操作人"
        clearable
        style="width: 140px"
      />
      <ElSelect
        v-model="searchForm.module"
        placeholder="模块"
        clearable
        style="width: 140px"
      >
        <ElSelectOption
          v-for="m in modules"
          :key="m"
          :value="m"
          :label="m"
        />
      </ElSelect>
      <ElInput
        v-model="searchForm.ip"
        placeholder="IP地址"
        clearable
        style="width: 140px"
      />
      <ElDatePicker
        v-model="searchForm.start_date"
        type="date"
        placeholder="开始日期"
        style="width: 140px"
      />
      <ElDatePicker
        v-model="searchForm.end_date"
        type="date"
        placeholder="结束日期"
        style="width: 140px"
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
        :data="logs"
        stripe
        row-key="id"
      >
        <ElTableColumn prop="id" label="ID" width="70" />
        <ElTableColumn prop="username" label="操作人" width="120" />
        <ElTableColumn prop="module" label="模块" width="120" />
        <ElTableColumn prop="action" label="动作" width="120" />
        <ElTableColumn prop="method" label="方法" width="80">
          <template #default="scope">
            <ElTag :type="formatMethod(scope.row.method)" size="small">
              {{ scope.row.method }}
            </ElTag>
          </template>
        </ElTableColumn>
        <ElTableColumn
          prop="url"
          label="URL"
          min-width="200"
          show-overflow-tooltip
        />
        <ElTableColumn prop="ip" label="IP" width="140" />
        <ElTableColumn prop="created_at" label="时间" width="160">
          <template #default="scope">
            {{ formatDate(scope.row.created_at) }}
          </template>
        </ElTableColumn>
        <ElTableColumn label="操作" width="80" fixed="right">
          <template #default="scope">
            <ElButton
              link
              type="primary"
              size="small"
              @click="viewDetail(scope.row)"
            >
              详情
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

    <ElDialog v-model="detailVisible" title="日志详情" width="600px">
      <ElDescriptions v-if="currentLog" :column="2" border>
        <ElDescriptionsItem label="ID">
          {{ currentLog.id }}
        </ElDescriptionsItem>
        <ElDescriptionsItem label="操作人">
          {{ currentLog.username }}
        </ElDescriptionsItem>
        <ElDescriptionsItem label="模块">
          {{ currentLog.module }}
        </ElDescriptionsItem>
        <ElDescriptionsItem label="动作">
          {{ currentLog.action }}
        </ElDescriptionsItem>
        <ElDescriptionsItem label="请求方法">
          {{ currentLog.method }}
        </ElDescriptionsItem>
        <ElDescriptionsItem label="IP地址">
          {{ currentLog.ip }}
        </ElDescriptionsItem>
        <ElDescriptionsItem label="URL" :span="2">
          {{ currentLog.url }}
        </ElDescriptionsItem>
        <ElDescriptionsItem label="时间" :span="2">
          {{ formatDate(currentLog.created_at) }}
        </ElDescriptionsItem>
        <ElDescriptionsItem label="请求参数" :span="2">
          <pre style="max-height: 200px; overflow: auto; margin: 0; font-size: 12px;">{{ formatParams(currentLog.params) }}</pre>
        </ElDescriptionsItem>
        <ElDescriptionsItem label="返回结果" :span="2">
          <pre style="max-height: 200px; overflow: auto; margin: 0; font-size: 12px;">{{ formatParams(currentLog.result) }}</pre>
        </ElDescriptionsItem>
        <ElDescriptionsItem label="User Agent" :span="2">
          <span style="word-break: break-all; font-size: 12px;">{{ currentLog.user_agent || '-' }}</span>
        </ElDescriptionsItem>
      </ElDescriptions>
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
