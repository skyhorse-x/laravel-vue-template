<script setup>
import { ref, onMounted, computed } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useAdminStore } from '@/stores/admin'
import api from '@/api'

const router = useRouter()
const route = useRoute()
const adminStore = useAdminStore()
const collapsed = ref(false)
const menus = ref([])
const expandedKeys = ref([])

onMounted(async () => {
  await adminStore.getInfo()
  const saved = localStorage.getItem('admin_sidebar_collapsed')
  if (saved !== null) collapsed.value = saved === 'true'
  await loadMenus()
})

async function loadMenus() {
  try {
    const res = await api.menus.list()
    if (res.code === 200) {
      menus.value = res.data
      // 默认展开当前路由所在的父菜单
      const parentId = findParentId(route.path, res.data)
      if (parentId && !expandedKeys.value.includes(parentId)) {
        expandedKeys.value.push(parentId)
      }
    }
  } catch (e) {
    // fallback 使用静态菜单
    menus.value = []
  }
}

function findParentId(path, menuList) {
  for (const menu of menuList) {
    if (menu.children) {
      for (const child of menu.children) {
        if (child.route && path.startsWith(child.route)) return menu.id
      }
    }
  }
  return null
}

function toggleSidebar() {
  collapsed.value = !collapsed.value
  localStorage.setItem('admin_sidebar_collapsed', collapsed.value)
}

function toggleExpand(id) {
  const idx = expandedKeys.value.indexOf(id)
  if (idx >= 0) {
    expandedKeys.value.splice(idx, 1)
  } else {
    expandedKeys.value = [id]
  }
}

function isExpanded(id) {
  return expandedKeys.value.includes(id)
}

function logout() {
  adminStore.logout()
  router.push('/admin/login')
}

function isActive(path) {
  if (!path) return false
  if (path === '/admin') return route.path === '/admin'
  return route.path.startsWith(path)
}

const breadcrumbLabel = computed(() => {
  for (const menu of menus.value) {
    if (menu.route && isActive(menu.route)) return menu.name
    if (menu.children) {
      for (const child of menu.children) {
        if (child.route && isActive(child.route)) return menu.name + ' / ' + child.name
      }
    }
  }
  return '仪表盘'
})

const iconMap = {
  Console: 'M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z',
  Product: 'M4 7h16M4 7a2 2 0 00-2 2v10a2 2 0 002 2h16a2 2 0 002-2V9a2 2 0 00-2-2M4 7l1.5-3h13L20 7M9 11h6',
  Dashboard: 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-4 0a1 1 0 01-1-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 01-1 1',
  Users: 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z',
  Article: 'M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z',
  Setting: 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.573-1.066z M15 12a3 3 0 11-6 0 3 3 0 016 0z',
  Financial: 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
  Api: 'M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z',
}

function getIconPath(icon) {
  return iconMap[icon] || iconMap.Dashboard
}

const unreadNotifications = ref([])
const notificationVisible = ref(false)
const notificationLoading = ref(false)

async function loadUnreadNotifications() {
  notificationLoading.value = true
  try {
    const res = await api.admin.notifications.list({ per_page: 5, is_read: 0 })
    if (res.code === 200) {
      unreadNotifications.value = res.data.data || []
    }
  } catch (error) {
    console.error('加载通知失败', error)
  }
  notificationLoading.value = false
}

function toggleNotification() {
  notificationVisible.value = !notificationVisible.value
  if (notificationVisible.value) {
    loadUnreadNotifications()
  }
}

async function markAllRead() {
  await loadUnreadNotifications()
}

function formatNotifyTime(date) {
  if (!date) return ''
  const d = new Date(date)
  const now = new Date()
  const diff = now - d
  if (diff < 60000) return '刚刚'
  if (diff < 3600000) return Math.floor(diff / 60000) + '分钟前'
  if (diff < 86400000) return Math.floor(diff / 3600000) + '小时前'
  return d.toLocaleDateString('zh-CN')
}
</script>

<template>
  <div class="admin-layout admin-theme">
    <!-- Sidebar -->
    <aside class="sidebar" :class="{ collapsed }">
      <div class="sidebar-header">
        <div v-if="!collapsed" class="logo">
          <svg
            viewBox="0 0 32 32"
            fill="none"
            width="28"
            height="28"
          ><rect
            width="32"
            height="32"
            rx="8"
            fill="currentColor"
            opacity="0.1"
          /><path
            d="M10 16L14 20L22 12"
            stroke="currentColor"
            stroke-width="2.5"
            stroke-linecap="round"
            stroke-linejoin="round"
          /></svg>
          <span>管理后台</span>
        </div>
        <div v-else class="logo-mini">
          <svg
            viewBox="0 0 32 32"
            fill="none"
            width="28"
            height="28"
          ><rect
            width="32"
            height="32"
            rx="8"
            fill="currentColor"
            opacity="0.1"
          /><path
            d="M10 16L14 20L22 12"
            stroke="currentColor"
            stroke-width="2.5"
            stroke-linecap="round"
            stroke-linejoin="round"
          /></svg>
        </div>
      </div>

      <nav class="sidebar-nav">
        <template v-for="menu in menus" :key="menu.id">
          <!-- 有子菜单 -->
          <template v-if="menu.children && menu.children.length > 0">
            <div
              class="nav-item has-children"
              :class="{ active: menu.children.some(c => c.route && isActive(c.route)), expanded: isExpanded(menu.id) }"
              :title="collapsed ? menu.name : ''"
              @click="toggleExpand(menu.id)"
            >
              <span v-if="menu.icon" class="nav-icon">
                <svg
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="1.5"
                ><path :d="getIconPath(menu.icon)" /></svg>
              </span>
              <span v-if="!collapsed" class="nav-label">{{ menu.name }}</span>
              <span v-if="!collapsed" class="nav-arrow">
                <svg
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="2"
                  width="14"
                  height="14"
                ><path d="M9 5l7 7-7 7" /></svg>
              </span>
            </div>
            <div v-if="!collapsed && isExpanded(menu.id)" class="nav-children">
              <router-link
                v-for="child in menu.children"
                :key="child.id"
                :to="child.route"
                class="nav-item child-item"
                :class="{ active: isActive(child.route) }"
              >
                <span class="child-dot" />
                <span class="nav-label">{{ child.name }}</span>
              </router-link>
            </div>
          </template>
          <!-- 无子菜单 -->
          <router-link
            v-else
            :to="menu.route || '/admin'"
            class="nav-item"
            :class="{ active: isActive(menu.route) }"
            :title="collapsed ? menu.name : ''"
          >
            <span v-if="menu.icon" class="nav-icon">
              <svg
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="1.5"
              ><path :d="getIconPath(menu.icon)" /></svg>
            </span>
            <span v-if="!collapsed" class="nav-label">{{ menu.name }}</span>
          </router-link>
        </template>
      </nav>
    </aside>

    <!-- Main Area -->
    <div class="main-area">
      <!-- Top Bar -->
      <header class="topbar">
        <div class="topbar-left">
          <button class="collapse-btn" @click="toggleSidebar">
            <svg
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="1.5"
              width="20"
              height="20"
            >
              <path v-if="!collapsed" d="M11 19l-7-7 7-7m8 14l-7-7 7-7" />
              <path v-else d="M13 5l7 7-7 7M5 5l7 7-7 7" />
            </svg>
          </button>
          <div class="breadcrumb">
            <span class="breadcrumb-home" @click="router.push('/admin')">Home</span>
            <span class="breadcrumb-sep">/</span>
            <span class="breadcrumb-current">{{ breadcrumbLabel }}</span>
          </div>
        </div>
        <div class="topbar-right">
          <button class="topbar-icon-btn" title="任务中心" @click="router.push('/admin/operation-logs')">
            <svg
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="1.5"
              width="18"
              height="18"
            ><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" /></svg>
          </button>
          <div class="notification-wrapper">
            <button class="topbar-icon-btn" title="通知" @click="toggleNotification">
              <svg
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="1.5"
                width="18"
                height="18"
              ><path d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" /></svg>
              <span v-if="unreadNotifications.length > 0" class="notification-badge">{{ unreadNotifications.length > 9 ? '9+' : unreadNotifications.length }}</span>
            </button>
            <div v-if="notificationVisible" class="notification-dropdown">
              <div class="notification-header">
                <span>通知</span>
                <button class="mark-read-btn" @click="markAllRead">
                  全部已读
                </button>
              </div>
              <div v-loading="notificationLoading" class="notification-list">
                <div v-if="unreadNotifications.length === 0" class="notification-empty">
                  暂无未读通知
                </div>
                <div v-for="item in unreadNotifications" :key="item.id" class="notification-item">
                  <div class="notification-content">
                    <div class="notification-title">
                      {{ item.title }}
                    </div>
                    <div class="notification-time">
                      {{ formatNotifyTime(item.created_at) }}
                    </div>
                  </div>
                </div>
              </div>
              <div class="notification-footer" @click="router.push('/admin/notifications'); notificationVisible = false">
                查看全部通知
              </div>
            </div>
          </div>
          <button class="topbar-icon-btn" title="前台首页" @click="router.push('/')">
            <svg
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="1.5"
              width="18"
              height="18"
            ><path d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" /></svg>
          </button>
          <div class="user-menu">
            <div class="user-avatar">
              {{ adminStore.admin?.name?.charAt(0) || 'A' }}
            </div>
            <span class="user-name">{{ adminStore.admin?.name || '管理员' }}</span>
            <div class="user-dropdown">
              <button class="dropdown-item" @click="router.push('/admin/profile')">
                <svg
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="1.5"
                  width="16"
                  height="16"
                ><path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                个人设置
              </button>
              <div class="dropdown-divider" />
              <button class="dropdown-item danger" @click="logout">
                <svg
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="1.5"
                  width="16"
                  height="16"
                ><path d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg>
                退出登录
              </button>
            </div>
          </div>
        </div>
      </header>

      <!-- Content -->
      <main class="content">
        <div class="content-wrapper">
          <router-view />
        </div>
      </main>
    </div>
  </div>
</template>

<style scoped>
.admin-layout {
  display: flex;
  height: 100vh;
  width: 100vw;
  overflow: hidden;
  background: var(--bg-page);
  font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

.main-area {
  flex: 1;
  display: flex;
  flex-direction: column;
  overflow: hidden;
  min-width: 0;
}

.content {
  flex: 1;
  overflow-y: auto;
  overflow-x: hidden;
  background: var(--bg-page);
}

.content-wrapper {
  width: auto;
  margin: 0 auto;
  padding: 24px;
}

.sidebar {
  width: 220px;
  background: var(--color-sidebar);
  display: flex;
  flex-direction: column;
  transition: width 0.25s ease;
  flex-shrink: 0;
  overflow: hidden;
  box-shadow: 2px 0 8px rgba(0,0,0,0.15);
}

.sidebar.collapsed { width: 64px; }

.sidebar-header {
  height: 56px;
  display: flex;
  align-items: center;
  padding: 0 16px;
  border-bottom: 1px solid var(--color-sidebar-border);
  flex-shrink: 0;
}

.logo {
  display: flex;
  align-items: center;
  gap: 10px;
  color: #fff;
  font-weight: 700;
  font-size: 1rem;
  white-space: nowrap;
  overflow: hidden;
}

.logo-mini {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 100%;
  color: #fff;
}

.sidebar-nav {
  flex: 1;
  padding: 12px 8px;
  overflow-y: auto;
  overflow-x: hidden;
}

.nav-item {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 10px 14px;
  border-radius: 8px;
  color: var(--color-sidebar-text);
  text-decoration: none;
  font-size: 0.875rem;
  font-weight: 500;
  transition: all 0.2s;
  white-space: nowrap;
  margin-bottom: 4px;
  cursor: pointer;
  user-select: none;
}

.nav-item:hover {
  background: var(--color-sidebar-hover);
  color: var(--color-sidebar-text-hover);
}

.nav-item.active {
  background: var(--color-sidebar-active);
  color: #fff;
}

.nav-item.has-children .nav-arrow {
  margin-left: auto;
  opacity: 0.6;
  transition: transform 0.2s, opacity 0.2s;
}

.nav-item.has-children.expanded .nav-arrow {
  transform: rotate(90deg);
}

.nav-item.has-children.active {
  color: #fff;
}

.nav-children {
  padding-left: 12px;
  margin-top: -2px;
  margin-bottom: 4px;
}

.child-item {
  padding: 8px 14px 8px 20px;
  font-size: 0.8125rem;
  gap: 8px;
  color: var(--color-sidebar-text);
}

.child-dot {
  width: 6px;
  height: 6px;
  border-radius: 50%;
  background: rgba(255,255,255,0.3);
  flex-shrink: 0;
  transition: all 0.2s;
}

.child-item.active {
  color: #fff;
}

.child-item.active .child-dot {
  background: #fff;
  box-shadow: 0 0 6px rgba(255,255,255,0.5);
}

.child-item:hover {
  color: var(--color-sidebar-text-hover);
}

.child-item:hover .child-dot {
  background: rgba(255,255,255,0.5);
}

.nav-icon {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 20px;
  height: 20px;
  flex-shrink: 0;
  opacity: 0.8;
  transition: opacity 0.2s;
}

.nav-item:hover .nav-icon,
.nav-item.active .nav-icon {
  opacity: 1;
}

.nav-icon svg {
  width: 20px;
  height: 20px;
}

.nav-label {
  overflow: hidden;
  text-overflow: ellipsis;
}

.collapsed .nav-item {
  justify-content: center;
  padding: 10px;
}

.collapsed .nav-icon {
  width: 24px;
  height: 24px;
}

.collapsed .nav-icon svg {
  width: 24px;
  height: 24px;
}

/* Top Bar */
.topbar {
  height: 56px;
  background: var(--color-card);
  border-bottom: 1px solid var(--color-border);
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0 24px;
  flex-shrink: 0;
}

.topbar-left {
  display: flex;
  align-items: center;
  gap: 16px;
}

.collapse-btn {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 32px;
  height: 32px;
  border: none;
  background: none;
  color: var(--color-text-light);
  border-radius: 6px;
  cursor: pointer;
  transition: all 0.15s;
}

.collapse-btn:hover {
  background: var(--color-border-light);
  color: var(--color-text);
}

.breadcrumb {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 0.85rem;
}

.breadcrumb-home {
  color: var(--color-text-lighter);
  cursor: pointer;
  transition: color 0.15s;
}

.breadcrumb-home:hover { color: var(--color-primary); }
.breadcrumb-sep { color: var(--color-text-lighter); opacity: 0.5; }
.breadcrumb-current { color: var(--color-text); font-weight: 600; }

.topbar-right {
  display: flex;
  align-items: center;
  gap: 12px;
}

.topbar-icon-btn {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 36px;
  height: 36px;
  border: none;
  background: none;
  color: var(--color-text-light);
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.15s;
}

.topbar-icon-btn:hover {
  background: var(--color-border-light);
  color: var(--color-primary);
}

/* User Menu */
.user-menu {
  position: relative;
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 4px 8px 4px 4px;
  border-radius: 8px;
  cursor: pointer;
  transition: background 0.15s;
}

.user-menu:hover {
  background: var(--color-border-light);
}

.user-avatar {
  width: 32px;
  height: 32px;
  border-radius: 8px;
  background: linear-gradient(135deg, var(--color-primary), var(--color-primary-light));
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 0.8rem;
  font-weight: 700;
  flex-shrink: 0;
}

.user-name {
  font-size: 0.85rem;
  font-weight: 500;
  color: var(--color-text);
}

.user-dropdown {
  position: absolute;
  top: calc(100% + 6px);
  right: 0;
  background: var(--color-card);
  border: 1px solid var(--color-border);
  border-radius: 10px;
  box-shadow: 0 10px 40px rgba(0,0,0,0.08);
  padding: 4px;
  min-width: 160px;
  opacity: 0;
  visibility: hidden;
  transform: translateY(-4px);
  transition: all 0.15s;
  z-index: 100;
}

.user-menu:hover .user-dropdown {
  opacity: 1;
  visibility: visible;
  transform: translateY(0);
}

.dropdown-item {
  display: flex;
  align-items: center;
  gap: 8px;
  width: 100%;
  padding: 8px 12px;
  border: none;
  background: none;
  color: var(--color-text-light);
  font-size: 0.85rem;
  border-radius: 6px;
  cursor: pointer;
  transition: all 0.15s;
  text-align: left;
}

.dropdown-item:hover { background: var(--color-border-light); color: var(--color-text-primary); }
.dropdown-item.danger:hover { background: #fef2f2; color: #F56C6C; }
.dropdown-divider { height: 1px; background: var(--color-border-light); margin: 4px 0; }

/* Notification */
.notification-wrapper {
  position: relative;
}

.notification-badge {
  position: absolute;
  top: 2px;
  right: 2px;
  min-width: 16px;
  height: 16px;
  padding: 0 4px;
  background: #F56C6C;
  color: white;
  font-size: 10px;
  font-weight: 600;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.notification-dropdown {
  position: absolute;
  top: calc(100% + 8px);
  right: 0;
  width: 320px;
  background: var(--color-card);
  border: 1px solid var(--color-border);
  border-radius: 12px;
  box-shadow: 0 10px 40px rgba(0,0,0,0.1);
  z-index: 1000;
  overflow: hidden;
}

.notification-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 12px 16px;
  border-bottom: 1px solid var(--color-border-light);
  font-weight: 600;
  color: var(--color-text-dark);
}

.mark-read-btn {
  border: none;
  background: none;
  color: var(--color-primary);
  font-size: 13px;
  cursor: pointer;
}

.notification-list {
  max-height: 300px;
  overflow-y: auto;
}

.notification-empty {
  padding: 40px 16px;
  text-align: center;
  color: var(--color-text-lighter);
  font-size: 14px;
}

.notification-item {
  padding: 12px 16px;
  border-bottom: 1px solid var(--color-border-light);
  cursor: pointer;
  transition: background 0.15s;
}

.notification-item:hover {
  background: var(--color-border-light);
}

.notification-item:last-child {
  border-bottom: none;
}

.notification-title {
  font-size: 14px;
  color: var(--color-text);
  margin-bottom: 4px;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.notification-time {
  font-size: 12px;
  color: var(--color-text-lighter);
}

.notification-footer {
  padding: 12px 16px;
  text-align: center;
  color: var(--color-primary);
  font-size: 14px;
  cursor: pointer;
  border-top: 1px solid var(--color-border-light);
  transition: background 0.15s;
}

.notification-footer:hover {
  background: var(--color-border-light);
}

@media (max-width: 768px) {
  .sidebar { position: fixed; z-index: 200; height: 100vh; }
  .sidebar.collapsed { width: 0; }
  .user-name { display: none; }
  .breadcrumb { display: none; }
  .content { padding: 16px; }
}
</style>
