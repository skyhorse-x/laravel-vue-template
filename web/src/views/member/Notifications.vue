<script setup>
import { ref, onMounted, computed } from 'vue'
import api from '@/api'
import { ElMessage } from 'element-plus'

const notifications = ref([])
const loading = ref(false)
const activeTab = ref('all')
const unreadCount = ref(0)

const filteredNotifications = computed(() => {
  if (activeTab.value === 'unread') {
    return notifications.value.filter(n => !n.is_read)
  }
  return notifications.value
})

onMounted(async () => { await loadData() })

async function loadData() {
  loading.value = true
  const res = await api.user.notifications.list()
  if (res.code === 200) {
    notifications.value = res.data.data || []
    unreadCount.value = notifications.value.filter(n => !n.is_read).length
  }
  loading.value = false
}

async function markAsRead(id) {
  const res = await api.user.notifications.markRead(id)
  if (res.code === 200) {
    const notification = notifications.value.find(n => n.id === id)
    if (notification) {
      notification.is_read = 1
      notification.read_at = new Date().toISOString()
      unreadCount.value--
    }
  }
}

async function markAllRead() {
  const res = await api.user.notifications.markAllRead()
  if (res.code === 200) {
    notifications.value.forEach(n => {
      if (!n.is_read) {
        n.is_read = 1
        n.read_at = new Date().toISOString()
      }
    })
    unreadCount.value = 0
    ElMessage.success('已全部标记为已读')
  }
}

async function deleteNotification(id) {
  const res = await api.user.notifications.delete(id)
  if (res.code === 200) {
    const index = notifications.value.findIndex(n => n.id === id)
    if (index > -1) {
      if (!notifications.value[index].is_read) unreadCount.value--
      notifications.value.splice(index, 1)
    }
    ElMessage.success('删除成功')
  }
}

function formatDate(date) {
  if (!date) return '-'
  const d = new Date(date)
  const now = new Date()
  const diff = now - d
  if (diff < 60000) return '刚刚'
  if (diff < 3600000) return Math.floor(diff / 60000) + '分钟前'
  if (diff < 86400000) return Math.floor(diff / 3600000) + '小时前'
  if (diff < 604800000) return Math.floor(diff / 86400000) + '天前'
  return d.toLocaleDateString('zh-CN')
}

function getTypeIcon(type) {
  const map = { system: '🔔', order: '📦', article: '📄', message: '💬' }
  return map[type] || '📢'
}
</script>

<template>
  <div class="notifications-page">
    <div class="page-header">
      <h2 class="page-title">
        通知中心
      </h2>
      <ElButton v-if="unreadCount > 0" size="small" @click="markAllRead">
        全部已读
      </ElButton>
    </div>

    <div class="tabs">
      <div class="tab" :class="{ active: activeTab === 'all' }" @click="activeTab = 'all'">
        全部 <span class="count">{{ notifications.length }}</span>
      </div>
      <div class="tab" :class="{ active: activeTab === 'unread' }" @click="activeTab = 'unread'">
        未读 <span v-if="unreadCount > 0" class="count">{{ unreadCount }}</span>
      </div>
    </div>

    <div v-loading="loading" class="notification-list">
      <div v-if="filteredNotifications.length === 0" class="empty-state">
        <div class="empty-icon">
          🔔
        </div>
        <div class="empty-text">
          暂无通知
        </div>
      </div>

      <div
        v-for="item in filteredNotifications"
        :key="item.id"
        class="notification-item"
        :class="{ unread: !item.is_read }"
        @click="!item.is_read && markAsRead(item.id)"
      >
        <div class="notification-icon">
          {{ getTypeIcon(item.type) }}
        </div>
        <div class="notification-content">
          <div class="notification-title">
            {{ item.title }}
          </div>
          <div v-if="item.content" class="notification-text">
            {{ item.content }}
          </div>
          <div class="notification-time">
            {{ formatDate(item.created_at) }}
          </div>
        </div>
        <div class="notification-actions">
          <span v-if="!item.is_read" class="unread-badge">未读</span>
          <button class="btn-delete" @click.stop="deleteNotification(item.id)">
            删除
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.notifications-page {
  padding: 20px;
}
.page-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
}
.page-title {
  font-size: 1.25rem;
  font-weight: 600;
  margin: 0;
}
.tabs {
  display: flex;
  gap: 4px;
  margin-bottom: 16px;
  border-bottom: 1px solid #e5e7eb;
}
.tab {
  padding: 8px 16px;
  cursor: pointer;
  color: #6b7280;
  border-bottom: 2px solid transparent;
  display: flex;
  align-items: center;
  gap: 6px;
}
.tab.active {
  color: #4f46e5;
  border-bottom-color: #4f46e5;
}
.tab .count {
  background: #f3f4f6;
  padding: 2px 6px;
  border-radius: 10px;
  font-size: 12px;
}
.tab.active .count {
  background: #eef2ff;
  color: #4f46e5;
}
.notification-list {
  background: white;
  border-radius: 12px;
  border: 1px solid #f1f5f9;
  overflow: hidden;
}
.empty-state {
  padding: 60px 20px;
  text-align: center;
}
.empty-icon {
  font-size: 48px;
  margin-bottom: 12px;
}
.empty-text {
  color: #9ca3af;
  font-size: 14px;
}
.notification-item {
  display: flex;
  align-items: flex-start;
  padding: 16px;
  border-bottom: 1px solid #f1f5f9;
  cursor: pointer;
  transition: background 0.2s;
}
.notification-item:last-child {
  border-bottom: none;
}
.notification-item:hover {
  background: #f9fafb;
}
.notification-item.unread {
  background: #f0f9ff;
}
.notification-item.unread:hover {
  background: #e0f2fe;
}
.notification-icon {
  font-size: 24px;
  margin-right: 12px;
  flex-shrink: 0;
}
.notification-content {
  flex: 1;
  min-width: 0;
}
.notification-title {
  font-weight: 500;
  color: #1f2937;
  margin-bottom: 4px;
}
.notification-text {
  color: #6b7280;
  font-size: 14px;
  margin-bottom: 4px;
  line-height: 1.5;
}
.notification-time {
  color: #9ca3af;
  font-size: 12px;
}
.notification-actions {
  display: flex;
  align-items: center;
  gap: 8px;
  flex-shrink: 0;
}
.unread-badge {
  background: #ef4444;
  color: white;
  padding: 2px 8px;
  border-radius: 10px;
  font-size: 12px;
}
.btn-delete {
  background: none;
  border: none;
  color: #9ca3af;
  cursor: pointer;
  padding: 4px 8px;
  border-radius: 4px;
  font-size: 13px;
}
.btn-delete:hover {
  background: #fee2e2;
  color: #ef4444;
}
</style>
