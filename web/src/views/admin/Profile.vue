<script setup>
import { ref, onMounted } from 'vue'
import { useAdminStore } from '@/stores/admin'
import { ElForm, ElFormItem, ElInput, ElButton, ElMessage } from 'element-plus'

const adminStore = useAdminStore()
const form = ref({ name: '', email: '', username: '' })
const passwordForm = ref({ oldPassword: '', newPassword: '', newPassword_confirmation: '' })

onMounted(() => {
  if (adminStore.admin) {
    form.value.name = adminStore.admin.name || ''
    form.value.email = adminStore.admin.email || ''
    form.value.username = adminStore.admin.username || ''
  }
})

async function updateProfile() {
  const res = await adminStore.updateProfile(form.value)
  if (res.code === 200) ElMessage.success('更新成功')
  else ElMessage.error(res.message)
}

async function changePassword() {
  if (!passwordForm.value.oldPassword || !passwordForm.value.newPassword) { ElMessage.error('请填写完整信息'); return }
  if (passwordForm.value.newPassword !== passwordForm.value.newPassword_confirmation) { ElMessage.error('两次密码不一致'); return }
  const res = await adminStore.changePassword(passwordForm.value.oldPassword, passwordForm.value.newPassword)
  if (res.code === 200) { ElMessage.success('修改成功'); passwordForm.value = { oldPassword: '', newPassword: '', newPassword_confirmation: '' } }
  else { ElMessage.error(res.message) }
}
</script>

<template>
  <div class="page-container">
    <div class="page-header">
      <h2 class="page-title">
        个人设置
      </h2>
    </div>

    <div class="card">
      <h3 class="card-subtitle">
        基本信息
      </h3>
      <ElForm :model="form" label-width="80px" style="max-width: 480px;">
        <ElFormItem label="用户名">
          <ElInput v-model="form.username" disabled />
        </ElFormItem>
        <ElFormItem label="姓名">
          <ElInput v-model="form.name" placeholder="请输入姓名" />
        </ElFormItem>
        <ElFormItem label="邮箱">
          <ElInput v-model="form.email" type="email" placeholder="请输入邮箱" />
        </ElFormItem>
        <ElFormItem>
          <ElButton type="primary" @click="updateProfile">
            保存修改
          </ElButton>
        </ElFormItem>
      </ElForm>
    </div>

    <div class="card" style="margin-top: 16px;">
      <h3 class="card-subtitle">
        修改密码
      </h3>
      <ElForm :model="passwordForm" label-width="80px" style="max-width: 480px;">
        <ElFormItem label="旧密码">
          <ElInput v-model="passwordForm.oldPassword" type="password" placeholder="请输入旧密码" />
        </ElFormItem>
        <ElFormItem label="新密码">
          <ElInput v-model="passwordForm.newPassword" type="password" placeholder="请输入新密码" />
        </ElFormItem>
        <ElFormItem label="确认密码">
          <ElInput v-model="passwordForm.newPassword_confirmation" type="password" placeholder="请再次输入密码" />
        </ElFormItem>
        <ElFormItem>
          <ElButton type="primary" @click="changePassword">
            修改密码
          </ElButton>
        </ElFormItem>
      </ElForm>
    </div>
  </div>
</template>

<style scoped>
.page-header { margin-bottom: 16px; }
.page-title { font-size: 1.1rem; font-weight: 700; color: #1e293b; }
.card { background: white; border-radius: 12px; padding: 24px; border: 1px solid #f1f5f9; }
.card-subtitle { font-size: 0.9rem; font-weight: 600; color: #475569; margin-bottom: 20px; padding-bottom: 12px; border-bottom: 1px solid #f1f5f9; }
</style>
