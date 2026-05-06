<script setup>
import { ref, onMounted } from 'vue'
import { useUserStore } from '@/stores/user'
import { useI18n } from 'vue-i18n'
import api from '@/api'

const { t } = useI18n()
const userStore = useUserStore()
const referralInfo = ref(null)

onMounted(async () => {
  const res = await api.referral.getReferralCode()
  if (res.code === 200) {
    referralInfo.value = res.data
  }
})
</script>

<template>
  <div class="member-home">
    <!-- Welcome Banner -->
    <div class="welcome-banner">
      <div class="welcome-content">
        <h2>{{ t('member.welcome') }}，{{ userStore.user?.name }} 👋</h2>
        <p>{{ userStore.user?.email }}</p>
      </div>
      <div class="welcome-decor">
        <svg viewBox="0 0 200 200" fill="none"><circle
          cx="100"
          cy="100"
          r="80"
          fill="rgba(255,255,255,0.1)"
        /><circle
          cx="100"
          cy="100"
          r="50"
          fill="rgba(255,255,255,0.08)"
        /></svg>
      </div>
    </div>

    <!-- Stats -->
    <div v-if="referralInfo" class="stats-grid">
      <div class="stat-card">
        <div class="stat-icon icon-balance">
          <svg
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="1.5"
          ><path d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
        </div>
        <div class="stat-info">
          <span class="stat-label">{{ t('member.balance') }}</span>
          <span class="stat-value">¥ {{ referralInfo.balance }}</span>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon icon-invite">
          <svg
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="1.5"
          ><path d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
        </div>
        <div class="stat-info">
          <span class="stat-label">{{ t('member.inviteCount') }}</span>
          <span class="stat-value">{{ referralInfo.invite_count }}</span>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon icon-code">
          <svg
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="1.5"
          ><path d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14" /></svg>
        </div>
        <div class="stat-info">
          <span class="stat-label">{{ t('member.referralCode') }}</span>
          <span class="stat-value code">{{ referralInfo.referral_code }}</span>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.member-home {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.welcome-banner {
  background: linear-gradient(135deg, #6366f1 0%, #06b6d4 100%);
  border-radius: 16px;
  padding: 2rem;
  color: white;
  position: relative;
  overflow: hidden;
}

.welcome-content h2 {
  font-size: 1.5rem;
  font-weight: 700;
  margin-bottom: 0.3rem;
}

.welcome-content p {
  opacity: 0.8;
  font-size: 0.9rem;
}

.welcome-decor {
  position: absolute;
  right: -20px;
  bottom: -20px;
  opacity: 0.3;
}

.welcome-decor svg { width: 160px; height: 160px; }

.stats-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 1.5rem;
}

.stat-card {
  background: white;
  border-radius: 16px;
  padding: 1.5rem;
  display: flex;
  align-items: center;
  gap: 1rem;
  border: 1px solid #f1f5f9;
  transition: all 0.3s;
}

.stat-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 30px rgba(0,0,0,0.06);
}

.stat-icon {
  width: 48px;
  height: 48px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.stat-icon svg { width: 24px; height: 24px; }

.icon-balance { background: rgba(99,102,241,0.1); color: #6366f1; }
.icon-invite { background: rgba(16,185,129,0.1); color: #10b981; }
.icon-code { background: rgba(6,182,212,0.1); color: #06b6d4; }

.stat-label {
  display: block;
  font-size: 0.8rem;
  color: #94a3b8;
  font-weight: 500;
  margin-bottom: 0.25rem;
}

.stat-value {
  display: block;
  font-size: 1.5rem;
  font-weight: 700;
  color: #1e293b;
}

.stat-value.code {
  font-size: 1.1rem;
  font-family: 'SF Mono', 'Fira Code', monospace;
  letter-spacing: 1px;
  color: #6366f1;
}

@media (max-width: 768px) {
  .stats-grid { grid-template-columns: 1fr; }
}
</style>
