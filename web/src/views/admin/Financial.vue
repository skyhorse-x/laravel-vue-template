<script setup>
import { ref, onMounted } from 'vue'
import api from '@/api'
import { ElTable, ElTableColumn, ElPagination, ElButton, ElDialog, ElForm, ElFormItem, ElInput, ElMessage, ElSelect, ElOption as ElSelectOption } from 'element-plus'

const records = ref([])
const total = ref(0)
const page = ref(1)
const perPage = 10
const statistics = ref({ income: 0, expense: 0, profit: 0 })
const dialogVisible = ref(false)
const editForm = ref({ user_id: '', type: 'income', amount: '', title: '', description: '', status: 1 })
const users = ref([])

onMounted(async () => { await loadRecords(); await loadStatistics(); await loadUsers() })

async function loadRecords() {
  const res = await api.financial.list({ page: page.value, per_page: perPage })
  if (res.code === 200) { records.value = res.data.data; total.value = res.data.total }
}

async function loadStatistics() {
  const res = await api.financial.statistics()
  if (res.code === 200) statistics.value = res.data
}

async function loadUsers() {
  const res = await api.users.list({ per_page: 100 })
  if (res.code === 200) users.value = res.data.data
}

function handlePageChange(val) { page.value = val; loadRecords() }

function openAddDialog() {
  editForm.value = { user_id: '', type: 'income', amount: '', title: '', description: '', status: 1 }
  dialogVisible.value = true
}

async function saveRecord() {
  const res = await api.financial.create(editForm.value)
  if (res.code === 200) { ElMessage.success('创建成功'); dialogVisible.value = false; loadRecords(); loadStatistics() }
  else { ElMessage.error(res.message) }
}
</script>

<template>
  <div class="page-container">
    <div class="page-header">
      <h2 class="page-title">
        财务管理
      </h2>
      <ElButton type="primary" @click="openAddDialog">
        新增记录
      </ElButton>
    </div>

    <div class="stats-row">
      <div class="stat-card income">
        <div class="stat-value">
          +{{ statistics.income }}
        </div>
        <div class="stat-label">
          总收入
        </div>
      </div>
      <div class="stat-card expense">
        <div class="stat-value">
          -{{ statistics.expense }}
        </div>
        <div class="stat-label">
          总支出
        </div>
      </div>
      <div class="stat-card profit">
        <div class="stat-value" :class="statistics.profit >= 0 ? 'positive' : 'negative'">
          {{ statistics.profit >= 0 ? '+' : '' }}{{ statistics.profit }}
        </div>
        <div class="stat-label">
          净利润
        </div>
      </div>
    </div>

    <div class="card">
      <ElTable :data="records" stripe>
        <ElTableColumn prop="id" label="ID" width="70" />
        <ElTableColumn prop="user.name" label="用户" min-width="100" />
        <ElTableColumn prop="type" label="类型" width="80">
          <template #default="scope">
            <span class="type-tag" :class="scope.row.type === 'income' ? 'income' : 'expense'">{{ scope.row.type === 'income' ? '收入' : '支出' }}</span>
          </template>
        </ElTableColumn>
        <ElTableColumn prop="amount" label="金额" min-width="100">
          <template #default="scope">
            <span :class="scope.row.type === 'income' ? 'text-green' : 'text-red'">{{ scope.row.type === 'income' ? '+' : '-' }}{{ scope.row.amount }}</span>
          </template>
        </ElTableColumn>
        <ElTableColumn prop="title" label="标题" min-width="120" />
        <ElTableColumn prop="status" label="状态" width="80">
          <template #default="scope">
            <span class="status-tag" :class="scope.row.status === 1 ? 'active' : 'disabled'">{{ scope.row.status === 1 ? '成功' : '失败' }}</span>
          </template>
        </ElTableColumn>
        <ElTableColumn prop="created_at" label="时间" min-width="170" />
      </ElTable>

      <div class="pagination-wrap">
        <ElPagination
          v-model:current-page="page"
          :page-size="perPage"
          :total="total"
          layout="total, prev, pager, next"
          size="small"
          @current-change="handlePageChange"
        />
      </div>
    </div>

    <ElDialog
      :visible="dialogVisible"
      title="新增财务记录"
      width="480px"
      @close="dialogVisible = false"
    >
      <ElForm :model="editForm" label-width="80px">
        <ElFormItem label="用户">
          <ElSelect v-model="editForm.user_id" style="width: 100%;">
            <ElSelectOption
              v-for="user in users"
              :key="user.id"
              :value="user.id"
              :label="user.name"
            />
          </ElSelect>
        </ElFormItem>
        <ElFormItem label="类型">
          <ElSelect v-model="editForm.type" style="width: 100%;">
            <ElSelectOption value="income" label="收入" />
            <ElSelectOption value="expense" label="支出" />
          </ElSelect>
        </ElFormItem>
        <ElFormItem label="金额">
          <ElInput v-model.number="editForm.amount" type="number" placeholder="请输入金额" />
        </ElFormItem>
        <ElFormItem label="标题">
          <ElInput v-model="editForm.title" placeholder="请输入标题" />
        </ElFormItem>
        <ElFormItem label="描述">
          <ElInput
            v-model="editForm.description"
            type="textarea"
            :rows="3"
            placeholder="请输入描述"
          />
        </ElFormItem>
        <ElFormItem label="状态">
          <ElSelect v-model="editForm.status" style="width: 100%;">
            <ElSelectOption :value="1" label="成功" />
            <ElSelectOption :value="0" label="失败" />
          </ElSelect>
        </ElFormItem>
      </ElForm>
      <template #footer>
        <ElButton @click="dialogVisible = false">
          取消
        </ElButton>
        <ElButton type="primary" @click="saveRecord">
          保存
        </ElButton>
      </template>
    </ElDialog>
  </div>
</template>

<style scoped>
.page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; }
.page-title { font-size: 1.1rem; font-weight: 700; color: #1e293b; }

.stats-row { display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px; margin-bottom: 16px; }

.stat-card {
  background: white;
  border-radius: 12px;
  padding: 20px;
  border: 1px solid #f1f5f9;
  text-align: center;
}

.stat-card.income { border-left: 3px solid #10b981; }
.stat-card.expense { border-left: 3px solid #ef4444; }
.stat-card.profit { border-left: 3px solid #6366f1; }

.stat-value { font-size: 1.5rem; font-weight: 700; margin-bottom: 4px; }
.stat-value.positive { color: #10b981; }
.stat-value.negative { color: #ef4444; }
.stat-label { font-size: 0.8rem; color: #94a3b8; }

.card { background: white; border-radius: 12px; padding: 20px; border: 1px solid #f1f5f9; }

.type-tag { display: inline-block; padding: 2px 10px; border-radius: 10px; font-size: 0.75rem; font-weight: 500; }
.type-tag.income { background: #ecfdf5; color: #10b981; }
.type-tag.expense { background: #fef2f2; color: #ef4444; }

.status-tag { display: inline-block; padding: 2px 10px; border-radius: 10px; font-size: 0.75rem; font-weight: 500; }
.status-tag.active { background: #ecfdf5; color: #10b981; }
.status-tag.disabled { background: #fef2f2; color: #ef4444; }

.text-green { color: #10b981; font-weight: 600; }
.text-red { color: #ef4444; font-weight: 600; }

.pagination-wrap { display: flex; justify-content: flex-end; margin-top: 16px; }

@media (max-width: 768px) {
  .stats-row { grid-template-columns: 1fr; }
}
</style>
