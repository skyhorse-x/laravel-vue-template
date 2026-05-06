<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useUserStore } from '@/stores/user'
import { useI18n } from 'vue-i18n'
import { ElMessage } from 'element-plus'

const { t } = useI18n()
const router = useRouter()
const userStore = useUserStore()
const loading = ref(false)
const form = ref({ email: '' })

async function handleSubmit() {
  if (!form.value.email) {
    ElMessage.error('请输入邮箱')
    return
  }

  loading.value = true
  try {
    const res = await userStore.forgotPassword(form.value.email)
    if (res.code === 200) {
      ElMessage.success(res.message)
      router.push('/login')
    } else {
      ElMessage.error(res.message)
    }
  } finally {
    loading.value = false
  }
}

function goToLogin() {
  router.push('/login')
}
</script>

<template>
  <div class="forgot-page">
    <div class="forgot-left">
      <div class="brand-content">
        <div class="logo-icon">
          <svg viewBox="0 0 40 40" fill="none"><rect
            width="40"
            height="40"
            rx="10"
            fill="white"
            fill-opacity="0.2"
          /><path
            d="M12 20L18 26L28 14"
            stroke="white"
            stroke-width="3"
            stroke-linecap="round"
            stroke-linejoin="round"
          /></svg>
        </div>
        <h1>TemplatePro</h1>
        <p>安全 · 可靠</p>
      </div>
    </div>

    <div class="forgot-right">
      <div class="forgot-form-wrapper">
        <div class="form-header">
          <div class="icon-circle">
            <svg
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="1.5"
            >
              <path d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
            </svg>
          </div>
          <h2>{{ t('auth.forgotTitle') }}</h2>
          <p>{{ t('auth.forgotSubtitle') }}</p>
        </div>

        <form class="forgot-form" @submit.prevent="handleSubmit">
          <div class="input-group">
            <label>{{ t('member.email') }}</label>
            <div class="input-wrapper">
              <svg
                class="input-icon"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="1.5"
              >
                <path d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
              </svg>
              <input v-model="form.email" type="email" :placeholder="t('auth.emailPlaceholder')">
            </div>
          </div>

          <button type="submit" class="btn-primary" :disabled="loading">
            <span v-if="loading" class="spinner" />
            <span v-else>{{ t('auth.sendResetBtn') }}</span>
          </button>
        </form>

        <div class="form-footer">
          <a href="#" @click.prevent="goToLogin">
            <svg
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="1.5"
              width="16"
              height="16"
            >
              <path d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            {{ t('auth.backToLogin') }}
          </a>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.forgot-page { display: flex; min-height: 100vh; }

.forgot-left {
  flex: 1;
  background: linear-gradient(135deg, #6366f1 0%, #06b6d4 100%);
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 3rem;
  position: relative;
  overflow: hidden;
}

.forgot-left::before {
  content: '';
  position: absolute;
  top: -50%; left: -50%;
  width: 200%; height: 200%;
  background: radial-gradient(circle, rgba(255,255,255,0.05) 0%, transparent 60%);
  animation: float 15s ease-in-out infinite;
}

@keyframes float {
  0%, 100% { transform: translateY(0) rotate(0deg); }
  50% { transform: translateY(-20px) rotate(5deg); }
}

.brand-content { position: relative; z-index: 1; color: white; max-width: 400px; }
.logo-icon { margin-bottom: 2rem; }
.logo-icon svg { width: 56px; height: 56px; }

.brand-content h1 { font-size: 2.5rem; font-weight: 700; margin-bottom: 0.5rem; letter-spacing: 1px; }
.brand-content p { font-size: 1.1rem; opacity: 0.85; letter-spacing: 2px; }

.forgot-right {
  flex: 1;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 2rem;
  background: #f8fafc;
}

.forgot-form-wrapper { width: 100%; max-width: 420px; }
.form-header { margin-bottom: 2rem; text-align: center; }

.icon-circle {
  width: 64px;
  height: 64px;
  border-radius: 50%;
  background: linear-gradient(135deg, rgba(99,102,241,0.1), rgba(6,182,212,0.1));
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 1.25rem;
  color: #6366f1;
}

.icon-circle svg { width: 28px; height: 28px; }

.form-header h2 { font-size: 1.75rem; font-weight: 700; color: #1e293b; margin-bottom: 0.5rem; }
.form-header p { color: #94a3b8; font-size: 0.95rem; }

.forgot-form { display: flex; flex-direction: column; gap: 1.25rem; }

.input-group label { display: block; font-size: 0.875rem; font-weight: 500; color: #475569; margin-bottom: 0.5rem; }

.input-wrapper { position: relative; display: flex; align-items: center; }
.input-icon { position: absolute; left: 14px; width: 18px; height: 18px; color: #94a3b8; pointer-events: none; }

.input-wrapper input {
  width: 100%;
  padding: 0.75rem 0.75rem 0.75rem 2.75rem;
  border: 1.5px solid #e2e8f0;
  border-radius: 10px;
  font-size: 0.95rem;
  background: white;
  transition: all 0.2s ease;
  outline: none;
  color: #1e293b;
}

.input-wrapper input:focus { border-color: #6366f1; box-shadow: 0 0 0 3px rgba(99,102,241,0.1); }
.input-wrapper input::placeholder { color: #cbd5e1; }

.btn-primary {
  width: 100%;
  padding: 0.85rem;
  background: linear-gradient(135deg, #6366f1 0%, #06b6d4 100%);
  color: white;
  border: none;
  border-radius: 10px;
  font-size: 1rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
  justify-content: center;
  min-height: 48px;
}

.btn-primary:hover:not(:disabled) { transform: translateY(-1px); box-shadow: 0 8px 25px rgba(99,102,241,0.4); }
.btn-primary:disabled { opacity: 0.7; cursor: not-allowed; }

.spinner {
  width: 20px; height: 20px;
  border: 2px solid rgba(255,255,255,0.3);
  border-top-color: white;
  border-radius: 50%;
  animation: spin 0.6s linear infinite;
}

@keyframes spin { to { transform: rotate(360deg); } }

.form-footer { margin-top: 1.5rem; text-align: center; }
.form-footer a {
  color: #6366f1;
  text-decoration: none;
  font-weight: 500;
  font-size: 0.9rem;
  display: inline-flex;
  align-items: center;
  gap: 0.35rem;
  transition: color 0.2s;
}

.form-footer a:hover { color: #4f46e5; }

@media (max-width: 768px) { .forgot-left { display: none; } }
</style>
