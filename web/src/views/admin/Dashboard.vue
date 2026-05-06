<script setup>
import { ref, onMounted } from 'vue'
import api from '@/api'

const stats = ref({
  users: 0,
  admins: 0,
  articles: 0,
  income: 0
})

const loading = ref(true)

onMounted(async () => {
  try {
    const res = await api.admin.dashboard()
    if (res.code === 200) {
      stats.value = res.data
    }
  } finally {
    loading.value = false
  }
})
</script>

<template>
  <div class="dashboard">
    <h1 class="page-title">
      控制台
    </h1>

    <div class="stats-grid">
      <div class="stat-card">
        <div class="stat-icon icon-users">
          <svg
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="2"
          >
            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
            <circle cx="9" cy="7" r="4" />
            <path d="M23 21v-2a4 4 0 0 0-3-3.87" />
            <path d="M16 3.13a4 4 0 0 1 0 7.75" />
          </svg>
        </div>
        <div class="stat-info">
          <div class="stat-value">
            {{ stats.users || 0 }}
          </div>
          <div class="stat-label">
            用户总数
          </div>
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-icon icon-admins">
          <svg
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="2"
          >
            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" />
          </svg>
        </div>
        <div class="stat-info">
          <div class="stat-value">
            {{ stats.admins || 0 }}
          </div>
          <div class="stat-label">
            管理员
          </div>
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-icon icon-articles">
          <svg
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="2"
          >
            <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20" />
            <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z" />
          </svg>
        </div>
        <div class="stat-info">
          <div class="stat-value">
            {{ stats.articles || 0 }}
          </div>
          <div class="stat-label">
            文章数量
          </div>
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-icon icon-income">
          <svg
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="2"
          >
            <line
              x1="12"
              y1="1"
              x2="12"
              y2="23"
            />
            <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6" />
          </svg>
        </div>
        <div class="stat-info">
          <div class="stat-value">
            ¥{{ stats.income || 0 }}
          </div>
          <div class="stat-label">
            总收入
          </div>
        </div>
      </div>
    </div>

    <div class="quick-section">
      <div class="quick-card">
        <h3>快捷操作</h3>
        <div class="quick-links">
          <router-link to="/admin/users" class="quick-link">
            <svg
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
              width="16"
              height="16"
            >
              <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
              <circle cx="9" cy="7" r="4" />
            </svg>
            用户管理
          </router-link>
          <router-link to="/admin/articles" class="quick-link">
            <svg
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
              width="16"
              height="16"
            >
              <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20" />
              <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z" />
            </svg>
            文章管理
          </router-link>
          <router-link to="/admin/products" class="quick-link">
            <svg
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
              width="16"
              height="16"
            >
              <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z" />
              <line
                x1="3"
                y1="6"
                x2="21"
                y2="6"
              />
              <path d="M16 10a4 4 0 0 1-8 0" />
            </svg>
            商品管理
          </router-link>
          <router-link to="/admin/menus" class="quick-link">
            <svg
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
              width="16"
              height="16"
            >
              <line
                x1="3"
                y1="12"
                x2="21"
                y2="12"
              />
              <line
                x1="3"
                y1="6"
                x2="21"
                y2="6"
              />
              <line
                x1="3"
                y1="18"
                x2="21"
                y2="18"
              />
            </svg>
            菜单管理
          </router-link>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.page-title {
  font-size: 1.25rem;
  font-weight: 700;
  color: var(--color-text);
  margin-bottom: 24px;
}

.stats-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 16px;
  margin-bottom: 24px;
}

.stat-card {
  background: var(--color-card);
  border-radius: 12px;
  padding: 20px;
  display: flex;
  align-items: center;
  gap: 16px;
  border: 1px solid var(--border-light);
  transition: box-shadow 0.2s;
}

.stat-card:hover {
  box-shadow: 0 4px 16px rgba(0,0,0,0.08);
}

.stat-icon {
  width: 48px;
  height: 48px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.stat-icon svg {
  width: 24px;
  height: 24px;
}

.icon-users {
  background: rgba(99,102,241,0.1);
  color: #6366f1;
}

.icon-admins {
  background: rgba(16,185,129,0.1);
  color: #10b981;
}

.icon-articles {
  background: rgba(245,158,11,0.1);
  color: #f59e0b;
}

.icon-income {
  background: rgba(236,72,153,0.1);
  color: #ec4899;
}

.stat-value {
  font-size: 1.5rem;
  font-weight: 700;
  color: var(--color-text);
  line-height: 1.2;
}

.stat-label {
  font-size: 0.8rem;
  color: var(--color-text-secondary);
  margin-top: 2px;
}

.quick-section {
  margin-top: 24px;
}

.quick-card {
  background: var(--color-card);
  border-radius: 12px;
  padding: 20px;
  border: 1px solid var(--border-light);
}

.quick-card h3 {
  font-size: 0.9rem;
  font-weight: 600;
  color: var(--color-text);
  margin-bottom: 16px;
}

.quick-links {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 12px;
}

.quick-link {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 12px 16px;
  background: var(--bg-hover);
  border-radius: 8px;
  color: var(--color-text);
  font-size: 0.875rem;
  transition: all 0.2s;
  text-decoration: none;
}

.quick-link:hover {
  background: var(--color-primary);
  color: white;
}
</style>
