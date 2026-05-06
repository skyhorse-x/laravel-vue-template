<script setup>
import { ref } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useAdminStore } from '@/stores/admin'
import { ElMessage } from 'element-plus'

const router = useRouter()
const route = useRoute()
const adminStore = useAdminStore()
const loading = ref(false)

const form = ref({ username: '', password: '' })

async function handleLogin() {
  if (!form.value.username || !form.value.password) { ElMessage.error('请填写完整信息'); return }
  loading.value = true
  try {
    const res = await adminStore.login(form.value.username, form.value.password)
    if (res.code === 200) { ElMessage.success('登录成功'); router.push(route.query.redirect || '/admin') }
    else { ElMessage.error(res.message) }
  } finally { loading.value = false }
}

function goToFront() { router.push('/') }
</script>

<template>
  <div class="login-page">
    <div class="login-card">
      <div class="card-header">
        <svg
          viewBox="0 0 32 32"
          fill="none"
          width="40"
          height="40"
        ><rect
          width="32"
          height="32"
          rx="8"
          fill="url(#lg)"
        /><path
          d="M10 16L14 20L22 12"
          stroke="#fff"
          stroke-width="2.5"
          stroke-linecap="round"
          stroke-linejoin="round"
        /><defs><linearGradient
          id="lg"
          x1="0"
          y1="0"
          x2="32"
          y2="32"
        ><stop stop-color="#6366f1" /><stop offset="1" stop-color="#818cf8" /></linearGradient></defs></svg>
        <h1>管理后台</h1>
        <p>请输入管理员账号登录</p>
      </div>

      <form class="login-form" @submit.prevent="handleLogin">
        <div class="input-group">
          <label>用户名</label>
          <div class="input-wrapper">
            <svg
              class="input-icon"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="1.5"
            ><path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
            <input
              v-model="form.username"
              type="text"
              placeholder="请输入管理员用户名"
              autocomplete="username"
            >
          </div>
        </div>

        <div class="input-group">
          <label>密码</label>
          <div class="input-wrapper">
            <svg
              class="input-icon"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="1.5"
            ><path d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
            <input
              v-model="form.password"
              type="password"
              placeholder="请输入密码"
              autocomplete="current-password"
            >
          </div>
        </div>

        <button type="submit" class="btn-login" :disabled="loading">
          <span v-if="loading" class="spinner" />
          <span v-else>登 录</span>
        </button>
      </form>

      <div class="card-footer">
        <a href="#" @click.prevent="goToFront">
          <svg
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="1.5"
            width="14"
            height="14"
          ><path d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
          返回前台
        </a>
      </div>
    </div>
  </div>
</template>

<style scoped>
.login-page {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  background: #f5f7fa;
  padding: 2rem;
}

.login-card {
  width: 100%;
  max-width: 400px;
  background: white;
  border-radius: 16px;
  padding: 40px 32px;
  border: 1px solid #f1f5f9;
  box-shadow: 0 8px 32px rgba(0,0,0,0.06);
}

.card-header {
  text-align: center;
  margin-bottom: 32px;
}

.card-header h1 {
  font-size: 1.5rem;
  font-weight: 700;
  color: #1e293b;
  margin-top: 16px;
  margin-bottom: 4px;
}

.card-header p {
  color: #94a3b8;
  font-size: 0.9rem;
}

.login-form {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.input-group label {
  display: block;
  font-size: 0.85rem;
  font-weight: 500;
  color: #475569;
  margin-bottom: 6px;
}

.input-wrapper {
  position: relative;
  display: flex;
  align-items: center;
}

.input-icon {
  position: absolute;
  left: 12px;
  width: 18px;
  height: 18px;
  color: #94a3b8;
  pointer-events: none;
}

.input-wrapper input {
  width: 100%;
  padding: 10px 12px 10px 40px;
  border: 1.5px solid #e2e8f0;
  border-radius: 10px;
  font-size: 0.9rem;
  background: #f8fafc;
  transition: all 0.2s;
  outline: none;
  color: #1e293b;
}

.input-wrapper input:focus {
  border-color: #6366f1;
  box-shadow: 0 0 0 3px rgba(99,102,241,0.08);
  background: white;
}

.input-wrapper input::placeholder { color: #cbd5e1; }

.btn-login {
  width: 100%;
  padding: 11px;
  background: #6366f1;
  color: white;
  border: none;
  border-radius: 10px;
  font-size: 0.95rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s;
  display: flex;
  align-items: center;
  justify-content: center;
  min-height: 44px;
  margin-top: 4px;
}

.btn-login:hover:not(:disabled) { background: #4f46e5; }
.btn-login:disabled { opacity: 0.7; cursor: not-allowed; }

.spinner {
  width: 18px;
  height: 18px;
  border: 2px solid rgba(255,255,255,0.3);
  border-top-color: white;
  border-radius: 50%;
  animation: spin 0.6s linear infinite;
}

@keyframes spin { to { transform: rotate(360deg); } }

.card-footer {
  margin-top: 24px;
  text-align: center;
}

.card-footer a {
  color: #6366f1;
  text-decoration: none;
  font-weight: 500;
  font-size: 0.85rem;
  display: inline-flex;
  align-items: center;
  gap: 4px;
  transition: color 0.2s;
}

.card-footer a:hover { color: #4f46e5; }
</style>
