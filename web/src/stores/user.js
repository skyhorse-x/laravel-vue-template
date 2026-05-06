import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import api from '@/api'

export const useUserStore = defineStore('user', () => {
  const token = ref(localStorage.getItem('user_token') || '')
  const user = ref(null)

  const isLoggedIn = computed(() => !!token.value)

  async function register(data) {
    const res = await api.user.register(data)
    if (res.code === 200) {
      token.value = res.data.token
      user.value = res.data.user
      localStorage.setItem('user_token', token.value)
    }
    return res
  }

  async function login(account, password, loginType = 'username') {
    const res = await api.user.login(account, password, loginType)
    if (res.code === 200) {
      token.value = res.data.token
      user.value = res.data.user
      localStorage.setItem('user_token', token.value)
    }
    return res
  }

  async function logout() {
    await api.user.logout()
    token.value = ''
    user.value = null
    localStorage.removeItem('user_token')
  }

  async function getInfo() {
    if (!token.value) return null
    const res = await api.user.me()
    if (res.code === 200) {
      user.value = res.data
    }
    return res
  }

  async function updateProfile(data) {
    const res = await api.user.updateProfile(data)
    if (res.code === 200) {
      user.value = res.data
    }
    return res
  }

  async function changePassword(oldPassword, newPassword) {
    return await api.user.changePassword(oldPassword, newPassword)
  }

  async function forgotPassword(email) {
    return await api.user.forgotPassword(email)
  }

  async function resetPassword(email, token, password) {
    return await api.user.resetPassword(email, token, password)
  }

  return {
    token,
    user,
    isLoggedIn,
    register,
    login,
    logout,
    getInfo,
    updateProfile,
    changePassword,
    forgotPassword,
    resetPassword
  }
})
