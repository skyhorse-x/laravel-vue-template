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

const form = ref({
  name: '',
  username: '',
  email: '',
  password: '',
  password_confirmation: '',
  referral_code: ''
})

async function handleRegister() {
  if (!form.value.name || !form.value.username || !form.value.email || !form.value.password) {
    ElMessage.error('请填写完整信息')
    return
  }

  if (form.value.password !== form.value.password_confirmation) {
    ElMessage.error('两次密码不一致')
    return
  }

  loading.value = true
  try {
    const res = await userStore.register(form.value)
    if (res.code === 200) {
      ElMessage.success('注册成功')
      router.push('/member')
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
  <div class="register-page">
    <div class="register-left">
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
        <p>加入我们，开启新体验</p>
      </div>
    </div>

    <div class="register-right">
      <div class="register-form-wrapper">
        <div class="form-header">
          <h2>{{ t('auth.registerTitle') }}</h2>
          <p>{{ t('auth.registerSubtitle') }}</p>
        </div>

        <form class="register-form" @submit.prevent="handleRegister">
          <div class="form-row">
            <div class="input-group">
              <label>{{ t('member.username') }}</label>
              <div class="input-wrapper">
                <svg
                  class="input-icon"
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="1.5"
                >
                  <path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                <input v-model="form.username" type="text" :placeholder="t('auth.usernamePlaceholder')">
              </div>
            </div>
            <div class="input-group">
              <label>{{ t('member.name') }}</label>
              <div class="input-wrapper">
                <svg
                  class="input-icon"
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="1.5"
                >
                  <path d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <input v-model="form.name" type="text" :placeholder="t('auth.nicknamePlaceholder')">
              </div>
            </div>
          </div>

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

          <div class="form-row">
            <div class="input-group">
              <label>{{ t('auth.passwordPlaceholder').replace('请输入', '').replace('Enter ', '') || 'Password' }}</label>
              <div class="input-wrapper">
                <svg
                  class="input-icon"
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="1.5"
                >
                  <path d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
                <input v-model="form.password" type="password" :placeholder="t('auth.passwordPlaceholder')">
              </div>
            </div>
            <div class="input-group">
              <label>{{ t('member.confirmPassword') }}</label>
              <div class="input-wrapper">
                <svg
                  class="input-icon"
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="1.5"
                >
                  <path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                </svg>
                <input v-model="form.password_confirmation" type="password" :placeholder="t('auth.confirmPasswordPlaceholder')">
              </div>
            </div>
          </div>

          <div class="input-group">
            <label>{{ t('member.referralCode') }} <span class="optional">({{ t('auth.referralOptional') }})</span></label>
            <div class="input-wrapper">
              <svg
                class="input-icon"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="1.5"
              >
                <path d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14" />
              </svg>
              <input v-model="form.referral_code" type="text" :placeholder="t('auth.referralPlaceholder')">
            </div>
          </div>

          <button type="submit" class="btn-primary" :disabled="loading">
            <span v-if="loading" class="spinner" />
            <span v-else>{{ t('auth.registerBtn') }}</span>
          </button>
        </form>

        <div class="form-footer">
          <span>{{ t('auth.hasAccount') }}</span>
          <a href="#" @click.prevent="goToLogin">{{ t('auth.loginNow') }}</a>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.register-page { display: flex; min-height: 100vh; }

.register-left {
  flex: 1;
  background: linear-gradient(135deg, #6366f1 0%, #06b6d4 100%);
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 3rem;
  position: relative;
  overflow: hidden;
}

.register-left::before {
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

.register-right {
  flex: 1;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 2rem;
  background: #f8fafc;
  overflow-y: auto;
}

.register-form-wrapper { width: 100%; max-width: 500px; }
.form-header { margin-bottom: 2rem; }
.form-header h2 { font-size: 1.75rem; font-weight: 700; color: #1e293b; margin-bottom: 0.5rem; }
.form-header p { color: #94a3b8; font-size: 0.95rem; }

.register-form { display: flex; flex-direction: column; gap: 1.25rem; }
.form-row { display: flex; gap: 1rem; }
.form-row .input-group { flex: 1; }

.input-group label { display: block; font-size: 0.875rem; font-weight: 500; color: #475569; margin-bottom: 0.5rem; }
.optional { color: #94a3b8; font-weight: 400; }

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
  margin-top: 0.5rem;
}

.btn-primary:hover:not(:disabled) { transform: translateY(-1px); box-shadow: 0 8px 25px rgba(99,102,241,0.4); }
.btn-primary:active:not(:disabled) { transform: translateY(0); }
.btn-primary:disabled { opacity: 0.7; cursor: not-allowed; }

.spinner {
  width: 20px; height: 20px;
  border: 2px solid rgba(255,255,255,0.3);
  border-top-color: white;
  border-radius: 50%;
  animation: spin 0.6s linear infinite;
}

@keyframes spin { to { transform: rotate(360deg); } }

.form-footer { margin-top: 1.5rem; text-align: center; font-size: 0.9rem; color: #94a3b8; }
.form-footer a { color: #6366f1; text-decoration: none; font-weight: 500; margin-left: 0.25rem; }
.form-footer a:hover { text-decoration: underline; }

@media (max-width: 768px) {
  .register-left { display: none; }
  .form-row { flex-direction: column; gap: 1.25rem; }
}
</style>
