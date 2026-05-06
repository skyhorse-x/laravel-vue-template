import axios from 'axios'
import router from '@/router'

const instance = axios.create({
  baseURL: '/api',
  timeout: 10000
})

instance.interceptors.request.use((config) => {
  // 根据请求 URL 自动选择正确的 token
  const url = config.url || ''
  if (url.startsWith('/admin')) {
    const adminToken = localStorage.getItem('admin_token')
    if (adminToken) {
      config.headers.Authorization = `Bearer ${adminToken}`
    }
  } else {
    const userToken = localStorage.getItem('user_token')
    if (userToken) {
      config.headers.Authorization = `Bearer ${userToken}`
    }
  }

  return config
})

instance.interceptors.response.use(
  (response) => response.data,
  (error) => {
    if (error.response?.data) {
      const status = error.response.status
      const data = error.response.data

      // 401 未登录/token 过期，清除对应 token 并跳转登录页
      if (status === 401) {
        const url = error.config?.url || ''
        if (url.startsWith('/admin') || url.startsWith('admin')) {
          localStorage.removeItem('admin_token')
          // 避免在登录页重复跳转
          if (router.currentRoute.value.path !== '/admin/login') {
            router.push({ path: '/admin/login', query: { redirect: router.currentRoute.value.fullPath } })
          }
        } else {
          localStorage.removeItem('user_token')
          if (router.currentRoute.value.path !== '/login') {
            router.push({ path: '/login', query: { redirect: router.currentRoute.value.fullPath } })
          }
        }
      }

      return data
    }
    return { code: 500, message: '网络错误' }
  }
)

export default {
  user: {
    register(data) {
      return instance.post('/auth/register', data)
    },
    login(account, password, loginType = 'username') {
      return instance.post('/auth/login', { account, password, login_type: loginType })
    },
    logout() {
      return instance.post('/auth/logout')
    },
    me() {
      return instance.get('/auth/me')
    },
    updateProfile(data) {
      return instance.put('/auth/profile', data)
    },
    changePassword(oldPassword, newPassword) {
      return instance.put('/auth/change-password', { oldPassword, newPassword, newPassword_confirmation: newPassword })
    },
    forgotPassword(email) {
      return instance.post('/auth/forgot-password', { email })
    },
    resetPassword(email, token, password) {
      return instance.post('/auth/reset-password', { email, token, password, password_confirmation: password })
    },
    getBalance() {
      return instance.get('/user/balance')
    },
    notifications: {
      list(params) {
        return instance.get('/user/notifications', { params })
      },
      show(id) {
        return instance.get(`/user/notifications/${id}`)
      },
      markRead(id) {
        return instance.post(`/user/notifications/${id}/read`)
      },
      markAllRead() {
        return instance.post('/user/notifications/read-all')
      },
      delete(id) {
        return instance.delete(`/user/notifications/${id}`)
      }
    }
  },
  admin: {
    login(username, password) {
      return instance.post('/admin/login', { username, password })
    },
    logout() {
      return instance.post('/admin/logout')
    },
    me() {
      return instance.get('/admin/me')
    },
    dashboard() {
      return instance.get('/admin/dashboard')
    },
    updateProfile(data) {
      return instance.put('/admin/profile', data)
    },
    changePassword(oldPassword, newPassword) {
      return instance.put('/admin/change-password', { oldPassword, newPassword, newPassword_confirmation: newPassword })
    },
    forgotPassword(email) {
      return instance.post('/admin/forgot-password', { email })
    },
    resetPassword(email, token, password) {
      return instance.post('/admin/reset-password', { email, token, password, password_confirmation: password })
    },
    notifications: {
      list(params) {
        return instance.get('/admin/notifications', { params })
      },
      create(data) {
        return instance.post('/admin/notifications', data)
      },
      update(id, data) {
        return instance.put(`/admin/notifications/${id}`, data)
      },
      delete(id) {
        return instance.delete(`/admin/notifications/${id}`)
      },
      batchDelete(ids) {
        return instance.delete('/admin/notifications/batch', { data: { ids } })
      }
    },
    operationLogs: {
      list(params) {
        return instance.get('/admin/operation-logs', { params })
      },
      modules() {
        return instance.get('/admin/operation-logs/modules')
      },
      show(id) {
        return instance.get(`/admin/operation-logs/${id}`)
      },
      delete(id) {
        return instance.delete(`/admin/operation-logs/${id}`)
      },
      clear(params) {
        return instance.delete('/admin/operation-logs', { data: params })
      }
    },
    ipBlacklist: {
      list(params) {
        return instance.get('/admin/ip-blacklist', { params })
      },
      create(data) {
        return instance.post('/admin/ip-blacklist', data)
      },
      update(id, data) {
        return instance.put(`/admin/ip-blacklist/${id}`, data)
      },
      delete(id) {
        return instance.delete(`/admin/ip-blacklist/${id}`)
      },
      batchDelete(ids) {
        return instance.delete('/admin/ip-blacklist/batch', { data: { ids } })
      }
    },
    products: {
      list(params) {
        return instance.get('/admin/products', { params })
      },
      get(id) {
        return instance.get(`/admin/products/${id}`)
      },
      create(data) {
        return instance.post('/admin/products', data)
      },
      update(id, data) {
        return instance.put(`/admin/products/${id}`, data)
      },
      delete(id) {
        return instance.delete(`/admin/products/${id}`)
      },
      batchDelete(ids) {
        return instance.delete('/admin/products/batch', { data: { ids } })
      },
      categories() {
        return instance.get('/admin/products/categories')
      },
      createCategory(data) {
        return instance.post('/admin/products/categories', data)
      },
      updateCategory(id, data) {
        return instance.put(`/admin/products/categories/${id}`, data)
      },
      deleteCategory(id) {
        return instance.delete(`/admin/products/categories/${id}`)
      }
    },
    notificationTemplates: {
      list(params) {
        return instance.get('/admin/notification-templates', { params })
      },
      get(id) {
        return instance.get(`/admin/notification-templates/${id}`)
      },
      create(data) {
        return instance.post('/admin/notification-templates', data)
      },
      update(id, data) {
        return instance.put(`/admin/notification-templates/${id}`, data)
      },
      delete(id) {
        return instance.delete(`/admin/notification-templates/${id}`)
      }
    }
  },
  users: {
    list(params) {
      return instance.get('/admin/users', { params })
    },
    get(id) {
      return instance.get(`/admin/users/${id}`)
    },
    create(data) {
      return instance.post('/admin/users', data)
    },
    update(id, data) {
      return instance.put(`/admin/users/${id}`, data)
    },
    delete(id) {
      return instance.delete(`/admin/users/${id}`)
    },
    batchDelete(ids) {
      return instance.delete('/admin/users/batch', { data: { ids } })
    }
  },
  admins: {
    list(params) {
      return instance.get('/admin/admins', { params })
    },
    get(id) {
      return instance.get(`/admin/admins/${id}`)
    },
    create(data) {
      return instance.post('/admin/admins', data)
    },
    update(id, data) {
      return instance.put(`/admin/admins/${id}`, data)
    },
    delete(id) {
      return instance.delete(`/admin/admins/${id}`)
    }
  },
  roles: {
    list(params) {
      return instance.get('/admin/roles', { params })
    },
    get(id) {
      return instance.get(`/admin/roles/${id}`)
    },
    create(data) {
      return instance.post('/admin/roles', data)
    },
    update(id, data) {
      return instance.put(`/admin/roles/${id}`, data)
    },
    delete(id) {
      return instance.delete(`/admin/roles/${id}`)
    },
    permissions() {
      return instance.get('/admin/permissions')
    }
  },
  articles: {
    list(params) {
      return instance.get('/admin/articles', { params })
    },
    get(id) {
      return instance.get(`/admin/articles/${id}`)
    },
    create(data) {
      return instance.post('/admin/articles', data)
    },
    update(id, data) {
      return instance.put(`/admin/articles/${id}`, data)
    },
    delete(id) {
      return instance.delete(`/admin/articles/${id}`)
    },
    frontList(params) {
      return instance.get('/front/articles', { params })
    },
    frontGet(id) {
      return instance.get(`/front/articles/${id}`)
    },
    categories() {
      return instance.get('/front/articles/categories')
    }
  },
  products: {
    list(params) {
      return instance.get('/front/products', { params })
    },
    get(id) {
      return instance.get(`/front/products/${id}`)
    },
    categories() {
      return instance.get('/front/products/categories')
    }
  },
  menus: {
    list() {
      return instance.get('/admin/menus')
    },
    all() {
      return instance.get('/admin/menus/all')
    },
    get(id) {
      return instance.get(`/admin/menus/${id}`)
    },
    create(data) {
      return instance.post('/admin/menus', data)
    },
    update(id, data) {
      return instance.put(`/admin/menus/${id}`, data)
    },
    delete(id) {
      return instance.delete(`/admin/menus/${id}`)
    }
  },
  financial: {
    list(params) {
      return instance.get('/admin/financial', { params })
    },
    get(id) {
      return instance.get(`/admin/financial/${id}`)
    },
    create(data) {
      return instance.post('/admin/financial', data)
    },
    update(id, data) {
      return instance.put(`/admin/financial/${id}`, data)
    },
    statistics(params) {
      return instance.get('/admin/financial/statistics', { params })
    },
    userList(params) {
      return instance.get('/financial', { params })
    },
    userGet(id) {
      return instance.get(`/financial/${id}`)
    },
    createDeposit(data) {
      return instance.post('/financial/deposit', data)
    },
    createWithdraw(data) {
      return instance.post('/financial/withdraw', data)
    }
  },
  userArticles: {
    list(params) {
      return instance.get('/user/articles', { params })
    },
    get(id) {
      return instance.get(`/user/articles/${id}`)
    },
    create(data) {
      return instance.post('/user/articles', data)
    },
    update(id, data) {
      return instance.put(`/user/articles/${id}`, data)
    },
    delete(id) {
      return instance.delete(`/user/articles/${id}`)
    }
  },
  referral: {
    getInfo(code) {
      return instance.get(`/front/referral/info/${code}`)
    },
    getReferralCode() {
      return instance.get('/referral')
    }
  },
  settings: {
    list(params) {
      return instance.get('/admin/settings', { params })
    },
    get(key) {
      return instance.get(`/admin/settings/${key}`)
    },
    getByGroup(group) {
      return instance.get(`/admin/settings/group/${group}`)
    },
    save(data) {
      return instance.post('/admin/settings', data)
    },
    update(key, value) {
      return instance.put(`/admin/settings/${key}`, { value })
    },
    updateByGroup(group, settings) {
      return instance.put(`/admin/settings/group/${group}`, { settings })
    }
  }
}
