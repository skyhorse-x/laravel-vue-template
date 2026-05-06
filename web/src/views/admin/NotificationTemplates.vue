<script setup>
import { ref, onMounted } from 'vue'
import api from '@/api'
import { ElTable, ElTableColumn, ElPagination, ElButton, ElDialog, ElForm, ElFormItem, ElInput, ElMessage, ElSelect, ElOption as ElSelectOption } from 'element-plus'

const templates = ref([])
const total = ref(0)
const page = ref(1)
const perPage = 15
const searchForm = ref({ keyword: '', type: '', status: '' })
const dialogVisible = ref(false)
const editForm = ref({ id: '', code: '', name: '', type: '', subject: '', content: '', variables: '', status: 1 })
const typeOptions = [{ value: 'system', label: '系统通知' }, { value: 'order', label: '订单通知' }, { value: 'article', label: '文章通知' }, { value: 'message', label: '消息通知' }]

onMounted(async () => { await loadTemplates() })

async function loadTemplates() {
  const res = await api.admin.notificationTemplates.list({ page: page.value, per_page: perPage, ...searchForm.value })
  if (res.code === 200) { templates.value = res.data.data; total.value = res.data.total }
}

function handlePageChange(val) { page.value = val; loadTemplates() }
function search() { page.value = 1; loadTemplates() }
function resetSearch() { searchForm.value = { keyword: '', type: '', status: '' }; page.value = 1; loadTemplates() }

function openAddDialog() {
  editForm.value = { id: '', code: '', name: '', type: '', subject: '', content: '', variables: '', status: 1 }
  dialogVisible.value = true
}

function openEditDialog(template) {
  editForm.value = { ...template, variables: Array.isArray(template.variables) ? template.variables.join(', ') : template.variables }
  dialogVisible.value = true
}

async function saveTemplate() {
  const data = { ...editForm.value }
  if (data.variables) {
    data.variables = data.variables.split(',').map(v => v.trim()).filter(v => v)
  } else {
    data.variables = []
  }
  let res
  if (editForm.value.id) {
    res = await api.admin.notificationTemplates.update(editForm.value.id, data)
  } else {
    res = await api.admin.notificationTemplates.create(data)
  }
  if (res.code === 200) { ElMessage.success(editForm.value.id ? '更新成功' : '创建成功'); dialogVisible.value = false; loadTemplates() }
  else { ElMessage.error(res.message) }
}

async function deleteTemplate(id) {
  const res = await api.admin.notificationTemplates.delete(id)
  if (res.code === 200) { ElMessage.success('删除成功'); loadTemplates() } else { ElMessage.error(res.message) }
}
</script>

<template>
  <div class="page-container">
    <div class="page-header">
      <h2 class="page-title">
        通知模板
      </h2>
      <ElButton type="primary" @click="openAddDialog">
        新增模板
      </ElButton>
    </div>

    <div class="card">
      <div class="search-bar">
        <ElInput
          v-model="searchForm.keyword"
          placeholder="搜索模板名称"
          style="width: 180px;"
          clearable
          @keyup.enter="search"
        />
        <ElSelect
          v-model="searchForm.type"
          placeholder="通知类型"
          style="width: 140px;"
          clearable
          @change="search"
        >
          <ElSelectOption
            v-for="opt in typeOptions"
            :key="opt.value"
            :value="opt.value"
            :label="opt.label"
          />
        </ElSelect>
        <ElSelect
          v-model="searchForm.status"
          placeholder="状态"
          style="width: 120px;"
          clearable
          @change="search"
        >
          <ElSelectOption :value="1" label="启用" />
          <ElSelectOption :value="0" label="禁用" />
        </ElSelect>
        <ElButton type="primary" plain @click="search">
          搜索
        </ElButton>
        <ElButton plain @click="resetSearch">
          重置
        </ElButton>
      </div>

      <ElTable :data="templates" :row-key="(row) => row.id" stripe>
        <ElTableColumn prop="id" label="ID" width="70" />
        <ElTableColumn prop="code" label="模板代码" min-width="120" />
        <ElTableColumn prop="name" label="模板名称" min-width="150" />
        <ElTableColumn prop="type" label="类型" width="100">
          <template #default="scope">
            <span>{{ typeOptions.find(t => t.value === scope.row.type)?.label || scope.row.type }}</span>
          </template>
        </ElTableColumn>
        <ElTableColumn
          prop="subject"
          label="邮件主题"
          min-width="150"
          show-overflow-tooltip
        />
        <ElTableColumn
          prop="content"
          label="内容"
          min-width="200"
          show-overflow-tooltip
        />
        <ElTableColumn prop="status" label="状态" width="80">
          <template #default="scope">
            <span class="status-tag" :class="scope.row.status === 1 ? 'active' : 'disabled'">
              {{ scope.row.status === 1 ? '启用' : '禁用' }}
            </span>
          </template>
        </ElTableColumn>
        <ElTableColumn prop="created_at" label="创建时间" min-width="170" />
        <ElTableColumn label="操作" width="120" fixed="right">
          <template #default="scope">
            <ElButton
              link
              type="primary"
              size="small"
              @click="openEditDialog(scope.row)"
            >
              编辑
            </ElButton>
            <ElButton
              link
              type="danger"
              size="small"
              @click="deleteTemplate(scope.row.id)"
            >
              删除
            </ElButton>
          </template>
        </ElTableColumn>
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
      v-model="dialogVisible"
      :title="editForm.id ? '编辑模板' : '新增模板'"
      width="580px"
      @close="dialogVisible = false"
    >
      <ElForm :model="editForm" label-width="100px">
        <ElFormItem label="模板代码">
          <ElInput v-model="editForm.code" placeholder="如: order_paid" :disabled="!!editForm.id" />
        </ElFormItem>
        <ElFormItem label="模板名称">
          <ElInput v-model="editForm.name" placeholder="请输入模板名称" />
        </ElFormItem>
        <ElFormItem label="通知类型">
          <ElSelect v-model="editForm.type" placeholder="请选择类型" style="width: 100%;">
            <ElSelectOption
              v-for="opt in typeOptions"
              :key="opt.value"
              :value="opt.value"
              :label="opt.label"
            />
          </ElSelect>
        </ElFormItem>
        <ElFormItem label="邮件主题">
          <ElInput v-model="editForm.subject" placeholder="邮件通知时使用" />
        </ElFormItem>
        <ElFormItem label="内容模板">
          <ElInput
            v-model="editForm.content"
            type="textarea"
            :rows="4"
            placeholder="支持变量: {name}, {amount}, {date} 等"
          />
        </ElFormItem>
        <ElFormItem label="变量说明">
          <ElInput v-model="editForm.variables" placeholder="如: name, amount, date (多个用逗号分隔)" />
        </ElFormItem>
        <ElFormItem label="状态">
          <ElSelect v-model="editForm.status" style="width: 100%;">
            <ElSelectOption :value="1" label="启用" />
            <ElSelectOption :value="0" label="禁用" />
          </ElSelect>
        </ElFormItem>
      </ElForm>
      <template #footer>
        <ElButton @click="dialogVisible = false">
          取消
        </ElButton>
        <ElButton type="primary" @click="saveTemplate">
          保存
        </ElButton>
      </template>
    </ElDialog>
  </div>
</template>

<style scoped>
.page-container {}
.page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; }
.page-title { font-size: 1.1rem; font-weight: 700; color: var(--color-text); }
.card { background: var(--color-card); border-radius: 12px; padding: 20px; border: 1px solid var(--color-border-light); }
.search-bar { display: flex; gap: 10px; margin-bottom: 16px; flex-wrap: wrap; }
.status-tag { display: inline-block; padding: 2px 10px; border-radius: 10px; font-size: 0.75rem; font-weight: 500; }
.status-tag.active { background: #ecfdf5; color: #10b981; }
.status-tag.disabled { background: #fef2f2; color: #ef4444; }
.pagination-wrap { margin-top: 16px; display: flex; justify-content: flex-end; }
</style>