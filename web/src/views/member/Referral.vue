<script setup>
import { ref, onMounted } from 'vue'
import { useI18n } from 'vue-i18n'
import api from '@/api'

const { t } = useI18n()
const referralInfo = ref(null)
const referrals = ref([])

onMounted(async () => {
  const res = await api.referral.getReferralCode()
  if (res.code === 200) {
    referralInfo.value = res.data
    referrals.value = res.data.referrals || []
  }
})

function copyCode() {
  if (referralInfo.value?.referral_code) {
    navigator.clipboard.writeText(referralInfo.value.referral_code)
  }
}
</script>

<template>
  <div class="referral-page">
    <!-- Code Card -->
    <div v-if="referralInfo" class="code-card">
      <div class="code-card-inner">
        <div class="code-info">
          <h3>{{ t('member.myReferralCode') }}</h3>
          <div class="code-display">
            <span class="code-text">{{ referralInfo.referral_code }}</span>
            <button class="copy-btn" @click="copyCode">
              <svg
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="1.5"
                width="16"
                height="16"
              ><path d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" /></svg>
            </button>
          </div>
          <p class="code-tip">
            {{ t('member.referralTip') }}
          </p>
        </div>
      </div>
    </div>

    <!-- Stats -->
    <div v-if="referralInfo" class="stats-row">
      <div class="stat-card">
        <div class="stat-icon icon-invite">
          <svg
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="1.5"
          ><path d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
        </div>
        <div class="stat-info">
          <span class="stat-label">{{ t('member.inviteCount') }}</span>
          <span class="stat-value">{{ referralInfo.invite_count }}</span>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon icon-reward">
          <svg
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="1.5"
          ><path d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
        </div>
        <div class="stat-info">
          <span class="stat-label">{{ t('member.totalReward') }}</span>
          <span class="stat-value">¥ {{ referralInfo.balance }}</span>
        </div>
      </div>
    </div>

    <!-- Records -->
    <div class="records-card">
      <h3>{{ t('member.inviteRecords') }}</h3>
      <div class="table-wrapper">
        <table v-if="referrals.length">
          <thead>
            <tr>
              <th>ID</th>
              <th>{{ t('member.referee') }}</th>
              <th>{{ t('member.bonus') }}</th>
              <th>{{ t('member.status') }}</th>
              <th>{{ t('member.time') }}</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="item in referrals" :key="item.id">
              <td>{{ item.id }}</td>
              <td>{{ item.referee?.name }}</td>
              <td class="amount">
                ¥ {{ item.bonus }}
              </td>
              <td>
                <span class="badge" :class="item.status === 1 ? 'success' : 'warning'">
                  {{ item.status === 1 ? t('member.completed') : t('member.incomplete') }}
                </span>
              </td>
              <td class="time">
                {{ item.created_at }}
              </td>
            </tr>
          </tbody>
        </table>
        <div v-else class="empty">
          <svg
            viewBox="0 0 24 24"
            fill="none"
            stroke="#cbd5e1"
            stroke-width="1.5"
            width="48"
            height="48"
          ><path d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" /></svg>
          <p>暂无邀请记录</p>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.referral-page { display: flex; flex-direction: column; gap: 1.5rem; }

.code-card {
  background: linear-gradient(135deg, #6366f1, #06b6d4);
  border-radius: 16px;
  padding: 2px;
}

.code-card-inner {
  background: white;
  border-radius: 14px;
  padding: 2rem;
}

.code-info h3 {
  font-size: 1rem;
  font-weight: 600;
  color: #1e293b;
  margin-bottom: 1rem;
}

.code-display {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  margin-bottom: 0.75rem;
}

.code-text {
  font-size: 1.5rem;
  font-weight: 800;
  font-family: 'SF Mono', 'Fira Code', monospace;
  color: #667eea;
  letter-spacing: 2px;
}

.copy-btn {
  width: 36px;
  height: 36px;
  border-radius: 8px;
  border: 1.5px solid #e2e8f0;
  background: white;
  color: #64748b;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.2s;
}

.copy-btn:hover { border-color: #667eea; color: #667eea; }

.code-tip { font-size: 0.85rem; color: #94a3b8; }

.stats-row {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
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
}

.stat-icon {
  width: 48px;
  height: 48px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.stat-icon svg { width: 24px; height: 24px; }

.icon-invite { background: rgba(52,211,153,0.1); color: #34d399; }
.icon-reward { background: rgba(102,126,234,0.1); color: #667eea; }

.stat-label { display: block; font-size: 0.8rem; color: #94a3b8; margin-bottom: 0.25rem; }
.stat-value { display: block; font-size: 1.5rem; font-weight: 700; color: #1e293b; }

.records-card {
  background: white;
  border-radius: 16px;
  padding: 2rem;
  border: 1px solid #f1f5f9;
}

.records-card h3 {
  font-size: 1rem;
  font-weight: 700;
  color: #1e293b;
  margin-bottom: 1.25rem;
}

.table-wrapper { overflow-x: auto; }

table {
  width: 100%;
  border-collapse: collapse;
}

th {
  text-align: left;
  padding: 0.75rem 1rem;
  font-size: 0.8rem;
  font-weight: 600;
  color: #64748b;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  border-bottom: 1px solid #f1f5f9;
}

td {
  padding: 0.75rem 1rem;
  font-size: 0.9rem;
  color: #475569;
  border-bottom: 1px solid #f8fafc;
}

.amount { font-weight: 600; color: #34d399; }
.time { color: #94a3b8; font-size: 0.85rem; }

.badge {
  padding: 0.2rem 0.6rem;
  border-radius: 6px;
  font-size: 0.75rem;
  font-weight: 600;
}

.badge.success { background: rgba(52,211,153,0.1); color: #34d399; }
.badge.warning { background: rgba(251,146,60,0.1); color: #fb923c; }

.empty {
  text-align: center;
  padding: 3rem;
  color: #cbd5e1;
}

.empty p { margin-top: 0.75rem; font-size: 0.9rem; }

@media (max-width: 768px) {
  .stats-row { grid-template-columns: 1fr; }
}
</style>
