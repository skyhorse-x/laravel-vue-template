import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import api from '@/api'

export const useAdminStore = defineStore('admin', () => {
  const token = ref(localStorage.getItem('admin_token') || '')
  const admin = ref(null)

  const isLoggedIn = computed(() => !!token.value)

  async function login(username, password) {
    const res = await api.admin.login(username, password)
    if (res.code === 200) {
      token.value = res.data.token
      admin.value = res.data.admin
      localStorage.setItem('admin_token', token.value)
    }
    return res
  }

  async function logout() {
    await api.admin.logout()
    token.value = ''
    admin.value = null
    localStorage.removeItem('admin_token')
  }

  async function getInfo() {
    if (!token.value) return null
    const res = await api.admin.me()
    if (res.code === 200) {
      admin.value = res.data
    }
    return res
  }

  async function updateProfile(data) {
    const res = await api.admin.updateProfile(data)
    if (res.code === 200) {
      admin.value = res.data
    }
    return res
  }

  async function changePassword(oldPassword, newPassword) {
    return await api.admin.changePassword(oldPassword, newPassword)
  }

  return {
    token,
    admin,
    isLoggedIn,
    login,
    logout,
    getInfo,
    updateProfile,
    changePassword
  }
})
