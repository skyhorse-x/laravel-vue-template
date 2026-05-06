<script setup>
import { ref } from 'vue'
import { useUserStore } from '@/stores/user'
import { useI18n } from 'vue-i18n'
import { ElMessage } from 'element-plus'

const { t } = useI18n()
const userStore = useUserStore()
const form = ref({ oldPassword: '', newPassword: '', newPassword_confirmation: '' })

async function handleSubmit() {
  if (!form.value.oldPassword || !form.value.newPassword) {
    ElMessage.error('请填写完整信息')
    return
  }
  if (form.value.newPassword !== form.value.newPassword_confirmation) {
    ElMessage.error('两次密码不一致')
    return
  }
  const res = await userStore.changePassword(form.value.oldPassword, form.value.newPassword)
  if (res.code === 200) {
    ElMessage.success('修改成功')
    form.value = { oldPassword: '', newPassword: '', newPassword_confirmation: '' }
  } else {
    ElMessage.error(res.message)
  }
}
</script>

<template>
  <div class="change-password-page">
    <div class="page-card">
      <h3>{{ t('member.changePassword') }}</h3>
      <form class="password-form" @submit.prevent="handleSubmit">
        <div class="input-group">
          <label>{{ t('member.oldPassword') }}</label>
          <div class="input-wrapper">
            <svg
              class="input-icon"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="1.5"
            ><path d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
            <input v-model="form.oldPassword" type="password" :placeholder="t('auth.passwordPlaceholder')">
          </div>
        </div>
        <div class="input-group">
          <label>{{ t('member.newPassword') }}</label>
          <div class="input-wrapper">
            <svg
              class="input-icon"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="1.5"
            ><path d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" /></svg>
            <input v-model="form.newPassword" type="password" :placeholder="t('auth.passwordPlaceholder')">
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
            ><path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" /></svg>
            <input v-model="form.newPassword_confirmation" type="password" :placeholder="t('auth.confirmPasswordPlaceholder')">
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
.change-password-page { max-width: 500px; }

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

.password-form {
  display: flex;
  flex-direction: column;
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

.input-wrapper input:focus {
  border-color: #6366f1;
  box-shadow: 0 0 0 3px rgba(99,102,241,0.1);
  background: white;
}

.btn-save {
  padding: 0.7rem 2rem;
  background: linear-gradient(135deg, #6366f1, #06b6d4);
  color: white;
  border: none;
  border-radius: 10px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s;
  align-self: flex-start;
}

.btn-save:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(99,102,241,0.35); }
</style>
