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
const mobileMenuOpen = ref(false)
const langDropdownOpen = ref(false)

const languages = [
  { code: 'zh', label: '中文', flag: '🇨🇳' },
  { code: 'en', label: 'English', flag: '🇺🇸' },
]

onMounted(async () => {
  const res = await api.articles.frontList({ per_page: 3 })
  if (res.code === 200) {
    articles.value = res.data.data
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
</script>

<template>
  <div class="home-page" @click="handleClickOutside">
    <!-- Navbar -->
    <nav class="navbar">
      <div class="nav-inner">
        <div class="nav-brand" @click="router.push('/')">
          <div class="brand-icon">
            <svg viewBox="0 0 32 32" fill="none"><rect width="32" height="32" rx="8" fill="url(#g1)"/><path d="M10 16L14 20L22 12" stroke="#fff" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/><defs><linearGradient id="g1" x1="0" y1="0" x2="32" y2="32"><stop stop-color="#6366f1"/><stop offset="1" stop-color="#06b6d4"/></linearGradient></defs></svg>
          </div>
          <span class="brand-text">TemplatePro</span>
        </div>
        <div class="nav-links" :class="{ open: mobileMenuOpen }">
          <router-link to="/" class="nav-link">{{ t('nav.home') }}</router-link>
          <router-link to="/articles" class="nav-link">{{ t('nav.articles') }}</router-link>
          <router-link to="/products" class="nav-link">{{ t('nav.products') }}</router-link>
          <template v-if="userStore.isLoggedIn">
            <router-link to="/member" class="nav-link">{{ t('nav.member') }}</router-link>
          </template>
          <div class="lang-select">
            <button class="lang-trigger" @click.stop="langDropdownOpen = !langDropdownOpen">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="16" height="16"><path d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/></svg>
              <span>{{ languages.find(l => l.code === locale)?.flag }} {{ languages.find(l => l.code === locale)?.label }}</span>
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><path d="M19 9l-7 7-7-7"/></svg>
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
                <svg v-if="locale === lang.code" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" width="14" height="14"><path d="M5 13l4 4L19 7"/></svg>
              </button>
            </div>
          </div>
        </div>
        <div class="nav-actions">
          <template v-if="userStore.isLoggedIn">
            <button class="btn-outline" @click="logout">{{ t('nav.logout') }}</button>
          </template>
          <template v-else>
            <button class="btn-ghost" @click="goToLogin">{{ t('nav.login') }}</button>
            <button class="btn-primary-sm" @click="goToRegister">{{ t('nav.register') }}</button>
          </template>
          <button class="mobile-toggle" @click="mobileMenuOpen = !mobileMenuOpen">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 6h16M4 12h16M4 18h16"/></svg>
          </button>
        </div>
      </div>
    </nav>

    <!-- Hero -->
    <section class="hero">
      <div class="hero-bg">
        <div class="hero-orb orb-1"></div>
        <div class="hero-orb orb-2"></div>
        <div class="hero-orb orb-3"></div>
      </div>
      <div class="hero-content">
        <h1>{{ t('home.heroTitle') }}</h1>
        <p>{{ t('home.heroSubtitle') }}</p>
        <div class="hero-buttons">
          <button class="btn-primary-lg" @click="goToRegister">{{ t('home.heroCta') }}</button>
          <button class="btn-secondary-lg" @click="router.push('/articles')">{{ t('home.heroSecondary') }}</button>
        </div>
        <div class="hero-stats">
          <div class="stat">
            <span class="stat-num">10K+</span>
            <span class="stat-label">Active Users</span>
          </div>
          <div class="stat-divider"></div>
          <div class="stat">
            <span class="stat-num">99.9%</span>
            <span class="stat-label">Uptime</span>
          </div>
          <div class="stat-divider"></div>
          <div class="stat">
            <span class="stat-num">50+</span>
            <span class="stat-label">APIs</span>
          </div>
        </div>
      </div>
    </section>

    <!-- Features -->
    <section class="features">
      <div class="section-inner">
        <div class="section-header">
          <h2>{{ t('home.featureTitle') }}</h2>
          <p>{{ t('home.featureSubtitle') }}</p>
        </div>
        <div class="feature-grid">
          <div class="feature-card" v-for="i in 6" :key="i">
            <div class="feature-icon" :class="`icon-${i}`">
              <svg v-if="i===1" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
              <svg v-if="i===2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
              <svg v-if="i===3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"/></svg>
              <svg v-if="i===4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"/></svg>
              <svg v-if="i===5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
              <svg v-if="i===6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01"/></svg>
            </div>
            <h3>{{ t(`home.feat${i}Title`) }}</h3>
            <p>{{ t(`home.feat${i}Desc`) }}</p>
          </div>
        </div>
      </div>
    </section>

    <!-- Articles -->
    <section class="articles-section" v-if="articles.length">
      <div class="section-inner">
        <div class="section-header">
          <h2>{{ t('home.articleTitle') }}</h2>
          <p>{{ t('home.articleSubtitle') }}</p>
        </div>
        <div class="article-grid">
          <div
            v-for="article in articles"
            :key="article.id"
            class="article-card"
            @click="router.push(`/article/${article.id}`)"
          >
            <div class="article-thumb">
              <div class="article-thumb-placeholder">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1"><path d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
              </div>
            </div>
            <div class="article-body">
              <h4>{{ article.title }}</h4>
              <p>{{ article.content?.slice(0, 80) }}...</p>
              <span class="read-more">{{ t('home.readMore') }} →</span>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- CTA -->
    <section class="cta-section">
      <div class="cta-inner">
        <h2>{{ t('home.ctaTitle') }}</h2>
        <p>{{ t('home.ctaDesc') }}</p>
        <button class="btn-primary-lg" @click="goToRegister">{{ t('home.heroCta') }}</button>
      </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
      <div class="footer-inner">
        <div class="footer-top">
          <div class="footer-brand-col">
            <div class="footer-brand">
              <div class="brand-icon-sm">
                <svg viewBox="0 0 32 32" fill="none"><rect width="32" height="32" rx="8" fill="url(#g2)"/><path d="M10 16L14 20L22 12" stroke="#fff" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/><defs><linearGradient id="g2" x1="0" y1="0" x2="32" y2="32"><stop stop-color="#6366f1"/><stop offset="1" stop-color="#06b6d4"/></linearGradient></defs></svg>
              </div>
              <span>TemplatePro</span>
            </div>
            <p class="footer-desc">{{ t('home.heroSubtitle') }}</p>
          </div>
          <div class="footer-nav-col">
            <h4>{{ t('nav.home') }}</h4>
            <router-link to="/">{{ t('nav.home') }}</router-link>
            <router-link to="/articles">{{ t('nav.articles') }}</router-link>
            <router-link to="/products">{{ t('nav.products') }}</router-link>
            <router-link to="/member" v-if="userStore.isLoggedIn">{{ t('nav.member') }}</router-link>
          </div>
          <div class="footer-nav-col">
            <h4>{{ t('nav.member') }}</h4>
            <router-link to="/member/profile">{{ t('member.profile') }}</router-link>
            <router-link to="/member/referral">{{ t('member.referral') }}</router-link>
            <router-link to="/member/financial">{{ t('member.financial') }}</router-link>
          </div>
          <div class="footer-nav-col">
            <h4>{{ t('nav.login') }}</h4>
            <router-link to="/login" v-if="!userStore.isLoggedIn">{{ t('nav.login') }}</router-link>
            <router-link to="/register" v-if="!userStore.isLoggedIn">{{ t('nav.register') }}</router-link>
            <a href="#" @click.prevent="logout" v-if="userStore.isLoggedIn">{{ t('nav.logout') }}</a>
          </div>
        </div>
        <div class="footer-bottom">
          <p class="copyright">{{ t('home.copyright') }}</p>
          <div class="footer-socials">
            <a href="#" class="social-icon">
              <svg viewBox="0 0 24 24" fill="currentColor" width="18" height="18"><path d="M24 4.557a9.83 9.83 0 01-2.828.775 4.932 4.932 0 002.165-2.724 9.864 9.864 0 01-3.127 1.195 4.916 4.916 0 00-3.594-1.555c-3.179 0-5.515 2.966-4.797 6.045A13.978 13.978 0 011.671 3.149a4.93 4.93 0 001.523 6.574 4.903 4.903 0 01-2.229-.616c-.054 2.281 1.581 4.415 3.949 4.89a4.935 4.935 0 01-2.224.084 4.928 4.928 0 004.6 3.419A9.9 9.9 0 010 19.54a13.94 13.94 0 007.548 2.212c9.142 0 14.307-7.721 13.995-14.646A10.025 10.025 0 0024 4.557z"/></svg>
            </a>
            <a href="#" class="social-icon">
              <svg viewBox="0 0 24 24" fill="currentColor" width="18" height="18"><path d="M12 0C5.374 0 0 5.373 0 12c0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23A11.509 11.509 0 0112 5.803c1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576C20.566 21.797 24 17.3 24 12c0-6.627-5.373-12-12-12z"/></svg>
            </a>
          </div>
        </div>
      </div>
    </footer>
  </div>
</template>

<style scoped>
.home-page {
  min-height: 100vh;
  background: #fafbfc;
}

/* Navbar */
.navbar {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
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

.nav-brand {
  display: flex;
  align-items: center;
  gap: 0.6rem;
  cursor: pointer;
}

.brand-icon svg { width: 32px; height: 32px; }
.brand-text { font-size: 1.2rem; font-weight: 700; color: #1e293b; }

.nav-links {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.nav-link {
  padding: 0.5rem 1rem;
  text-decoration: none;
  color: #64748b;
  font-size: 0.9rem;
  font-weight: 500;
  border-radius: 8px;
  transition: all 0.2s;
}

.nav-link:hover, .nav-link.router-link-exact-active {
  color: #6366f1;
  background: rgba(99,102,241,0.06);
}

/* Language dropdown */
.lang-select {
  position: relative;
  margin-left: 0.5rem;
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

.nav-actions {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.btn-ghost {
  padding: 0.5rem 1.2rem;
  border: none;
  background: none;
  color: #64748b;
  font-weight: 500;
  font-size: 0.9rem;
  cursor: pointer;
  border-radius: 8px;
  transition: all 0.2s;
}

.btn-ghost:hover { color: #6366f1; background: rgba(99,102,241,0.06); }

.btn-outline {
  padding: 0.5rem 1.2rem;
  border: 1.5px solid #e2e8f0;
  background: white;
  color: #64748b;
  font-weight: 500;
  font-size: 0.9rem;
  cursor: pointer;
  border-radius: 8px;
  transition: all 0.2s;
}

.btn-outline:hover { border-color: #6366f1; color: #6366f1; }

.btn-primary-sm {
  padding: 0.5rem 1.2rem;
  border: none;
  background: linear-gradient(135deg, #6366f1, #06b6d4);
  color: white;
  font-weight: 600;
  font-size: 0.9rem;
  cursor: pointer;
  border-radius: 8px;
  transition: all 0.2s;
}

.btn-primary-sm:hover { transform: translateY(-1px); box-shadow: 0 4px 12px rgba(99,102,241,0.4); }

.mobile-toggle {
  display: none;
  background: none;
  border: none;
  color: #64748b;
  cursor: pointer;
  padding: 0.5rem;
}

.mobile-toggle svg { width: 24px; height: 24px; }

/* Hero */
.hero {
  position: relative;
  padding: 10rem 2rem 6rem;
  text-align: center;
  overflow: hidden;
}

.hero-bg {
  position: absolute;
  inset: 0;
  background: linear-gradient(135deg, #eef2ff 0%, #ecfeff 50%, #f0fdfa 100%);
}

.hero-orb {
  position: absolute;
  border-radius: 50%;
  filter: blur(80px);
  opacity: 0.35;
}

.orb-1 { width: 500px; height: 500px; background: #6366f1; top: -100px; left: -100px; }
.orb-2 { width: 400px; height: 400px; background: #06b6d4; bottom: -100px; right: -100px; }
.orb-3 { width: 300px; height: 300px; background: #8b5cf6; top: 50%; left: 50%; transform: translate(-50%,-50%); }

.hero-content {
  position: relative;
  z-index: 1;
  max-width: 800px;
  margin: 0 auto;
}

.hero h1 {
  font-size: 3.5rem;
  font-weight: 800;
  color: #0f172a;
  line-height: 1.15;
  margin-bottom: 1.5rem;
  letter-spacing: -0.02em;
}

.hero p {
  font-size: 1.25rem;
  color: #64748b;
  line-height: 1.6;
  margin-bottom: 2.5rem;
  max-width: 600px;
  margin-left: auto;
  margin-right: auto;
}

.hero-buttons {
  display: flex;
  gap: 1rem;
  justify-content: center;
  margin-bottom: 4rem;
}

.btn-primary-lg {
  padding: 0.9rem 2.5rem;
  background: linear-gradient(135deg, #6366f1, #06b6d4);
  color: white;
  border: none;
  border-radius: 12px;
  font-size: 1rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s;
}

.btn-primary-lg:hover { transform: translateY(-2px); box-shadow: 0 12px 35px rgba(99,102,241,0.35); }

.btn-secondary-lg {
  padding: 0.9rem 2.5rem;
  background: white;
  color: #475569;
  border: 1.5px solid #e2e8f0;
  border-radius: 12px;
  font-size: 1rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s;
}

.btn-secondary-lg:hover { border-color: #6366f1; color: #6366f1; transform: translateY(-2px); box-shadow: 0 8px 25px rgba(0,0,0,0.08); }

.hero-stats {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 2.5rem;
}

.stat-num {
  display: block;
  font-size: 1.75rem;
  font-weight: 800;
  background: linear-gradient(135deg, #6366f1, #06b6d4);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

.stat-label {
  display: block;
  font-size: 0.8rem;
  color: #94a3b8;
  font-weight: 500;
  margin-top: 0.2rem;
}

.stat-divider {
  width: 1px;
  height: 40px;
  background: #e2e8f0;
}

/* Sections */
.section-inner {
  max-width: 1200px;
  margin: 0 auto;
  padding: 6rem 2rem;
}

.section-header {
  text-align: center;
  margin-bottom: 3.5rem;
}

.section-header h2 {
  font-size: 2.25rem;
  font-weight: 800;
  color: #0f172a;
  margin-bottom: 0.75rem;
}

.section-header p {
  font-size: 1.1rem;
  color: #94a3b8;
  max-width: 600px;
  margin: 0 auto;
}

/* Features */
.features {
  background: white;
}

.feature-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 2rem;
}

.feature-card {
  padding: 2rem;
  border-radius: 16px;
  background: #fafbfc;
  border: 1px solid #f1f5f9;
  transition: all 0.3s;
}

.feature-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 12px 40px rgba(0,0,0,0.08);
  border-color: rgba(99,102,241,0.2);
}

.feature-icon {
  width: 48px;
  height: 48px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 1.25rem;
}

.feature-icon svg { width: 24px; height: 24px; }

.icon-1 { background: rgba(99,102,241,0.1); color: #6366f1; }
.icon-2 { background: rgba(16,185,129,0.1); color: #10b981; }
.icon-3 { background: rgba(245,158,11,0.1); color: #f59e0b; }
.icon-4 { background: rgba(139,92,246,0.1); color: #8b5cf6; }
.icon-5 { background: rgba(236,72,153,0.1); color: #ec4899; }
.icon-6 { background: rgba(6,182,212,0.1); color: #06b6d4; }

.feature-card h3 {
  font-size: 1.1rem;
  font-weight: 700;
  color: #1e293b;
  margin-bottom: 0.6rem;
}

.feature-card p {
  font-size: 0.9rem;
  color: #94a3b8;
  line-height: 1.6;
}

/* Articles */
.articles-section {
  background: #fafbfc;
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
}

.article-thumb {
  height: 160px;
  background: linear-gradient(135deg, #eef2ff, #ecfeff);
  display: flex;
  align-items: center;
  justify-content: center;
}

.article-thumb-placeholder svg {
  width: 48px;
  height: 48px;
  color: #c7d2fe;
}

.article-body {
  padding: 1.25rem;
}

.article-body h4 {
  font-size: 1rem;
  font-weight: 700;
  color: #1e293b;
  margin-bottom: 0.5rem;
  display: -webkit-box;
  -webkit-line-clamp: 1;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.article-body p {
  font-size: 0.85rem;
  color: #94a3b8;
  line-height: 1.5;
  margin-bottom: 0.75rem;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.read-more {
  font-size: 0.85rem;
  font-weight: 600;
  color: #6366f1;
}

/* CTA */
.cta-section {
  background: linear-gradient(135deg, #6366f1 0%, #06b6d4 100%);
  padding: 6rem 2rem;
}

.cta-inner {
  max-width: 600px;
  margin: 0 auto;
  text-align: center;
  color: white;
}

.cta-inner h2 {
  font-size: 2.25rem;
  font-weight: 800;
  margin-bottom: 0.75rem;
}

.cta-inner p {
  font-size: 1.1rem;
  opacity: 0.85;
  margin-bottom: 2rem;
}

.cta-inner .btn-primary-lg {
  background: white;
  color: #6366f1;
}

.cta-inner .btn-primary-lg:hover {
  box-shadow: 0 12px 35px rgba(0,0,0,0.2);
}

/* Footer */
.footer {
  background: #0f172a;
  padding: 0;
}

.footer-inner {
  max-width: 1200px;
  margin: 0 auto;
}

.footer-top {
  display: grid;
  grid-template-columns: 2fr 1fr 1fr 1fr;
  gap: 3rem;
  padding: 4rem 2rem 3rem;
}

.footer-brand-col {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.footer-brand {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  color: white;
  font-weight: 700;
  font-size: 1.1rem;
}

.brand-icon-sm svg { width: 28px; height: 28px; }

.footer-desc {
  color: #94a3b8;
  font-size: 0.85rem;
  line-height: 1.6;
  max-width: 280px;
}

.footer-nav-col {
  display: flex;
  flex-direction: column;
  gap: 0.6rem;
}

.footer-nav-col h4 {
  color: white;
  font-size: 0.9rem;
  font-weight: 600;
  margin-bottom: 0.3rem;
}

.footer-nav-col a {
  color: #94a3b8;
  text-decoration: none;
  font-size: 0.85rem;
  transition: color 0.2s;
}

.footer-nav-col a:hover { color: #06b6d4; }

.footer-bottom {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1.5rem 2rem;
  border-top: 1px solid rgba(255,255,255,0.08);
}

.copyright {
  color: #64748b;
  font-size: 0.85rem;
}

.footer-socials {
  display: flex;
  gap: 0.75rem;
}

.social-icon {
  width: 36px;
  height: 36px;
  border-radius: 8px;
  background: rgba(255,255,255,0.06);
  display: flex;
  align-items: center;
  justify-content: center;
  color: #94a3b8;
  transition: all 0.2s;
  text-decoration: none;
}

.social-icon:hover { background: rgba(6,182,212,0.15); color: #06b6d4; }

@media (max-width: 768px) {
  .nav-links { display: none; }
  .nav-links.open { display: flex; flex-direction: column; position: absolute; top: 64px; left: 0; right: 0; background: white; padding: 1rem; border-bottom: 1px solid #e2e8f0; z-index: 200; }
  .mobile-toggle { display: block; }
  .hero h1 { font-size: 2rem; }
  .hero { padding: 7rem 1.5rem 4rem; }
  .feature-grid { grid-template-columns: 1fr; }
  .article-grid { grid-template-columns: 1fr; }
  .hero-stats { flex-direction: column; gap: 1rem; }
  .stat-divider { width: 40px; height: 1px; }
  .hero-buttons { flex-direction: column; align-items: center; }
  .footer-top { grid-template-columns: 1fr; gap: 2rem; }
  .footer-bottom { flex-direction: column; gap: 1rem; text-align: center; }
}
</style>
