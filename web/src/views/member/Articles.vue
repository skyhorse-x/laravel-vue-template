<script setup>
import { ref, onMounted } from 'vue'
import api from '@/api'
import { ElMessage } from 'element-plus'

const articles = ref([])
const total = ref(0)
const page = ref(1)
const perPage = 10
const dialogVisible = ref(false)
const editForm = ref({ id: '', title: '', content: '', category: '', cover: '', status: 1 })
const isEdit = ref(false)

onMounted(async () => { await loadArticles() })

async function loadArticles() {
  const res = await api.userArticles.list({ page: page.value, per_page: perPage })
  if (res.code === 200) {
    articles.value = res.data.data
    total.value = res.data.total
  }
}

function handlePageChange(val) {
  page.value = val
  loadArticles()
}

function openAddDialog() {
  editForm.value = { id: '', title: '', content: '', category: '', cover: '', status: 1 }
  isEdit.value = false
  dialogVisible.value = true
}

function openEditDialog(article) {
  editForm.value = {
    id: article.id,
    title: article.title,
    content: article.content,
    category: article.category,
    cover: article.cover || '',
    status: article.status
  }
  isEdit.value = true
  dialogVisible.value = true
}

async function saveArticle() {
  if (!editForm.value.title) {
    ElMessage.warning('请输入文章标题')
    return
  }
  let res
  if (isEdit.value) {
    res = await api.userArticles.update(editForm.value.id, editForm.value)
  } else {
    res = await api.userArticles.create(editForm.value)
  }
  if (res.code === 200) {
    ElMessage.success(isEdit.value ? '更新成功' : '创建成功')
    dialogVisible.value = false
    loadArticles()
  } else {
    ElMessage.error(res.message || '操作失败')
  }
}

async function deleteArticle(id) {
  const res = await api.userArticles.delete(id)
  if (res.code === 200) {
    ElMessage.success('删除成功')
    loadArticles()
  } else {
    ElMessage.error(res.message || '删除失败')
  }
}

function formatDate(date) {
  if (!date) return '-'
  return new Date(date).toLocaleString('zh-CN')
}
</script>

<template>
  <div class="articles-page">
    <div class="page-card">
      <div class="card-header">
        <h3>我的文章</h3>
        <button class="btn-primary" @click="openAddDialog">
          发布文章
        </button>
      </div>

      <div class="table-wrapper">
        <table v-if="articles.length">
          <thead>
            <tr>
              <th>ID</th>
              <th>标题</th>
              <th>分类</th>
              <th>状态</th>
              <th>浏览</th>
              <th>发布时间</th>
              <th>操作</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="item in articles" :key="item.id">
              <td>{{ item.id }}</td>
              <td class="title-cell">
                {{ item.title }}
              </td>
              <td>{{ item.category || '-' }}</td>
              <td>
                <span class="badge" :class="item.status === 1 ? 'success' : 'draft'">
                  {{ item.status === 1 ? '已发布' : '草稿' }}
                </span>
              </td>
              <td>{{ item.view_count || 0 }}</td>
              <td class="time">
                {{ formatDate(item.created_at) }}
              </td>
              <td class="actions">
                <button class="btn-link" @click="openEditDialog(item)">
                  编辑
                </button>
                <button class="btn-link danger" @click="deleteArticle(item.id)">
                  删除
                </button>
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
            <path d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
          </svg>
          <p>暂无文章，发布你的第一篇文章吧</p>
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
          <h4>{{ isEdit ? '编辑文章' : '发布文章' }}</h4>
          <button class="close-btn" @click="dialogVisible = false">
            ×
          </button>
        </div>
        <div class="dialog-body">
          <div class="form-group">
            <label>标题 *</label>
            <input v-model="editForm.title" type="text" placeholder="请输入文章标题">
          </div>
          <div class="form-group">
            <label>分类</label>
            <input v-model="editForm.category" type="text" placeholder="请输入文章分类">
          </div>
          <div class="form-group">
            <label>封面图</label>
            <input v-model="editForm.cover" type="text" placeholder="请输入封面图片URL">
          </div>
          <div class="form-group">
            <label>状态</label>
            <select v-model="editForm.status">
              <option :value="1">
                发布
              </option>
              <option :value="0">
                草稿
              </option>
            </select>
          </div>
          <div class="form-group">
            <label>内容 *</label>
            <textarea v-model="editForm.content" rows="8" placeholder="请输入文章内容" />
          </div>
        </div>
        <div class="dialog-footer">
          <button class="btn-default" @click="dialogVisible = false">
            取消
          </button>
          <button class="btn-primary" @click="saveArticle">
            保存
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.articles-page {}

.card-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1.25rem;
}

.card-header h3 {
  font-size: 1.15rem;
  font-weight: 700;
  color: #1e293b;
  margin: 0;
}

.btn-primary {
  background: linear-gradient(135deg, #6366f1, #8b5cf6);
  color: white;
  border: none;
  padding: 0.5rem 1rem;
  border-radius: 8px;
  font-size: 0.875rem;
  cursor: pointer;
  transition: opacity 0.2s;
}

.btn-primary:hover { opacity: 0.9; }

.btn-default {
  background: #f1f5f9;
  color: #64748b;
  border: none;
  padding: 0.5rem 1rem;
  border-radius: 8px;
  font-size: 0.875rem;
  cursor: pointer;
}

.btn-link {
  background: none;
  border: none;
  color: #6366f1;
  font-size: 0.8rem;
  cursor: pointer;
  padding: 0.25rem 0.5rem;
}

.btn-link.danger { color: #ef4444; }
.btn-link:hover { text-decoration: underline; }

.table-wrapper { overflow-x: auto; }

table {
  width: 100%;
  border-collapse: collapse;
}

th, td {
  text-align: left;
  padding: 0.75rem;
  border-bottom: 1px solid #f1f5f9;
}

th {
  font-size: 0.75rem;
  font-weight: 600;
  color: #64748b;
  text-transform: uppercase;
}

td { font-size: 0.875rem; color: #334155; }

.title-cell { font-weight: 500; }

.badge {
  display: inline-block;
  padding: 0.2rem 0.6rem;
  border-radius: 10px;
  font-size: 0.7rem;
  font-weight: 500;
}

.badge.success { background: #ecfdf5; color: #10b981; }
.badge.draft { background: #fef3c7; color: #d97706; }

.time { color: #94a3b8; font-size: 0.8rem; }

.actions { display: flex; gap: 0.5rem; }

.empty {
  text-align: center;
  padding: 3rem 1rem;
  color: #94a3b8;
}

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
  max-width: 560px;
  max-height: 90vh;
  overflow: hidden;
  display: flex;
  flex-direction: column;
}

.dialog-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1.25rem 1.5rem;
  border-bottom: 1px solid #f1f5f9;
}

.dialog-header h4 { margin: 0; font-size: 1.1rem; color: #1e293b; }

.close-btn {
  background: none;
  border: none;
  font-size: 1.5rem;
  color: #94a3b8;
  cursor: pointer;
  line-height: 1;
}

.dialog-body {
  padding: 1.5rem;
  overflow-y: auto;
  flex: 1;
}

.form-group { margin-bottom: 1rem; }
.form-group label { display: block; font-size: 0.875rem; font-weight: 500; color: #475569; margin-bottom: 0.5rem; }
.form-group input, .form-group select, .form-group textarea {
  width: 100%;
  padding: 0.625rem 0.875rem;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  font-size: 0.875rem;
  box-sizing: border-box;
}
.form-group textarea { resize: vertical; }

.dialog-footer {
  display: flex;
  justify-content: flex-end;
  gap: 0.75rem;
  padding: 1rem 1.5rem;
  border-top: 1px solid #f1f5f9;
}
</style>
