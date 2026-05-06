<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useUserStore } from '@/stores/user'
import { useI18n } from 'vue-i18n'
import api from '@/api'

const { t, locale } = useI18n()
const router = useRouter()
const userStore = useUserStore()
const articles = ref([])
const categories = ref([])
const activeCategory = ref('')
const total = ref(0)
const page = ref(1)
const perPage = 9
const mobileMenuOpen = ref(false)
const langDropdownOpen = ref(false)

const languages = [
  { code: 'zh', label: '中文', flag: '🇨🇳' },
  { code: 'en', label: 'English', flag: '🇺🇸' },
]

onMounted(async () => {
  await Promise.all([loadArticles(), loadCategories()])
})

async function loadArticles() {
  const params = { page: page.value, per_page: perPage }
  if (activeCategory.value) params.category = activeCategory.value
  const res = await api.articles.frontList(params)
  if (res.code === 200) {
    articles.value = res.data.data
    total.value = res.data.total
  }
}

async function loadCategories() {
  const res = await api.articles.categories()
  if (res.code === 200) {
    categories.value = res.data
  }
}

function filterCategory(cat) {
  activeCategory.value = cat
  page.value = 1
  loadArticles()
}

function handlePageChange(val) {
  page.value = val
  loadArticles()
  window.scrollTo({ top: 0, behavior: 'smooth' })
}

function goToDetail(id) {
  router.push(`/article/${id}`)
}

function setLang(code) {
  locale.value = code
  localStorage.setItem('locale', code)
  langDropdownOpen.value = false
}

function goToLogin() { router.push('/login') }
function goToRegister() { router.push('/register') }
function logout() { userStore.logout() }

function handleClickOutside(e) {
  if (!e.target.closest('.lang-select')) langDropdownOpen.value = false
}

function stripHtml(html) {
  if (!html) return ''
  return html.replace(/<[^>]*>/g, '')
}

function formatDate(dateStr) {
  if (!dateStr) return ''
  const d = new Date(dateStr)
  return d.toLocaleDateString(locale.value === 'zh' ? 'zh-CN' : 'en-US', { year: 'numeric', month: 'short', day: 'numeric' })
}

const totalPages = ref(0)
import { watch } from 'vue'
watch(total, (v) => { totalPages.value = Math.ceil(v / perPage) })
</script>

<template>
  <div class="articles-page" @click="handleClickOutside">
    <!-- Navbar -->
    <nav class="navbar">
      <div class="nav-inner">
        <div class="nav-brand" @click="router.push('/')">
          <div class="brand-icon">
            <svg viewBox="0 0 32 32" fill="none"><rect
              width="32"
              height="32"
              rx="8"
              fill="url(#g1)"
            /><path
              d="M10 16L14 20L22 12"
              stroke="#fff"
              stroke-width="2.5"
              stroke-linecap="round"
              stroke-linejoin="round"
            /><defs><linearGradient
              id="g1"
              x1="0"
              y1="0"
              x2="32"
              y2="32"
            ><stop stop-color="#6366f1" /><stop offset="1" stop-color="#06b6d4" /></linearGradient></defs></svg>
          </div>
          <span class="brand-text">TemplatePro</span>
        </div>
        <div class="nav-links" :class="{ open: mobileMenuOpen }">
          <router-link to="/" class="nav-link">
            {{ t('nav.home') }}
          </router-link>
          <router-link to="/articles" class="nav-link router-link-exact-active">
            {{ t('nav.articles') }}
          </router-link>
          <template v-if="userStore.isLoggedIn">
            <router-link to="/member" class="nav-link">
              {{ t('nav.member') }}
            </router-link>
          </template>
          <div class="lang-select">
            <button class="lang-trigger" @click.stop="langDropdownOpen = !langDropdownOpen">
              <svg
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="1.5"
                width="16"
                height="16"
              ><path d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9" /></svg>
              <span>{{ languages.find(l => l.code === locale)?.flag }} {{ languages.find(l => l.code === locale)?.label }}</span>
              <svg
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                width="14"
                height="14"
              ><path d="M19 9l-7 7-7-7" /></svg>
            </button>
            <div class="lang-dropdown" :class="{ show: langDropdownOpen }">
              <button
                v-for="lang in languages"
                :key="lang.code"
                class="lang-option"
                :class="{ active: locale === lang.code }"
                @click="setLang(lang.code)"
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
        </div>
        <div class="nav-actions">
          <template v-if="userStore.isLoggedIn">
            <button class="btn-outline" @click="logout">
              {{ t('nav.logout') }}
            </button>
          </template>
          <template v-else>
            <button class="btn-ghost" @click="goToLogin">
              {{ t('nav.login') }}
            </button>
            <button class="btn-primary-sm" @click="goToRegister">
              {{ t('nav.register') }}
            </button>
          </template>
          <button class="mobile-toggle" @click="mobileMenuOpen = !mobileMenuOpen">
            <svg
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
            ><path d="M4 6h16M4 12h16M4 18h16" /></svg>
          </button>
        </div>
      </div>
    </nav>

    <!-- Hero Banner -->
    <section class="page-hero">
      <div class="hero-bg">
        <div class="hero-orb orb-1" />
        <div class="hero-orb orb-2" />
      </div>
      <div class="hero-content">
        <h1>{{ t('article.pageTitle') }}</h1>
        <p>{{ t('article.pageSubtitle') }}</p>
      </div>
    </section>

    <!-- Categories -->
    <section v-if="categories.length" class="categories-bar">
      <div class="categories-inner">
        <button class="cat-btn" :class="{ active: !activeCategory }" @click="filterCategory('')">
          {{ t('article.allCategories') }}
        </button>
        <button
          v-for="cat in categories"
          :key="cat"
          class="cat-btn"
          :class="{ active: activeCategory === cat }"
          @click="filterCategory(cat)"
        >
          {{ cat }}
        </button>
      </div>
    </section>

    <!-- Article List -->
    <section class="articles-section">
      <div class="articles-inner">
        <div v-if="articles.length" class="article-grid">
          <div
            v-for="article in articles"
            :key="article.id"
            class="article-card"
            @click="goToDetail(article.id)"
          >
            <div class="article-cover">
              <img v-if="article.cover" :src="article.cover" :alt="article.title">
              <div v-else class="cover-placeholder">
                <svg
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="1"
                ><path d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" /></svg>
              </div>
              <span v-if="article.category" class="article-category">{{ article.category }}</span>
            </div>
            <div class="article-body">
              <h3>{{ article.title }}</h3>
              <p>{{ stripHtml(article.content)?.slice(0, 120) }}...</p>
              <div class="article-meta">
                <span class="meta-date">{{ formatDate(article.created_at) }}</span>
                <span class="meta-views">
                  <svg
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.5"
                    width="14"
                    height="14"
                  ><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                  {{ article.view_count || 0 }}
                </span>
              </div>
            </div>
          </div>
        </div>
        <div v-else class="empty-state">
          <svg
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="1"
            width="48"
            height="48"
          ><path d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" /></svg>
          <p>{{ t('article.empty') }}</p>
        </div>

        <!-- Pagination -->
        <div v-if="totalPages > 1" class="pagination">
          <button class="page-btn" :disabled="page <= 1" @click="handlePageChange(page - 1)">
            <svg
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
              width="18"
              height="18"
            ><path d="M15 19l-7-7 7-7" /></svg>
          </button>
          <button
            v-for="p in totalPages"
            v-show="Math.abs(p - page) < 3 || p === 1 || p === totalPages"
            :key="p"
            class="page-btn"
            :class="{ active: p === page }"
            @click="handlePageChange(p)"
          >
            {{ p }}
          </button>
          <button class="page-btn" :disabled="page >= totalPages" @click="handlePageChange(page + 1)">
            <svg
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
              width="18"
              height="18"
            ><path d="M9 5l7 7-7 7" /></svg>
          </button>
        </div>
      </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
      <div class="footer-inner">
        <div class="footer-top">
          <div class="footer-brand-col">
            <div class="footer-brand">
              <div class="brand-icon-sm">
                <svg viewBox="0 0 32 32" fill="none"><rect
                  width="32"
                  height="32"
                  rx="8"
                  fill="url(#g2)"
                /><path
                  d="M10 16L14 20L22 12"
                  stroke="#fff"
                  stroke-width="2.5"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                /><defs><linearGradient
                  id="g2"
                  x1="0"
                  y1="0"
                  x2="32"
                  y2="32"
                ><stop stop-color="#6366f1" /><stop offset="1" stop-color="#06b6d4" /></linearGradient></defs></svg>
              </div>
              <span>TemplatePro</span>
            </div>
            <p class="footer-desc">
              {{ t('home.heroSubtitle') }}
            </p>
          </div>
          <div class="footer-nav-col">
            <h4>{{ t('nav.home') }}</h4>
            <router-link to="/">
              {{ t('nav.home') }}
            </router-link>
            <router-link to="/articles">
              {{ t('nav.articles') }}
            </router-link>
            <router-link v-if="userStore.isLoggedIn" to="/member">
              {{ t('nav.member') }}
            </router-link>
          </div>
          <div class="footer-nav-col">
            <h4>{{ t('nav.member') }}</h4>
            <router-link to="/member/profile">
              {{ t('member.profile') }}
            </router-link>
            <router-link to="/member/referral">
              {{ t('member.referral') }}
            </router-link>
            <router-link to="/member/financial">
              {{ t('member.financial') }}
            </router-link>
          </div>
          <div class="footer-nav-col">
            <h4>{{ t('nav.login') }}</h4>
            <router-link v-if="!userStore.isLoggedIn" to="/login">
              {{ t('nav.login') }}
            </router-link>
            <router-link v-if="!userStore.isLoggedIn" to="/register">
              {{ t('nav.register') }}
            </router-link>
            <a v-if="userStore.isLoggedIn" href="#" @click.prevent="logout">{{ t('nav.logout') }}</a>
          </div>
        </div>
        <div class="footer-bottom">
          <p class="copyright">
            {{ t('home.copyright') }}
          </p>
          <div class="footer-socials">
            <a href="#" class="social-icon">
              <svg
                viewBox="0 0 24 24"
                fill="currentColor"
                width="18"
                height="18"
              ><path d="M24 4.557a9.83 9.83 0 01-2.828.775 4.932 4.932 0 002.165-2.724 9.864 9.864 0 01-3.127 1.195 4.916 4.916 0 00-3.594-1.555c-3.179 0-5.515 2.966-4.797 6.045A13.978 13.978 0 011.671 3.149a4.93 4.93 0 001.523 6.574 4.903 4.903 0 01-2.229-.616c-.054 2.281 1.581 4.415 3.949 4.89a4.935 4.935 0 01-2.224.084 4.928 4.928 0 004.6 3.419A9.9 9.9 0 010 19.54a13.94 13.94 0 007.548 2.212c9.142 0 14.307-7.721 13.995-14.646A10.025 10.025 0 0024 4.557z" /></svg>
            </a>
            <a href="#" class="social-icon">
              <svg
                viewBox="0 0 24 24"
                fill="currentColor"
                width="18"
                height="18"
              ><path d="M12 0C5.374 0 0 5.373 0 12c0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23A11.509 11.509 0 0112 5.803c1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576C20.566 21.797 24 17.3 24 12c0-6.627-5.373-12-12-12z" /></svg>
            </a>
          </div>
        </div>
      </div>
    </footer>
  </div>
</template>

<style scoped>
.articles-page {
  min-height: 100vh;
  background: #fafbfc;
}

/* Navbar - same as Home */
.navbar {
  position: fixed;
  top: 0; left: 0; right: 0;
  z-index: 100;
  background: rgba(255,255,255,0.85);
  backdrop-filter: blur(12px);
  border-bottom: 1px solid rgba(0,0,0,0.06);
}

.nav-inner {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 2rem;
  height: 64px;
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.nav-brand { display: flex; align-items: center; gap: 0.6rem; cursor: pointer; }
.brand-icon svg { width: 32px; height: 32px; }
.brand-text { font-size: 1.2rem; font-weight: 700; color: #1e293b; }

.nav-links { display: flex; align-items: center; gap: 0.5rem; }

.nav-link {
  padding: 0.5rem 1rem;
  text-decoration: none;
  color: #64748b;
  font-size: 0.9rem;
  font-weight: 500;
  border-radius: 8px;
  transition: all 0.2s;
}
.nav-link:hover, .nav-link.router-link-exact-active { color: #6366f1; background: rgba(99,102,241,0.06); }

.lang-select { position: relative; margin-left: 0.5rem; }
.lang-trigger {
  display: flex; align-items: center; gap: 0.4rem;
  padding: 0.4rem 0.8rem;
  border: 1.5px solid #e2e8f0; border-radius: 8px;
  background: white; color: #475569;
  font-size: 0.8rem; font-weight: 500;
  cursor: pointer; transition: all 0.2s;
}
.lang-trigger:hover { border-color: #6366f1; color: #6366f1; }

.lang-dropdown {
  position: absolute; top: calc(100% + 6px); right: 0;
  background: white; border: 1px solid #e2e8f0; border-radius: 10px;
  box-shadow: 0 10px 40px rgba(0,0,0,0.1);
  padding: 0.35rem; min-width: 150px;
  opacity: 0; visibility: hidden; transform: translateY(-8px);
  transition: all 0.2s; z-index: 200;
}
.lang-dropdown.show { opacity: 1; visibility: visible; transform: translateY(0); }

.lang-option {
  display: flex; align-items: center; gap: 0.5rem;
  width: 100%; padding: 0.55rem 0.75rem;
  border: none; background: none; color: #475569;
  font-size: 0.85rem; border-radius: 7px; cursor: pointer;
  transition: all 0.15s; text-align: left;
}
.lang-option:hover { background: #f1f5f9; color: #1e293b; }
.lang-option.active { background: rgba(99,102,241,0.08); color: #6366f1; font-weight: 600; }
.lang-option svg { margin-left: auto; color: #6366f1; }

.nav-actions { display: flex; align-items: center; gap: 0.5rem; }
.btn-ghost { padding: 0.5rem 1.2rem; border: none; background: none; color: #64748b; font-weight: 500; font-size: 0.9rem; cursor: pointer; border-radius: 8px; transition: all 0.2s; }
.btn-ghost:hover { color: #6366f1; background: rgba(99,102,241,0.06); }
.btn-outline { padding: 0.5rem 1.2rem; border: 1.5px solid #e2e8f0; background: white; color: #64748b; font-weight: 500; font-size: 0.9rem; cursor: pointer; border-radius: 8px; transition: all 0.2s; }
.btn-outline:hover { border-color: #6366f1; color: #6366f1; }
.btn-primary-sm { padding: 0.5rem 1.2rem; border: none; background: linear-gradient(135deg, #6366f1, #06b6d4); color: white; font-weight: 600; font-size: 0.9rem; cursor: pointer; border-radius: 8px; transition: all 0.2s; }
.btn-primary-sm:hover { transform: translateY(-1px); box-shadow: 0 4px 12px rgba(99,102,241,0.4); }
.mobile-toggle { display: none; background: none; border: none; color: #64748b; cursor: pointer; padding: 0.5rem; }
.mobile-toggle svg { width: 24px; height: 24px; }

/* Page Hero */
.page-hero {
  position: relative;
  padding: 8rem 2rem 4rem;
  text-align: center;
  overflow: hidden;
}

.hero-bg {
  position: absolute; inset: 0;
  background: linear-gradient(135deg, #eef2ff 0%, #ecfeff 50%, #f0fdfa 100%);
}

.hero-orb {
  position: absolute; border-radius: 50%; filter: blur(80px); opacity: 0.35;
}
.orb-1 { width: 500px; height: 500px; background: #6366f1; top: -100px; left: -100px; }
.orb-2 { width: 400px; height: 400px; background: #06b6d4; bottom: -100px; right: -100px; }

.hero-content { position: relative; z-index: 1; max-width: 600px; margin: 0 auto; }
.hero-content h1 {
  font-size: 2.5rem; font-weight: 800; color: #0f172a;
  margin-bottom: 0.75rem; letter-spacing: -0.02em;
}
.hero-content p { font-size: 1.1rem; color: #64748b; line-height: 1.6; }

/* Categories */
.categories-bar {
  background: white;
  border-bottom: 1px solid #f1f5f9;
  position: sticky;
  top: 64px;
  z-index: 50;
}

.categories-inner {
  max-width: 1200px;
  margin: 0 auto;
  padding: 1rem 2rem;
  display: flex;
  gap: 0.5rem;
  overflow-x: auto;
}

.cat-btn {
  padding: 0.5rem 1.2rem;
  border: 1.5px solid #e2e8f0;
  border-radius: 20px;
  background: white;
  color: #64748b;
  font-size: 0.85rem;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s;
  white-space: nowrap;
}

.cat-btn:hover { border-color: #6366f1; color: #6366f1; }
.cat-btn.active {
  background: linear-gradient(135deg, #6366f1, #06b6d4);
  color: white;
  border-color: transparent;
}

/* Articles */
.articles-section {
  padding: 3rem 0;
}

.articles-inner {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 2rem;
}

.article-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 1.5rem;
}

.article-card {
  background: white;
  border-radius: 16px;
  overflow: hidden;
  border: 1px solid #f1f5f9;
  cursor: pointer;
  transition: all 0.3s;
}

.article-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 12px 40px rgba(0,0,0,0.08);
  border-color: rgba(99,102,241,0.15);
}

.article-cover {
  height: 180px;
  background: linear-gradient(135deg, #eef2ff, #ecfeff);
  position: relative;
  overflow: hidden;
}

.article-cover img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.cover-placeholder {
  width: 100%; height: 100%;
  display: flex; align-items: center; justify-content: center;
  color: #c7d2fe;
}
.cover-placeholder svg { width: 48px; height: 48px; }

.article-category {
  position: absolute;
  top: 12px; left: 12px;
  padding: 0.3rem 0.8rem;
  background: rgba(99,102,241,0.9);
  color: white;
  font-size: 0.75rem;
  font-weight: 600;
  border-radius: 6px;
  backdrop-filter: blur(4px);
}

.article-body { padding: 1.25rem; }
.article-body h3 {
  font-size: 1.05rem; font-weight: 700; color: #1e293b;
  margin-bottom: 0.5rem; line-height: 1.4;
  display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;
}
.article-body p {
  font-size: 0.85rem; color: #94a3b8; line-height: 1.6;
  margin-bottom: 0.75rem;
  display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;
}

.article-meta {
  display: flex; align-items: center; gap: 1rem;
  font-size: 0.8rem; color: #94a3b8;
}
.meta-views { display: flex; align-items: center; gap: 0.3rem; }

/* Empty */
.empty-state {
  text-align: center;
  padding: 6rem 2rem;
  color: #94a3b8;
}
.empty-state svg { margin-bottom: 1rem; opacity: 0.5; }
.empty-state p { font-size: 1rem; }

/* Pagination */
.pagination {
  display: flex; justify-content: center; gap: 0.4rem; margin-top: 3rem;
}

.page-btn {
  width: 40px; height: 40px;
  border: 1.5px solid #e2e8f0; border-radius: 10px;
  background: white; color: #64748b;
  font-size: 0.9rem; font-weight: 500;
  cursor: pointer; transition: all 0.2s;
  display: flex; align-items: center; justify-content: center;
}
.page-btn:hover:not(:disabled) { border-color: #6366f1; color: #6366f1; }
.page-btn.active { background: linear-gradient(135deg, #6366f1, #06b6d4); color: white; border-color: transparent; }
.page-btn:disabled { opacity: 0.4; cursor: not-allowed; }

/* Footer */
.footer { background: #0f172a; }
.footer-inner { max-width: 1200px; margin: 0 auto; }
.footer-top { display: grid; grid-template-columns: 2fr 1fr 1fr 1fr; gap: 3rem; padding: 4rem 2rem 3rem; }
.footer-brand-col { display: flex; flex-direction: column; gap: 1rem; }
.footer-brand { display: flex; align-items: center; gap: 0.5rem; color: white; font-weight: 700; font-size: 1.1rem; }
.brand-icon-sm svg { width: 28px; height: 28px; }
.footer-desc { color: #94a3b8; font-size: 0.85rem; line-height: 1.6; max-width: 280px; }
.footer-nav-col { display: flex; flex-direction: column; gap: 0.6rem; }
.footer-nav-col h4 { color: white; font-size: 0.9rem; font-weight: 600; margin-bottom: 0.3rem; }
.footer-nav-col a { color: #94a3b8; text-decoration: none; font-size: 0.85rem; transition: color 0.2s; }
.footer-nav-col a:hover { color: #06b6d4; }
.footer-bottom { display: flex; justify-content: space-between; align-items: center; padding: 1.5rem 2rem; border-top: 1px solid rgba(255,255,255,0.08); }
.copyright { color: #64748b; font-size: 0.85rem; }
.footer-socials { display: flex; gap: 0.75rem; }
.social-icon { width: 36px; height: 36px; border-radius: 8px; background: rgba(255,255,255,0.06); display: flex; align-items: center; justify-content: center; color: #94a3b8; transition: all 0.2s; text-decoration: none; }
.social-icon:hover { background: rgba(6,182,212,0.15); color: #06b6d4; }

@media (max-width: 768px) {
  .nav-links { display: none; }
  .nav-links.open { display: flex; flex-direction: column; position: absolute; top: 64px; left: 0; right: 0; background: white; padding: 1rem; border-bottom: 1px solid #e2e8f0; z-index: 200; }
  .mobile-toggle { display: block; }
  .page-hero h1 { font-size: 1.75rem; }
  .article-grid { grid-template-columns: 1fr; }
  .footer-top { grid-template-columns: 1fr; gap: 2rem; }
  .footer-bottom { flex-direction: column; gap: 1rem; text-align: center; }
}
</style>
