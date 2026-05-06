<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { getRequests, clearRequests, exportApiDocs } from '@/api/interceptor'

const requests = ref([])
const selectedRequest = ref(null)
const filterMethod = ref('all')
const filterStatus = ref('all')
const searchQuery = ref('')
const autoRefresh = ref(true)
const refreshInterval = ref(null)
const showDetail = ref(false)

const filteredRequests = computed(() => {
  let result = requests.value

  if (filterMethod.value !== 'all') {
    result = result.filter(r => r.method === filterMethod.value)
  }

  if (filterStatus.value !== 'all') {
    result = result.filter(r => r.status === filterStatus.value)
  }

  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase()
    result = result.filter(r =>
      r.path.toLowerCase().includes(query) ||
      r.method.toLowerCase().includes(query)
    )
  }

  return result
})

const statusCounts = computed(() => {
  return {
    all: requests.value.length,
    success: requests.value.filter(r => r.status === 'success').length,
    error: requests.value.filter(r => r.status === 'error').length,
    pending: requests.value.filter(r => r.status === 'pending').length
  }
})

function formatTime(timestamp) {
  const date = new Date(timestamp)
  return date.toLocaleTimeString()
}

function formatDuration(ms) {
  if (!ms) return '-'
  if (ms < 1000) return `${ms}ms`
  return `${(ms / 1000).toFixed(2)}s`
}

function getMethodColor(method) {
  const colors = {
    GET: '#61affe',
    POST: '#49cc90',
    PUT: '#fca130',
    DELETE: '#f93e3e',
    PATCH: '#50e3c2'
  }
  return colors[method] || '#999'
}

function getStatusColor(status) {
  if (status === 'success') return '#49cc90'
  if (status === 'error') return '#f93e3e'
  if (status === 'pending') return '#fca130'
  return '#999'
}

function viewDetail(request) {
  selectedRequest.value = request
  showDetail.value = true
}

function closeDetail() {
  showDetail.value = false
  selectedRequest.value = null
}

function handleClear() {
  clearRequests()
  requests.value = []
  selectedRequest.value = null
}

function downloadDocs(format) {
  const content = exportApiDocs(format)
  const blob = new Blob([content], {
    type: format === 'markdown' ? 'text/markdown' : 'application/json'
  })
  const url = URL.createObjectURL(blob)
  const a = document.createElement('a')
  a.href = url
  a.download = `api-docs-${Date.now()}.${format === 'markdown' ? 'md' : 'json'}`
  document.body.appendChild(a)
  a.click()
  document.body.removeChild(a)
  URL.revokeObjectURL(url)
}

function refreshRequests() {
  requests.value = [...getRequests()]
}

function toggleAutoRefresh() {
  autoRefresh.value = !autoRefresh.value
  if (autoRefresh.value) {
    startAutoRefresh()
  } else {
    stopAutoRefresh()
  }
}

function startAutoRefresh() {
  if (refreshInterval.value) return
  refreshInterval.value = setInterval(refreshRequests, 1000)
}

function stopAutoRefresh() {
  if (refreshInterval.value) {
    clearInterval(refreshInterval.value)
    refreshInterval.value = null
  }
}

onMounted(() => {
  refreshRequests()
  if (autoRefresh.value) {
    startAutoRefresh()
  }
})

onUnmounted(() => {
  stopAutoRefresh()
})
</script>

<template>
  <div class="api-manager">
    <div class="page-header">
      <h1>API 接口管理</h1>
    </div>

    <div class="api-toolbar">
      <div class="toolbar-left">
        <input
          v-model="searchQuery"
          type="text"
          class="search-input"
          placeholder="搜索 API 路径..."
        />
        <select v-model="filterMethod" class="filter-select">
          <option value="all">所有方法</option>
          <option value="GET">GET</option>
          <option value="POST">POST</option>
          <option value="PUT">PUT</option>
          <option value="DELETE">DELETE</option>
          <option value="PATCH">PATCH</option>
        </select>
        <select v-model="filterStatus" class="filter-select">
          <option value="all">所有状态 ({{ statusCounts.all }})</option>
          <option value="success">成功 ({{ statusCounts.success }})</option>
          <option value="error">失败 ({{ statusCounts.error }})</option>
          <option value="pending">进行中 ({{ statusCounts.pending }})</option>
        </select>
      </div>
      <div class="toolbar-right">
        <button
          class="btn"
          :class="{ 'btn-active': autoRefresh }"
          @click="toggleAutoRefresh"
        >
          {{ autoRefresh ? '暂停刷新' : '开始刷新' }}
        </button>
        <button class="btn btn-secondary" @click="refreshRequests">刷新</button>
        <button class="btn btn-danger" @click="handleClear">清空</button>
        <div class="dropdown">
          <button class="btn btn-primary">下载文档 ▼</button>
          <div class="dropdown-menu">
            <button @click="downloadDocs('json')">JSON 格式</button>
            <button @click="downloadDocs('markdown')">Markdown 格式</button>
          </div>
        </div>
      </div>
    </div>

    <div class="api-content">
      <div class="api-list">
        <div class="list-header">
          <span class="col-method">方法</span>
          <span class="col-path">路径</span>
          <span class="col-status">状态</span>
          <span class="col-time">时间</span>
          <span class="col-duration">耗时</span>
        </div>
        <div
          v-for="req in filteredRequests"
          :key="req.id"
          class="api-item"
          :class="{ selected: selectedRequest?.id === req.id }"
          @click="viewDetail(req)"
        >
          <span class="col-method">
            <span
              class="method-badge"
              :style="{ backgroundColor: getMethodColor(req.method) }"
            >
              {{ req.method }}
            </span>
          </span>
          <span class="col-path">{{ req.path }}</span>
          <span class="col-status">
            <span
              class="status-badge"
              :style="{ backgroundColor: getStatusColor(req.status) }"
            >
              {{ req.status }}
            </span>
          </span>
          <span class="col-time">{{ formatTime(req.timestamp) }}</span>
          <span class="col-duration">{{ formatDuration(req.duration) }}</span>
        </div>
        <div v-if="filteredRequests.length === 0" class="empty-state">
          暂无 API 请求记录
        </div>
      </div>

      <div v-if="selectedRequest" class="api-detail">
        <div class="detail-header">
          <h3>请求详情</h3>
          <button class="close-btn" @click="closeDetail">×</button>
        </div>

        <div class="detail-section">
          <h4>基本信息</h4>
          <div class="detail-row">
            <span class="detail-label">方法:</span>
            <span
              class="method-badge"
              :style="{ backgroundColor: getMethodColor(selectedRequest.method) }"
            >
              {{ selectedRequest.method }}
            </span>
          </div>
          <div class="detail-row">
            <span class="detail-label">路径:</span>
            <span class="detail-value">{{ selectedRequest.path }}</span>
          </div>
          <div class="detail-row">
            <span class="detail-label">状态:</span>
            <span
              class="status-badge"
              :style="{ backgroundColor: getStatusColor(selectedRequest.status) }"
            >
              {{ selectedRequest.status }}
            </span>
          </div>
          <div class="detail-row">
            <span class="detail-label">耗时:</span>
            <span class="detail-value">{{ formatDuration(selectedRequest.duration) }}</span>
          </div>
        </div>

        <div v-if="selectedRequest.params && Object.keys(selectedRequest.params).length > 0" class="detail-section">
          <h4>Query 参数</h4>
          <pre class="code-block">{{ JSON.stringify(selectedRequest.params, null, 2) }}</pre>
        </div>

        <div v-if="selectedRequest.data && Object.keys(selectedRequest.data).length > 0" class="detail-section">
          <h4>请求体</h4>
          <pre class="code-block">{{ JSON.stringify(selectedRequest.data, null, 2) }}</pre>
        </div>

        <div v-if="selectedRequest.response" class="detail-section">
          <h4>响应状态</h4>
          <pre class="code-block">{{ selectedRequest.response.status }} {{ selectedRequest.response.data?.message || '' }}</pre>
        </div>

        <div v-if="selectedRequest.response?.data" class="detail-section">
          <h4>响应数据</h4>
          <pre class="code-block">{{ JSON.stringify(selectedRequest.response.data, null, 2) }}</pre>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.api-manager {
  height: 100%;
  display: flex;
  flex-direction: column;
}

.page-header {
  margin-bottom: 20px;
}

.page-header h1 {
  font-size: 24px;
  color: var(--color-text-primary);
}

.api-toolbar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 16px;
  gap: 12px;
  flex-wrap: wrap;
}

.toolbar-left {
  display: flex;
  gap: 8px;
  flex-wrap: wrap;
}

.toolbar-right {
  display: flex;
  gap: 8px;
  align-items: center;
}

.search-input {
  padding: 8px 12px;
  border: 1px solid var(--color-border);
  border-radius: 6px;
  background: var(--color-bg-secondary);
  color: var(--color-text-primary);
  min-width: 200px;
}

.filter-select {
  padding: 8px 12px;
  border: 1px solid var(--color-border);
  border-radius: 6px;
  background: var(--color-bg-secondary);
  color: var(--color-text-primary);
  cursor: pointer;
}

.btn {
  padding: 8px 16px;
  border: 1px solid var(--color-border);
  border-radius: 6px;
  background: var(--color-bg-secondary);
  color: var(--color-text-primary);
  cursor: pointer;
  transition: all 0.2s;
}

.btn:hover {
  background: var(--color-bg-hover);
}

.btn-active {
  background: var(--color-primary);
  color: #fff;
  border-color: var(--color-primary);
}

.btn-primary {
  background: var(--color-primary);
  color: #fff;
  border-color: var(--color-primary);
}

.btn-primary:hover {
  background: var(--color-primary-dark, #0056b3);
}

.btn-secondary {
  background: var(--color-bg-secondary);
}

.btn-danger {
  color: #dc3545;
  border-color: #dc3545;
}

.btn-danger:hover {
  background: #dc3545;
  color: #fff;
}

.dropdown {
  position: relative;
}

.dropdown-menu {
  display: none;
  position: absolute;
  top: 100%;
  right: 0;
  background: var(--color-bg-secondary);
  border: 1px solid var(--color-border);
  border-radius: 6px;
  min-width: 150px;
  z-index: 100;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
}

.dropdown:hover .dropdown-menu {
  display: block;
}

.dropdown-menu button {
  width: 100%;
  padding: 10px 16px;
  border: none;
  background: none;
  text-align: left;
  cursor: pointer;
  color: var(--color-text-primary);
}

.dropdown-menu button:hover {
  background: var(--color-bg-hover);
}

.api-content {
  display: flex;
  gap: 16px;
  flex: 1;
  min-height: 0;
}

.api-list {
  flex: 1;
  border: 1px solid var(--color-border);
  border-radius: 8px;
  overflow: hidden;
  display: flex;
  flex-direction: column;
}

.list-header {
  display: grid;
  grid-template-columns: 80px 1fr 80px 100px 80px;
  gap: 8px;
  padding: 12px 16px;
  background: var(--color-bg-secondary);
  font-weight: 600;
  font-size: 13px;
  color: var(--color-text-secondary);
  border-bottom: 1px solid var(--color-border);
}

.api-item {
  display: grid;
  grid-template-columns: 80px 1fr 80px 100px 80px;
  gap: 8px;
  padding: 12px 16px;
  align-items: center;
  border-bottom: 1px solid var(--color-border);
  cursor: pointer;
  transition: background 0.2s;
}

.api-item:hover {
  background: var(--color-bg-hover);
}

.api-item.selected {
  background: var(--color-primary-light, rgba(97, 175, 254, 0.1));
}

.method-badge {
  display: inline-block;
  padding: 2px 8px;
  border-radius: 4px;
  color: #fff;
  font-size: 12px;
  font-weight: 600;
  text-align: center;
}

.status-badge {
  display: inline-block;
  padding: 2px 8px;
  border-radius: 4px;
  color: #fff;
  font-size: 12px;
}

.col-path {
  font-family: monospace;
  font-size: 13px;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.col-time,
.col-duration {
  font-size: 13px;
  color: var(--color-text-secondary);
}

.empty-state {
  padding: 40px;
  text-align: center;
  color: var(--color-text-secondary);
}

.api-detail {
  width: 500px;
  border: 1px solid var(--color-border);
  border-radius: 8px;
  overflow: hidden;
  display: flex;
  flex-direction: column;
}

.detail-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 16px;
  background: var(--color-bg-secondary);
  border-bottom: 1px solid var(--color-border);
}

.detail-header h3 {
  margin: 0;
  font-size: 16px;
}

.close-btn {
  width: 28px;
  height: 28px;
  border: none;
  background: none;
  font-size: 20px;
  cursor: pointer;
  color: var(--color-text-secondary);
}

.detail-section {
  padding: 16px;
  border-bottom: 1px solid var(--color-border);
}

.detail-section h4 {
  margin: 0 0 12px 0;
  font-size: 14px;
  color: var(--color-text-secondary);
}

.detail-row {
  display: flex;
  gap: 8px;
  margin-bottom: 8px;
  align-items: center;
}

.detail-label {
  font-size: 13px;
  color: var(--color-text-secondary);
  min-width: 60px;
}

.detail-value {
  font-size: 13px;
  font-family: monospace;
}

.code-block {
  background: var(--color-bg-secondary);
  padding: 12px;
  border-radius: 6px;
  font-size: 12px;
  font-family: monospace;
  overflow-x: auto;
  max-height: 300px;
  margin: 0;
  white-space: pre-wrap;
  word-break: break-all;
}
</style>
