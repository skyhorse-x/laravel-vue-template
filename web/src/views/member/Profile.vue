<script setup>
import { ref, onMounted } from 'vue'
import { useUserStore } from '@/stores/user'
import { useI18n } from 'vue-i18n'
import { ElMessage } from 'element-plus'

const { t } = useI18n()
const userStore = useUserStore()
const form = ref({ name: '', email: '', phone: '', username: '' })

onMounted(() => {
  if (userStore.user) {
    form.value.name = userStore.user.name || ''
    form.value.email = userStore.user.email || ''
    form.value.phone = userStore.user.phone || ''
    form.value.username = userStore.user.username || ''
  }
})

async function handleSubmit() {
  const res = await userStore.updateProfile(form.value)
  if (res.code === 200) {
    ElMessage.success(t('member.save'))
  } else {
    ElMessage.error(res.message)
  }
}
</script>

<template>
  <div class="profile-page">
    <div class="page-card">
      <h3>{{ t('member.profile') }}</h3>
      <form class="profile-form" @submit.prevent="handleSubmit">
        <div class="form-grid">
          <div class="input-group">
            <label>{{ t('member.username') }}</label>
            <div class="input-wrapper">
              <svg
                class="input-icon"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="1.5"
              ><path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
              <input v-model="form.username" type="text" disabled>
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
              ><path d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
              <input v-model="form.name" type="text" :placeholder="t('auth.nicknamePlaceholder')">
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
              ><path d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
              <input v-model="form.email" type="email" disabled>
            </div>
          </div>
          <div class="input-group">
            <label>{{ t('member.phone') }}</label>
            <div class="input-wrapper">
              <svg
                class="input-icon"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="1.5"
              ><path d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" /></svg>
              <input v-model="form.phone" type="text" placeholder="请输入手机号">
            </div>
          </div>
        </div>
        <button type="submit" class="btn-save">
          {{ t('member.save') }}
        </button>
      </form>
    </div>
  </div>
</template>

<style scoped>
.profile-page { max-width: 700px; }

.page-card {
  background: white;
  border-radius: 16px;
  padding: 2rem;
  border: 1px solid #f1f5f9;
}

.page-card h3 {
  font-size: 1.15rem;
  font-weight: 700;
  color: #1e293b;
  margin-bottom: 1.5rem;
}

.form-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 1.25rem;
}

.input-group label {
  display: block;
  font-size: 0.85rem;
  font-weight: 500;
  color: #475569;
  margin-bottom: 0.4rem;
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
  padding: 0.7rem 0.7rem 0.7rem 2.5rem;
  border: 1.5px solid #e2e8f0;
  border-radius: 10px;
  font-size: 0.9rem;
  background: #fafbfc;
  transition: all 0.2s;
  outline: none;
  color: #1e293b;
}

.input-wrapper input:not(:disabled):focus {
  border-color: #6366f1;
  box-shadow: 0 0 0 3px rgba(99,102,241,0.1);
  background: white;
}

.input-wrapper input:disabled {
  color: #94a3b8;
  cursor: not-allowed;
}

.btn-save {
  margin-top: 1.5rem;
  padding: 0.7rem 2rem;
  background: linear-gradient(135deg, #667eea, #764ba2);
  color: white;
  border: none;
  border-radius: 10px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s;
}

.btn-save:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(102,126,234,0.35); }

@media (max-width: 768px) {
  .form-grid { grid-template-columns: 1fr; }
}
</style>
