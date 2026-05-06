<script setup>
import { ref, onMounted, computed } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useUserStore } from '@/stores/user'
import { useI18n } from 'vue-i18n'

const { t, locale } = useI18n()
const router = useRouter()
const route = useRoute()
const userStore = useUserStore()
const sidebarCollapsed = ref(false)
const langDropdownOpen = ref(false)

const languages = [
  { code: 'zh', label: '中文', flag: '🇨🇳' },
  { code: 'en', label: 'English', flag: '🇺🇸' },
]

onMounted(async () => {
  await userStore.getInfo()
})

const menuItems = computed(() => [
  { path: '/member', icon: 'home', label: t('member.overview') },
  { path: '/member/articles', icon: 'article', label: '我的文章' },
  { path: '/member/profile', icon: 'user', label: t('member.profile') },
  { path: '/member/change-password', icon: 'lock', label: t('member.changePassword') },
  { path: '/member/referral', icon: 'gift', label: t('member.referral') },
  { path: '/member/financial', icon: 'wallet', label: t('member.financial') },
  { path: '/member/notifications', icon: 'bell', label: '通知中心' },
])

function logout() {
  userStore.logout()
  router.push('/login')
}

function setLang(code) {
  locale.value = code
  localStorage.setItem('locale', code)
  langDropdownOpen.value = false
}
</script>

<template>
  <div class="member-layout" @click="langDropdownOpen = false">
    <!-- Sidebar -->
    <aside class="sidebar" :class="{ collapsed: sidebarCollapsed }">
      <div class="sidebar-header">
        <div class="sidebar-brand">
          <svg viewBox="0 0 32 32" fill="none"><rect
            width="32"
            height="32"
            rx="8"
            fill="url(#sg)"
          /><path
            d="M10 16L14 20L22 12"
            stroke="#fff"
            stroke-width="2.5"
            stroke-linecap="round"
            stroke-linejoin="round"
          /><defs><linearGradient
            id="sg"
            x1="0"
            y1="0"
            x2="32"
            y2="32"
          ><stop stop-color="#6366f1" /><stop offset="1" stop-color="#06b6d4" /></linearGradient></defs></svg>
          <span v-if="!sidebarCollapsed" class="brand-name">TemplatePro</span>
        </div>
        <button class="collapse-btn" @click="sidebarCollapsed = !sidebarCollapsed">
          <svg
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="2"
          ><path d="M11 19l-7-7 7-7m8 14l-7-7 7-7" /></svg>
        </button>
      </div>

      <div v-if="userStore.user && !sidebarCollapsed" class="sidebar-user">
        <div class="user-avatar">
          {{ userStore.user.name?.charAt(0)?.toUpperCase() }}
        </div>
        <div class="user-info">
          <div class="user-name">
            {{ userStore.user.name }}
          </div>
          <div class="user-email">
            {{ userStore.user.email }}
          </div>
        </div>
      </div>
      <div v-else-if="userStore.user" class="sidebar-user-mini">
        <div class="user-avatar small">
          {{ userStore.user.name?.charAt(0)?.toUpperCase() }}
        </div>
      </div>

      <nav class="sidebar-nav">
        <router-link
          v-for="item in menuItems"
          :key="item.path"
          :to="item.path"
          class="nav-item"
          :class="{ active: route.path === item.path }"
        >
          <svg
            v-if="item.icon === 'home'"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="1.5"
          ><path d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" /></svg>
          <svg
            v-if="item.icon === 'user'"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="1.5"
          ><path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
          <svg
            v-if="item.icon === 'lock'"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="1.5"
          ><path d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
          <svg
            v-if="item.icon === 'gift'"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="1.5"
          ><path d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7" /></svg>
          <svg
            v-if="item.icon === 'wallet'"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="1.5"
          ><path d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" /></svg>
          <svg
            v-if="item.icon === 'article'"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="1.5"
          ><path d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" /></svg>
          <svg
            v-if="item.icon === 'bell'"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="1.5"
          ><path d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" /></svg>
          <span v-if="!sidebarCollapsed" class="nav-label">{{ item.label }}</span>
        </router-link>
      </nav>

      <div class="sidebar-footer">
        <button class="nav-item logout-btn" @click="logout">
          <svg
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="1.5"
          ><path d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg>
          <span v-if="!sidebarCollapsed">{{ t('member.logout') }}</span>
        </button>
      </div>
    </aside>

    <!-- Main -->
    <main class="main-content">
      <header class="topbar">
        <div class="topbar-left">
          <h2 class="page-title">
            {{ t('member.title') }}
          </h2>
        </div>
        <div class="topbar-right">
          <div class="lang-select" @click.stop>
            <button class="lang-trigger" @click.stop="langDropdownOpen = !langDropdownOpen">
              <svg
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="1.5"
                width="14"
                height="14"
              ><path d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9" /></svg>
              <span>{{ languages.find(l => l.code === locale)?.flag }}</span>
              <svg
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                width="12"
                height="12"
              ><path d="M19 9l-7 7-7-7" /></svg>
            </button>
            <div class="lang-dropdown" :class="{ show: langDropdownOpen }">
              <button
                v-for="lang in languages"
                :key="lang.code"
                class="lang-option"
                :class="{ active: locale === lang.code }"
                @click.stop="setLang(lang.code)"
              >
                <span>{{ lang.flag }}</span>
                <span>{{ lang.label }}</span>
                <svg
                  v-if="locale === lang.code"
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="2.5"
                  width="14"
                  height="14"
                ><path d="M5 13l4 4L19 7" /></svg>
              </button>
            </div>
          </div>
          <router-link to="/" class="back-home">
            <svg
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="1.5"
              width="18"
              height="18"
            ><path d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" /></svg>
          </router-link>
        </div>
      </header>
      <div class="content-area">
        <router-view />
      </div>
    </main>
  </div>
</template>

<style scoped>
.member-layout {
  display: flex;
  min-height: 100vh;
  background: #f1f5f9;
}

/* Sidebar */
.sidebar {
  width: 260px;
  background: white;
  border-right: 1px solid #e2e8f0;
  display: flex;
  flex-direction: column;
  transition: width 0.3s ease;
  position: fixed;
  top: 0;
  left: 0;
  bottom: 0;
  z-index: 50;
}

.sidebar.collapsed {
  width: 72px;
}

.sidebar-header {
  padding: 1rem;
  display: flex;
  align-items: center;
  justify-content: space-between;
  border-bottom: 1px solid #f1f5f9;
}

.sidebar-brand {
  display: flex;
  align-items: center;
  gap: 0.6rem;
}

.sidebar-brand svg { width: 32px; height: 32px; flex-shrink: 0; }
.brand-name { font-size: 1.1rem; font-weight: 700; color: #1e293b; white-space: nowrap; }

.collapse-btn {
  background: none;
  border: none;
  color: #94a3b8;
  cursor: pointer;
  padding: 0.3rem;
  border-radius: 6px;
  transition: all 0.2s;
}

.collapse-btn:hover { background: #f1f5f9; color: #64748b; }
.collapse-btn svg { width: 18px; height: 18px; }

.sidebar-user {
  padding: 1.25rem 1rem;
  border-bottom: 1px solid #f1f5f9;
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.sidebar-user-mini {
  padding: 1rem;
  display: flex;
  justify-content: center;
  border-bottom: 1px solid #f1f5f9;
}

.user-avatar {
  width: 40px;
  height: 40px;
  border-radius: 12px;
  background: linear-gradient(135deg, #6366f1, #06b6d4);
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 700;
  font-size: 1rem;
  flex-shrink: 0;
}

.user-avatar.small {
  width: 36px;
  height: 36px;
  border-radius: 10px;
  font-size: 0.9rem;
}

.user-name { font-weight: 600; font-size: 0.9rem; color: #1e293b; }
.user-email { font-size: 0.75rem; color: #94a3b8; margin-top: 0.1rem; }

.sidebar-nav {
  flex: 1;
  padding: 0.75rem;
  display: flex;
  flex-direction: column;
  gap: 0.25rem;
  overflow-y: auto;
}

.nav-item {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 0.65rem 0.85rem;
  border-radius: 10px;
  color: #64748b;
  text-decoration: none;
  font-size: 0.9rem;
  font-weight: 500;
  transition: all 0.2s;
  cursor: pointer;
  background: none;
  border: none;
  width: 100%;
  text-align: left;
}

.nav-item svg { width: 20px; height: 20px; flex-shrink: 0; }

.nav-item:hover {
  background: #f1f5f9;
  color: #6366f1;
}

.nav-item.active {
  background: linear-gradient(135deg, rgba(99,102,241,0.1), rgba(6,182,212,0.1));
  color: #6366f1;
}

.nav-label { white-space: nowrap; }

.sidebar-footer {
  padding: 0.75rem;
  border-top: 1px solid #f1f5f9;
}

.logout-btn { color: #ef4444; }
.logout-btn:hover { background: #fef2f2; color: #dc2626; }

/* Main content */
.main-content {
  flex: 1;
  margin-left: 260px;
  transition: margin-left 0.3s ease;
  display: flex;
  flex-direction: column;
  min-height: 100vh;
}

.sidebar.collapsed ~ .main-content,
.sidebar.collapsed + .main-content {
  margin-left: 72px;
}

.topbar {
  height: 64px;
  background: white;
  border-bottom: 1px solid #e2e8f0;
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0 2rem;
  position: sticky;
  top: 0;
  z-index: 10;
}

.page-title {
  font-size: 1.15rem;
  font-weight: 700;
  color: #1e293b;
}

.topbar-right {
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

/* Language dropdown in topbar */
.lang-select {
  position: relative;
}

.lang-trigger {
  display: flex;
  align-items: center;
  gap: 0.35rem;
  padding: 0.35rem 0.7rem;
  border: 1.5px solid #e2e8f0;
  border-radius: 8px;
  background: white;
  color: #475569;
  font-size: 0.8rem;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s;
}

.lang-trigger:hover { border-color: #6366f1; color: #6366f1; }

.lang-dropdown {
  position: absolute;
  top: calc(100% + 6px);
  right: 0;
  background: white;
  border: 1px solid #e2e8f0;
  border-radius: 10px;
  box-shadow: 0 10px 40px rgba(0,0,0,0.1);
  padding: 0.35rem;
  min-width: 140px;
  opacity: 0;
  visibility: hidden;
  transform: translateY(-8px);
  transition: all 0.2s;
  z-index: 200;
}

.lang-dropdown.show {
  opacity: 1;
  visibility: visible;
  transform: translateY(0);
}

.lang-option {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  width: 100%;
  padding: 0.5rem 0.65rem;
  border: none;
  background: none;
  color: #475569;
  font-size: 0.82rem;
  border-radius: 7px;
  cursor: pointer;
  transition: all 0.15s;
  text-align: left;
}

.lang-option:hover { background: #f1f5f9; color: #1e293b; }
.lang-option.active { background: rgba(99,102,241,0.08); color: #6366f1; font-weight: 600; }
.lang-option svg { margin-left: auto; color: #6366f1; }

.back-home {
  width: 36px;
  height: 36px;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #64748b;
  transition: all 0.2s;
}

.back-home:hover { background: #f1f5f9; color: #6366f1; }

.content-area {
  flex: 1;
  padding: 2rem;
}

@media (max-width: 768px) {
  .sidebar { width: 72px; }
  .sidebar .brand-name,
  .sidebar .nav-label,
  .sidebar .sidebar-user .user-info { display: none; }
  .main-content { margin-left: 72px; }
  .content-area { padding: 1rem; }
}
</style>
