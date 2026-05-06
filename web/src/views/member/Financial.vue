<script setup>
import { ref, onMounted } from 'vue'
import api from '@/api'
import { ElMessage } from 'element-plus'

const records = ref([])
const total = ref(0)
const page = ref(1)
const perPage = 10
const balance = ref(0)
const activeTab = ref('record')
const dialogVisible = ref(false)
const dialogType = ref('deposit')
const amount = ref('')
const paymentMethod = ref('alipay')
const withdrawAccount = ref('')
const withdrawName = ref('')

onMounted(async () => { await loadData() })

async function loadData() {
  await Promise.all([
    loadRecords(),
    loadBalance()
  ])
}

async function loadBalance() {
  const res = await api.user.getBalance()
  if (res.code === 200) {
    balance.value = res.data.balance || 0
  }
}

async function loadRecords() {
  const res = await api.financial.userList({ page: page.value, per_page: perPage })
  if (res.code === 200) {
    records.value = res.data.data
    total.value = res.data.total
  }
}

function handlePageChange(val) {
  page.value = val
  loadRecords()
}

function openDepositDialog() {
  dialogType.value = 'deposit'
  amount.value = ''
  paymentMethod.value = 'alipay'
  dialogVisible.value = true
}

function openWithdrawDialog() {
  dialogType.value = 'withdraw'
  amount.value = ''
  withdrawAccount.value = ''
  withdrawName.value = ''
  dialogVisible.value = true
}

async function submitTransaction() {
  const numAmount = parseFloat(amount.value)
  if (!numAmount || numAmount <= 0) {
    ElMessage.warning('请输入有效金额')
    return
  }

  if (dialogType.value === 'deposit') {
    const res = await api.financial.createDeposit({
      amount: numAmount,
      payment_method: paymentMethod.value
    })
    if (res.code === 200) {
      ElMessage.success('充值申请已提交，请完成支付')
      dialogVisible.value = false
      loadRecords()
    } else {
      ElMessage.error(res.message || '提交失败')
    }
  } else {
    if (!withdrawAccount.value) {
      ElMessage.warning('请输入提现账户')
      return
    }
    if (numAmount > balance.value) {
      ElMessage.warning('提现金额不能超过余额')
      return
    }
    const res = await api.financial.createWithdraw({
      amount: numAmount,
      account: withdrawAccount.value,
      account_name: withdrawName.value
    })
    if (res.code === 200) {
      ElMessage.success('提现申请已提交')
      dialogVisible.value = false
      loadRecords()
      loadBalance()
    } else {
      ElMessage.error(res.message || '提交失败')
    }
  }
}

function formatDate(date) {
  if (!date) return '-'
  return new Date(date).toLocaleString('zh-CN')
}

function getStatusText(status) {
  const map = {
    0: '处理中',
    1: '成功',
    2: '失败'
  }
  return map[status] || '未知'
}

function getStatusClass(status) {
  const map = {
    0: 'pending',
    1: 'success',
    2: 'failed'
  }
  return map[status] || ''
}

function getTypeText(type) {
  const map = {
    'deposit': '充值',
    'withdraw': '提现',
    'income': '收入',
    'expense': '支出'
  }
  return map[type] || type
}

function getTypeClass(type) {
  const map = {
    'deposit': 'deposit',
    'withdraw': 'withdraw',
    'income': 'income',
    'expense': 'expense'
  }
  return map[type] || ''
}
</script>

<template>
  <div class="financial-page">
    <div class="page-card">
      <div class="balance-section">
        <div class="balance-info">
          <span class="balance-label">我的余额</span>
          <span class="balance-value">¥ {{ balance.toFixed(2) }}</span>
        </div>
        <div class="balance-actions">
          <button class="btn-deposit" @click="openDepositDialog">
            充值
          </button>
          <button class="btn-withdraw" :disabled="balance <= 0" @click="openWithdrawDialog">
            提现
          </button>
        </div>
      </div>
    </div>

    <div class="page-card">
      <div class="tabs">
        <button class="tab" :class="{ active: activeTab === 'record' }" @click="activeTab = 'record'">
          交易记录
        </button>
        <button class="tab" :class="{ active: activeTab === 'deposit' }" @click="activeTab = 'deposit'">
          充值记录
        </button>
        <button class="tab" :class="{ active: activeTab === 'withdraw' }" @click="activeTab = 'withdraw'">
          提现记录
        </button>
      </div>

      <div class="table-wrapper">
        <table v-if="records.length">
          <thead>
            <tr>
              <th>ID</th>
              <th>类型</th>
              <th>金额</th>
              <th>说明</th>
              <th>状态</th>
              <th>时间</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="item in records" :key="item.id">
              <td>{{ item.id }}</td>
              <td>
                <span class="badge" :class="getTypeClass(item.type)">
                  {{ getTypeText(item.type) }}
                </span>
              </td>
              <td :class="item.type === 'income' || item.type === 'deposit' ? 'text-green' : 'text-red'">
                {{ item.type === 'income' || item.type === 'deposit' ? '+' : '-' }}{{ item.amount }}
              </td>
              <td>{{ item.title || '-' }}</td>
              <td>
                <span class="badge" :class="getStatusClass(item.status)">
                  {{ getStatusText(item.status) }}
                </span>
              </td>
              <td class="time">
                {{ formatDate(item.created_at) }}
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
          >
            <path d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
          </svg>
          <p>暂无交易记录</p>
        </div>
      </div>

      <div v-if="total > perPage" class="pagination">
        <button class="page-btn" :disabled="page <= 1" @click="handlePageChange(page - 1)">
          ‹
        </button>
        <span class="page-info">{{ page }} / {{ Math.ceil(total / perPage) }}</span>
        <button class="page-btn" :disabled="page >= Math.ceil(total / perPage)" @click="handlePageChange(page + 1)">
          ›
        </button>
      </div>
    </div>

    <div v-if="dialogVisible" class="dialog-overlay" @click.self="dialogVisible = false">
      <div class="dialog">
        <div class="dialog-header">
          <h4>{{ dialogType === 'deposit' ? '充值' : '提现' }}</h4>
          <button class="close-btn" @click="dialogVisible = false">
            ×
          </button>
        </div>
        <div class="dialog-body">
          <div v-if="dialogType === 'deposit'" class="form-group">
            <label>充值金额</label>
            <div class="amount-input">
              <span class="currency">¥</span>
              <input
                v-model="amount"
                type="number"
                min="0.01"
                step="0.01"
                placeholder="请输入充值金额"
              >
            </div>
          </div>
          <div v-if="dialogType === 'deposit'" class="form-group">
            <label>支付方式</label>
            <div class="payment-methods">
              <label class="payment-option" :class="{ selected: paymentMethod === 'alipay' }">
                <input v-model="paymentMethod" type="radio" value="alipay">
                <span class="method-icon alipay">支</span>
                <span>支付宝</span>
              </label>
              <label class="payment-option" :class="{ selected: paymentMethod === 'wechat' }">
                <input v-model="paymentMethod" type="radio" value="wechat">
                <span class="method-icon wechat">微</span>
                <span>微信支付</span>
              </label>
              <label class="payment-option" :class="{ selected: paymentMethod === 'bank' }">
                <input v-model="paymentMethod" type="radio" value="bank">
                <span class="method-icon bank">银</span>
                <span>银行卡</span>
              </label>
            </div>
          </div>
          <div v-if="dialogType === 'withdraw'" class="form-group">
            <label>提现金额</label>
            <div class="amount-input">
              <span class="currency">¥</span>
              <input
                v-model="amount"
                type="number"
                min="0.01"
                step="0.01"
                placeholder="可提现余额: {{ balance.toFixed(2) }}"
              >
            </div>
            <p class="form-tip">
              当前可提现余额：¥{{ balance.toFixed(2) }}
            </p>
          </div>
          <div v-if="dialogType === 'withdraw'" class="form-group">
            <label>提现账户</label>
            <input v-model="withdrawAccount" type="text" placeholder="请输入支付宝账号或银行卡号">
          </div>
          <div v-if="dialogType === 'withdraw'" class="form-group">
            <label>账户姓名</label>
            <input v-model="withdrawName" type="text" placeholder="请输入账户姓名">
          </div>
          <div v-if="dialogType === 'deposit'" class="quick-amounts">
            <button class="quick-btn" @click="amount = '100'">
              100
            </button>
            <button class="quick-btn" @click="amount = '500'">
              500
            </button>
            <button class="quick-btn" @click="amount = '1000'">
              1000
            </button>
            <button class="quick-btn" @click="amount = '2000'">
              2000
            </button>
          </div>
        </div>
        <div class="dialog-footer">
          <button class="btn-default" @click="dialogVisible = false">
            取消
          </button>
          <button class="btn-primary" @click="submitTransaction">
            {{ dialogType === 'deposit' ? '去支付' : '提交提现' }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.financial-page {}

.balance-section {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1.5rem;
  background: linear-gradient(135deg, #6366f1, #8b5cf6);
  border-radius: 12px;
  color: white;
}

.balance-info { display: flex; flex-direction: column; gap: 0.5rem; }
.balance-label { font-size: 0.875rem; opacity: 0.9; }
.balance-value { font-size: 2rem; font-weight: 700; }

.balance-actions { display: flex; gap: 0.75rem; }

.btn-deposit, .btn-withdraw {
  padding: 0.625rem 1.5rem;
  border-radius: 8px;
  font-size: 0.875rem;
  font-weight: 500;
  cursor: pointer;
  border: none;
  transition: opacity 0.2s;
}

.btn-deposit { background: white; color: #6366f1; }
.btn-withdraw { background: rgba(255,255,255,0.2); color: white; border: 1px solid rgba(255,255,255,0.3); }
.btn-withdraw:disabled { opacity: 0.5; cursor: not-allowed; }
.btn-deposit:hover, .btn-withdraw:hover { opacity: 0.9; }

.tabs {
  display: flex;
  gap: 0.5rem;
  margin-bottom: 1.25rem;
  border-bottom: 1px solid #f1f5f9;
  padding-bottom: 0.75rem;
}

.tab {
  padding: 0.5rem 1rem;
  border: none;
  background: none;
  color: #64748b;
  font-size: 0.875rem;
  cursor: pointer;
  border-radius: 6px;
  transition: all 0.2s;
}

.tab.active { background: #6366f1; color: white; }

.table-wrapper { overflow-x: auto; }

table { width: 100%; border-collapse: collapse; }
th, td { text-align: left; padding: 0.75rem; border-bottom: 1px solid #f1f5f9; }
th { font-size: 0.75rem; font-weight: 600; color: #64748b; text-transform: uppercase; }
td { font-size: 0.875rem; color: #334155; }

.badge {
  display: inline-block;
  padding: 0.2rem 0.6rem;
  border-radius: 10px;
  font-size: 0.7rem;
  font-weight: 500;
}

.badge.deposit { background: #dbeafe; color: #2563eb; }
.badge.withdraw { background: #fef3c7; color: #d97706; }
.badge.income { background: #ecfdf5; color: #10b981; }
.badge.expense { background: #fef2f2; color: #ef4444; }
.badge.success { background: #ecfdf5; color: #10b981; }
.badge.pending { background: #fef3c7; color: #d97706; }
.badge.failed { background: #fef2f2; color: #ef4444; }

.text-green { color: #10b981; font-weight: 500; }
.text-red { color: #ef4444; font-weight: 500; }
.time { color: #94a3b8; font-size: 0.8rem; }

.empty { text-align: center; padding: 3rem 1rem; color: #94a3b8; }
.empty p { margin-top: 0.75rem; font-size: 0.875rem; }

.pagination {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 0.5rem;
  margin-top: 1.5rem;
}

.page-btn {
  background: #f1f5f9;
  border: none;
  width: 32px;
  height: 32px;
  border-radius: 6px;
  cursor: pointer;
  color: #64748b;
}

.page-btn:disabled { opacity: 0.5; cursor: not-allowed; }
.page-info { font-size: 0.875rem; color: #64748b; }

.dialog-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
}

.dialog {
  background: white;
  border-radius: 16px;
  width: 90%;
  max-width: 440px;
  overflow: hidden;
}

.dialog-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1.25rem 1.5rem;
  border-bottom: 1px solid #f1f5f9;
}

.dialog-header h4 { margin: 0; font-size: 1.1rem; color: #1e293b; }
.close-btn { background: none; border: none; font-size: 1.5rem; color: #94a3b8; cursor: pointer; line-height: 1; }

.dialog-body { padding: 1.5rem; }

.form-group { margin-bottom: 1.25rem; }
.form-group label { display: block; font-size: 0.875rem; font-weight: 500; color: #475569; margin-bottom: 0.5rem; }
.form-group input[type="text"],
.form-group input[type="number"] {
  width: 100%;
  padding: 0.625rem 0.875rem;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  font-size: 0.875rem;
  box-sizing: border-box;
}

.amount-input {
  display: flex;
  align-items: center;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  overflow: hidden;
}

.amount-input .currency {
  padding: 0.625rem 0.875rem;
  background: #f8fafc;
  color: #64748b;
  font-size: 1.1rem;
}

.amount-input input {
  border: none;
  flex: 1;
}

.form-tip { font-size: 0.8rem; color: #94a3b8; margin-top: 0.5rem; }

.payment-methods { display: flex; gap: 0.75rem; }
.payment-option {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 0.5rem;
  padding: 1rem;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  cursor: pointer;
  flex: 1;
  transition: all 0.2s;
}

.payment-option input { display: none; }
.payment-option.selected { border-color: #6366f1; background: #eef2ff; }
.payment-option span:last-child { font-size: 0.8rem; color: #475569; }

.method-icon {
  width: 32px;
  height: 32px;
  border-radius: 6px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 0.875rem;
  font-weight: 600;
  color: white;
}

.method-icon.alipay { background: #1677ff; }
.method-icon.wechat { background: #07c160; }
.method-icon.bank { background: #666; }

.quick-amounts { display: flex; gap: 0.5rem; margin-top: 1rem; }
.quick-btn {
  flex: 1;
  padding: 0.5rem;
  border: 1px solid #e2e8f0;
  background: white;
  border-radius: 6px;
  cursor: pointer;
  font-size: 0.875rem;
  color: #475569;
}

.quick-btn:hover { border-color: #6366f1; color: #6366f1; }

.dialog-footer {
  display: flex;
  justify-content: flex-end;
  gap: 0.75rem;
  padding: 1rem 1.5rem;
  border-top: 1px solid #f1f5f9;
}

.btn-primary {
  background: linear-gradient(135deg, #6366f1, #8b5cf6);
  color: white;
  border: none;
  padding: 0.625rem 1.5rem;
  border-radius: 8px;
  font-size: 0.875rem;
  cursor: pointer;
}

.btn-default {
  background: #f1f5f9;
  color: #64748b;
  border: none;
  padding: 0.625rem 1.5rem;
  border-radius: 8px;
  font-size: 0.875rem;
  cursor: pointer;
}
</style>
