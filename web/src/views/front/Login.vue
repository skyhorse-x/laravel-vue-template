<script setup>
import { ref, computed } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useUserStore } from '@/stores/user'
import { useI18n } from 'vue-i18n'
import { ElMessage } from 'element-plus'

const { t, locale } = useI18n()
const router = useRouter()
const route = useRoute()
const userStore = useUserStore()
const loading = ref(false)
const loginType = ref('username')
const langDropdownOpen = ref(false)

const languages = [
  { code: 'zh', label: '中文', flag: '🇨🇳' },
  { code: 'en', label: 'English', flag: '🇺🇸' },
]

const form = ref({ account: '', password: '' })

const placeholder = computed(() => {
  return loginType.value === 'email' ? t('auth.emailPlaceholder') : t('auth.accountPlaceholder')
})

const inputType = computed(() => {
  return loginType.value === 'email' ? 'email' : 'text'
})

function toggleLoginType() {
  loginType.value = loginType.value === 'username' ? 'email' : 'username'
  form.value.account = ''
}

function setLang(code) {
  locale.value = code
  localStorage.setItem('locale', code)
  langDropdownOpen.value = false
}

async function handleLogin() {
  if (!form.value.account || !form.value.password) {
    ElMessage.error('请填写完整信息')
    return
  }
  loading.value = true
  try {
    const res = await userStore.login(form.value.account, form.value.password, loginType.value)
    if (res.code === 200) {
      ElMessage.success('登录成功')
      router.push(route.query.redirect || '/member')
    } else {
      ElMessage.error(res.message)
    }
  } finally {
    loading.value = false
  }
}

function goToRegister() { router.push('/register') }
function goToForgotPassword() { router.push('/forgot-password') }
</script>

<template>
  <div class="login-page" @click="langDropdownOpen = false">
    <div class="login-left">
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
        <p>高效 · 安全 · 便捷</p>
        <div class="features">
          <div class="feature-item">
            <span class="dot" /><span>{{ t('home.feat1Title') }}</span>
          </div>
          <div class="feature-item">
            <span class="dot" /><span>{{ t('home.feat2Title') }}</span>
          </div>
          <div class="feature-item">
            <span class="dot" /><span>{{ t('home.feat4Title') }}</span>
          </div>
        </div>
      </div>
    </div>

    <div class="login-right">
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
          <span>{{ languages.find(l => l.code === locale)?.flag }} {{ languages.find(l => l.code === locale)?.label }}</span>
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
      <div class="login-form-wrapper">
        <div class="form-header">
          <h2>{{ t('auth.loginTitle') }}</h2>
          <p>{{ t('auth.loginSubtitle') }}</p>
        </div>

        <form class="login-form" @submit.prevent="handleLogin">
          <div class="input-group">
            <label>{{ loginType === 'email' ? t('member.email') : t('member.username') }}</label>
            <div class="input-wrapper">
              <svg
                class="input-icon"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="1.5"
              >
                <path v-if="loginType === 'email'" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                <path v-else d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
              </svg>
              <input
                v-model="form.account"
                :type="inputType"
                :placeholder="placeholder"
                autocomplete="username"
              >
            </div>
          </div>

          <div class="input-group">
            <label>{{ t('auth.passwordPlaceholder').replace('请输入', '').replace('Enter ', '') || 'Password' }}</label>
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
                :placeholder="t('auth.passwordPlaceholder')"
                autocomplete="current-password"
              >
            </div>
          </div>

          <div class="form-options">
            <a href="#" class="switch-type" @click.prevent="toggleLoginType">
              {{ loginType === 'email' ? t('auth.switchToUsername') : t('auth.switchToEmail') }}
            </a>
            <a href="#" class="forgot-link" @click.prevent="goToForgotPassword">{{ t('auth.forgotPassword') }}</a>
          </div>

          <button type="submit" class="btn-primary" :disabled="loading">
            <span v-if="loading" class="spinner" />
            <span v-else>{{ t('auth.loginBtn') }}</span>
          </button>
        </form>

        <div class="form-footer">
          <span>{{ t('auth.noAccount') }}</span>
          <a href="#" @click.prevent="goToRegister">{{ t('auth.registerNow') }}</a>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.login-page { display: flex; min-height: 100vh; }

.login-left {
  flex: 1;
  background: linear-gradient(135deg, #6366f1 0%, #06b6d4 100%);
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 3rem;
  position: relative;
  overflow: hidden;
}

.login-left::before {
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
.brand-content p { font-size: 1.1rem; opacity: 0.85; margin-bottom: 2.5rem; letter-spacing: 3px; }

.features { display: flex; flex-direction: column; gap: 1rem; }
.feature-item { display: flex; align-items: center; gap: 0.75rem; font-size: 0.95rem; opacity: 0.9; }
.dot { width: 6px; height: 6px; border-radius: 50%; background: white; flex-shrink: 0; }

.login-right {
  flex: 1;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 2rem;
  background: #f8fafc;
  position: relative;
}

/* Language dropdown */
.lang-select {
  position: absolute;
  top: 1.5rem;
  right: 1.5rem;
}

.lang-trigger {
  display: flex;
  align-items: center;
  gap: 0.4rem;
  padding: 0.4rem 0.8rem;
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
  min-width: 150px;
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
  padding: 0.55rem 0.75rem;
  border: none;
  background: none;
  color: #475569;
  font-size: 0.85rem;
  border-radius: 7px;
  cursor: pointer;
  transition: all 0.15s;
  text-align: left;
}

.lang-option:hover { background: #f1f5f9; color: #1e293b; }
.lang-option.active { background: rgba(99,102,241,0.08); color: #6366f1; font-weight: 600; }
.lang-option svg { margin-left: auto; color: #6366f1; }

.login-form-wrapper { width: 100%; max-width: 420px; }
.form-header { margin-bottom: 2rem; }
.form-header h2 { font-size: 1.75rem; font-weight: 700; color: #1e293b; margin-bottom: 0.5rem; }
.form-header p { color: #94a3b8; font-size: 0.95rem; }

.login-form { display: flex; flex-direction: column; gap: 1.25rem; }

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

.form-options { display: flex; justify-content: space-between; align-items: center; font-size: 0.85rem; }
.switch-type { color: #6366f1; text-decoration: none; font-weight: 500; transition: color 0.2s; }
.switch-type:hover { color: #4f46e5; }
.forgot-link { color: #94a3b8; text-decoration: none; transition: color 0.2s; }
.forgot-link:hover { color: #6366f1; }

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

@media (max-width: 768px) { .login-left { display: none; } }
</style>
