<script setup>
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useUserStore } from '@/stores/user'
import { useI18n } from 'vue-i18n'
import api from '@/api'

const { t, locale } = useI18n()
const route = useRoute()
const router = useRouter()
const userStore = useUserStore()
const article = ref(null)
const mobileMenuOpen = ref(false)
const langDropdownOpen = ref(false)

const languages = [
  { code: 'zh', label: '中文', flag: '🇨🇳' },
  { code: 'en', label: 'English', flag: '🇺🇸' },
]

onMounted(async () => {
  const res = await api.articles.frontGet(route.params.id)
  if (res.code === 200) {
    article.value = res.data
  }
})

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

function formatDate(dateStr) {
  if (!dateStr) return ''
  const d = new Date(dateStr)
  return d.toLocaleDateString(locale.value === 'zh' ? 'zh-CN' : 'en-US', { year: 'numeric', month: 'long', day: 'numeric' })
}
</script>

<template>
  <div v-if="article" class="article-detail-page" @click="handleClickOutside">
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
          <router-link to="/articles" class="nav-link">
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

    <!-- Article Hero -->
    <section class="article-hero">
      <div class="hero-bg">
        <div class="hero-orb orb-1" />
        <div class="hero-orb orb-2" />
      </div>
      <div class="hero-content">
        <button class="back-btn" @click="router.push('/articles')">
          <svg
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="2"
            width="18"
            height="18"
          ><path d="M19 12H5m7-7l-7 7 7 7" /></svg>
          {{ t('article.backToList') }}
        </button>
        <span v-if="article.category" class="article-category-tag">{{ article.category }}</span>
        <h1>{{ article.title }}</h1>
        <div class="article-meta">
          <span class="meta-item">
            <svg
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="1.5"
              width="16"
              height="16"
            ><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
            {{ formatDate(article.created_at) }}
          </span>
          <span class="meta-item">
            <svg
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="1.5"
              width="16"
              height="16"
            ><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
            {{ article.view_count || 0 }} {{ t('article.views') }}
          </span>
        </div>
      </div>
    </section>

    <!-- Cover Image -->
    <section v-if="article.cover" class="cover-section">
      <div class="cover-inner">
        <img :src="article.cover" :alt="article.title">
      </div>
    </section>

    <!-- Content -->
    <section class="content-section">
      <div class="content-inner">
        <article class="article-content" v-html="article.content" />
      </div>
    </section>

    <!-- Nav between articles -->
    <section class="nav-section">
      <div class="nav-inner-bottom">
        <button class="nav-btn" @click="router.push('/articles')">
          <svg
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="2"
            width="18"
            height="18"
          ><path d="M19 12H5m7-7l-7 7 7 7" /></svg>
          {{ t('article.backToList') }}
        </button>
        <button class="nav-btn" @click="router.push('/')">
          {{ t('nav.home') }}
          <svg
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="2"
            width="18"
            height="18"
          ><path d="M5 12h14m-7-7l7 7-7 7" /></svg>
        </button>
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
.article-detail-page {
  min-height: 100vh;
  background: #fafbfc;
}

/* Navbar */
.navbar {
  position: fixed; top: 0; left: 0; right: 0; z-index: 100;
  background: rgba(255,255,255,0.85);
  backdrop-filter: blur(12px);
  border-bottom: 1px solid rgba(0,0,0,0.06);
}

.nav-inner {
  max-width: 1200px; margin: 0 auto; padding: 0 2rem; height: 64px;
  display: flex; align-items: center; justify-content: space-between;
}

.nav-brand { display: flex; align-items: center; gap: 0.6rem; cursor: pointer; }
.brand-icon svg { width: 32px; height: 32px; }
.brand-text { font-size: 1.2rem; font-weight: 700; color: #1e293b; }

.nav-links { display: flex; align-items: center; gap: 0.5rem; }
.nav-link {
  padding: 0.5rem 1rem; text-decoration: none; color: #64748b;
  font-size: 0.9rem; font-weight: 500; border-radius: 8px; transition: all 0.2s;
}
.nav-link:hover, .nav-link.router-link-exact-active { color: #6366f1; background: rgba(99,102,241,0.06); }

.lang-select { position: relative; margin-left: 0.5rem; }
.lang-trigger {
  display: flex; align-items: center; gap: 0.4rem;
  padding: 0.4rem 0.8rem; border: 1.5px solid #e2e8f0; border-radius: 8px;
  background: white; color: #475569; font-size: 0.8rem; font-weight: 500;
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

/* Article Hero */
.article-hero {
  position: relative;
  padding: 8rem 2rem 3rem;
  overflow: hidden;
}

.hero-bg {
  position: absolute; inset: 0;
  background: linear-gradient(135deg, #eef2ff 0%, #ecfeff 50%, #f0fdfa 100%);
}

.hero-orb { position: absolute; border-radius: 50%; filter: blur(80px); opacity: 0.35; }
.orb-1 { width: 500px; height: 500px; background: #6366f1; top: -100px; left: -100px; }
.orb-2 { width: 400px; height: 400px; background: #06b6d4; bottom: -100px; right: -100px; }

.hero-content { position: relative; z-index: 1; max-width: 800px; margin: 0 auto; }

.back-btn {
  display: inline-flex; align-items: center; gap: 0.4rem;
  padding: 0.5rem 1rem; border: 1.5px solid #e2e8f0; border-radius: 8px;
  background: white; color: #64748b; font-size: 0.85rem; font-weight: 500;
  cursor: pointer; transition: all 0.2s; margin-bottom: 1.5rem;
}
.back-btn:hover { border-color: #6366f1; color: #6366f1; }

.article-category-tag {
  display: inline-block;
  padding: 0.3rem 0.8rem;
  background: linear-gradient(135deg, #6366f1, #06b6d4);
  color: white;
  font-size: 0.8rem;
  font-weight: 600;
  border-radius: 6px;
  margin-bottom: 1rem;
}

.hero-content h1 {
  font-size: 2.25rem; font-weight: 800; color: #0f172a;
  line-height: 1.3; margin-bottom: 1rem; letter-spacing: -0.02em;
}

.article-meta {
  display: flex; align-items: center; gap: 1.5rem;
  color: #64748b; font-size: 0.9rem;
}

.meta-item {
  display: flex; align-items: center; gap: 0.4rem;
}

/* Cover */
.cover-section {
  max-width: 900px; margin: 0 auto; padding: 0 2rem;
  margin-top: -1rem; position: relative; z-index: 2;
}

.cover-inner {
  border-radius: 16px; overflow: hidden;
  box-shadow: 0 20px 60px rgba(0,0,0,0.1);
}

.cover-inner img {
  width: 100%; display: block;
}

/* Content */
.content-section {
  padding: 3rem 0 4rem;
}

.content-inner {
  max-width: 800px;
  margin: 0 auto;
  padding: 0 2rem;
}

.article-content {
  background: white;
  border-radius: 16px;
  padding: 3rem;
  border: 1px solid #f1f5f9;
  line-height: 1.8;
  color: #334155;
  font-size: 1rem;
}

.article-content :deep(h1) { font-size: 1.75rem; font-weight: 700; color: #0f172a; margin: 2rem 0 1rem; }
.article-content :deep(h2) { font-size: 1.5rem; font-weight: 700; color: #0f172a; margin: 2rem 0 1rem; }
.article-content :deep(h3) { font-size: 1.25rem; font-weight: 600; color: #1e293b; margin: 1.5rem 0 0.75rem; }
.article-content :deep(p) { margin-bottom: 1rem; }
.article-content :deep(img) { max-width: 100%; border-radius: 10px; margin: 1.5rem 0; }
.article-content :deep(a) { color: #6366f1; text-decoration: underline; }
.article-content :deep(blockquote) {
  border-left: 4px solid #6366f1; padding: 1rem 1.5rem; margin: 1.5rem 0;
  background: #f8fafc; border-radius: 0 8px 8px 0; color: #475569;
}
.article-content :deep(code) {
  background: #f1f5f9; padding: 0.2rem 0.5rem; border-radius: 4px;
  font-size: 0.9em; color: #6366f1;
}
.article-content :deep(pre) {
  background: #1e293b; color: #e2e8f0; padding: 1.5rem;
  border-radius: 10px; overflow-x: auto; margin: 1.5rem 0;
}
.article-content :deep(pre code) { background: none; color: inherit; padding: 0; }
.article-content :deep(ul), .article-content :deep(ol) { padding-left: 1.5rem; margin-bottom: 1rem; }
.article-content :deep(li) { margin-bottom: 0.5rem; }
.article-content :deep(table) { width: 100%; border-collapse: collapse; margin: 1.5rem 0; }
.article-content :deep(th), .article-content :deep(td) { border: 1px solid #e2e8f0; padding: 0.75rem 1rem; text-align: left; }
.article-content :deep(th) { background: #f8fafc; font-weight: 600; }

/* Nav section */
.nav-section {
  padding: 0 2rem 4rem;
}

.nav-inner-bottom {
  max-width: 800px; margin: 0 auto;
  display: flex; justify-content: space-between; gap: 1rem;
}

.nav-btn {
  display: flex; align-items: center; gap: 0.5rem;
  padding: 0.75rem 1.5rem; border: 1.5px solid #e2e8f0; border-radius: 10px;
  background: white; color: #64748b; font-size: 0.9rem; font-weight: 500;
  cursor: pointer; transition: all 0.2s;
}
.nav-btn:hover { border-color: #6366f1; color: #6366f1; transform: translateY(-1px); box-shadow: 0 4px 12px rgba(0,0,0,0.06); }

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
  .hero-content h1 { font-size: 1.5rem; }
  .article-content { padding: 1.5rem; }
  .nav-inner-bottom { flex-direction: column; }
  .footer-top { grid-template-columns: 1fr; gap: 2rem; }
  .footer-bottom { flex-direction: column; gap: 1rem; text-align: center; }
}
</style>
