import { createRouter, createWebHistory } from 'vue-router'
import { useAdminStore } from '@/stores/admin'
import { useUserStore } from '@/stores/user'

const routes = [
  {
    path: '/',
    name: 'Home',
    component: () => import('@/views/front/Home.vue')
  },
  {
    path: '/login',
    name: 'FrontLogin',
    component: () => import('@/views/front/Login.vue')
  },
  {
    path: '/register',
    name: 'FrontRegister',
    component: () => import('@/views/front/Register.vue')
  },
  {
    path: '/forgot-password',
    name: 'FrontForgotPassword',
    component: () => import('@/views/front/ForgotPassword.vue')
  },
  {
    path: '/reset-password',
    name: 'FrontResetPassword',
    component: () => import('@/views/front/ResetPassword.vue')
  },
  { path: '/articles',
    name: 'FrontArticles',
    component: () => import('@/views/front/Articles.vue')
  },
  {
    path: '/products',
    name: 'FrontProducts',
    component: () => import('@/views/front/Products.vue')
  },
  { path: '/product/:id',
    name: 'FrontProductDetail',
    component: () => import('@/views/front/ProductDetail.vue')
  },
  {
    path: '/article/:id',
    name: 'FrontArticleDetail',
    component: () => import('@/views/front/ArticleDetail.vue')
  },
  { path: '/member', name: 'MemberCenter', component: () => import('@/views/member/Layout.vue'),
    children: [
      { path: '', name: 'MemberHome', component: () => import('@/views/member/Home.vue') },
      { path: 'profile', name: 'MemberProfile', component: () => import('@/views/member/Profile.vue') },
      { path: 'change-password', name: 'MemberChangePassword', component: () => import('@/views/member/ChangePassword.vue') },
      { path: 'referral', name: 'MemberReferral', component: () => import('@/views/member/Referral.vue') },
      { path: 'financial', name: 'MemberFinancial', component: () => import('@/views/member/Financial.vue') },
      { path: 'articles', name: 'MemberArticles', component: () => import('@/views/member/Articles.vue') },
      { path: 'notifications', name: 'MemberNotifications', component: () => import('@/views/member/Notifications.vue') },
    ]
  },
  {
    path: '/admin/login',
    name: 'AdminLogin',
    component: () => import('@/views/admin/Login.vue'),
    meta: { guest: true }
  },
  {
    path: '/admin',
    name: 'AdminLayout',
    component: () => import('@/views/admin/Layout.vue'),
    meta: { requiresAdminAuth: true },
    children: [
      { path: '', name: 'AdminDashboard', component: () => import('@/views/admin/Dashboard.vue') },
      { path: 'users', name: 'AdminUsers', component: () => import('@/views/admin/Users.vue') },
      { path: 'admins', name: 'AdminAdmins', component: () => import('@/views/admin/Admins.vue') },
      { path: 'roles', name: 'AdminRoles', component: () => import('@/views/admin/Roles.vue') },
      { path: 'articles', name: 'AdminArticles', component: () => import('@/views/admin/Articles.vue') },
      { path: 'menus', name: 'AdminMenus', component: () => import('@/views/admin/Menus.vue') },
      { path: 'financial', name: 'AdminFinancial', component: () => import('@/views/admin/Financial.vue') },
      { path: 'notifications', name: 'AdminNotifications', component: () => import('@/views/admin/Notifications.vue') },
      { path: 'operation-logs', name: 'AdminOperationLogs', component: () => import('@/views/admin/OperationLogs.vue') },
      { path: 'ip-blacklist', name: 'AdminIpBlacklist', component: () => import('@/views/admin/IpBlacklist.vue') },
      { path: 'settings', name: 'AdminSettings', component: () => import('@/views/admin/Settings.vue') },
      { path: 'products', name: 'AdminProducts', component: () => import('@/views/admin/Products.vue') },
      { path: 'notification-templates', name: 'AdminNotificationTemplates', component: () => import('@/views/admin/NotificationTemplates.vue') },
      { path: 'profile', name: 'AdminProfile', component: () => import('@/views/admin/Profile.vue') },
    ]
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

router.beforeEach((to) => {
  const adminStore = useAdminStore()
  const userStore = useUserStore()

  // 后台页面需要 admin 登录
  if (to.matched.some(record => record.meta.requiresAdminAuth)) {
    if (!adminStore.token) {
      return { path: '/admin/login', query: { redirect: to.fullPath } }
    }
  }

  // 已登录 admin 访问登录页，跳转后台首页
  if (to.path === '/admin/login' && adminStore.token) {
    return '/admin'
  }

  // 前台会员页面需要 user 登录
  if (to.matched.some(record => record.path.startsWith('/member'))) {
    if (!userStore.token) {
      return { path: '/login', query: { redirect: to.fullPath } }
    }
  }
})

export default router
