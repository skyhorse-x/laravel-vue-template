<script setup>
import { ref, onMounted } from 'vue'
import api from '@/api'
import { ElTable, ElTableColumn, ElPagination, ElButton, ElDialog, ElForm, ElFormItem, ElInput, ElMessage, ElSelect, ElOption as ElSelectOption } from 'element-plus'

const articles = ref([])
const total = ref(0)
const page = ref(1)
const perPage = 10
const searchForm = ref({ title: '', category: '', status: '' })
const dialogVisible = ref(false)
const editForm = ref({ id: '', title: '', content: '', cover: '', category: '', status: 1, sort: 0 })
const categories = ref([])

onMounted(async () => { await loadArticles(); await loadCategories() })

async function loadArticles() {
  const res = await api.articles.list({ page: page.value, per_page: perPage, ...searchForm.value })
  if (res.code === 200) { articles.value = res.data.data; total.value = res.data.total }
}

async function loadCategories() {
  const res = await api.articles.categories()
  if (res.code === 200) categories.value = res.data
}

function handlePageChange(val) { page.value = val; loadArticles() }

function openAddDialog() {
  editForm.value = { id: '', title: '', content: '', cover: '', category: '', status: 1, sort: 0 }
  dialogVisible.value = true
}

function openEditDialog(article) {
  editForm.value = { id: article.id, title: article.title, content: article.content, cover: article.cover || '', category: article.category || '', status: article.status, sort: article.sort }
  dialogVisible.value = true
}

async function saveArticle() {
  let res = editForm.value.id ? await api.articles.update(editForm.value.id, editForm.value) : await api.articles.create(editForm.value)
  if (res.code === 200) { ElMessage.success(editForm.value.id ? '更新成功' : '创建成功'); dialogVisible.value = false; loadArticles() }
  else { ElMessage.error(res.message) }
}

async function deleteArticle(id) {
  const res = await api.articles.delete(id)
  if (res.code === 200) { ElMessage.success('删除成功'); loadArticles() } else { ElMessage.error(res.message) }
}

function search() { page.value = 1; loadArticles() }
</script>

<template>
  <div class="page-container">
    <div class="page-header">
      <h2 class="page-title">
        文章管理
      </h2>
      <ElButton type="primary" @click="openAddDialog">
        新增文章
      </ElButton>
    </div>

    <div class="card">
      <div class="search-bar">
        <ElInput
          v-model="searchForm.title"
          placeholder="搜索标题"
          style="width: 180px;"
          @keyup.enter="search"
        />
        <ElSelect
          v-model="searchForm.category"
          placeholder="分类"
          style="width: 130px;"
          clearable
        >
          <ElSelectOption
            v-for="cat in categories"
            :key="cat"
            :value="cat"
            :label="cat"
          />
        </ElSelect>
        <ElSelect
          v-model="searchForm.status"
          placeholder="状态"
          style="width: 120px;"
          clearable
        >
          <ElSelectOption :value="1" label="发布" />
          <ElSelectOption :value="0" label="草稿" />
        </ElSelect>
        <ElButton type="primary" plain @click="search">
          搜索
        </ElButton>
      </div>

      <ElTable :data="articles" stripe>
        <ElTableColumn prop="id" label="ID" width="70" />
        <ElTableColumn
          prop="title"
          label="标题"
          min-width="200"
          show-overflow-tooltip
        />
        <ElTableColumn prop="category" label="分类" width="100" />
        <ElTableColumn prop="status" label="状态" width="80">
          <template #default="scope">
            <span class="status-tag" :class="scope.row.status === 1 ? 'active' : 'draft'">{{ scope.row.status === 1 ? '发布' : '草稿' }}</span>
          </template>
        </ElTableColumn>
        <ElTableColumn prop="view_count" label="阅读" width="80" />
        <ElTableColumn prop="sort" label="排序" width="70" />
        <ElTableColumn prop="created_at" label="创建时间" min-width="170" />
        <ElTableColumn label="操作" width="150" fixed="right">
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
              @click="deleteArticle(scope.row.id)"
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
      :visible="dialogVisible"
      :title="editForm.id ? '编辑文章' : '新增文章'"
      width="680px"
      @close="dialogVisible = false"
    >
      <ElForm :model="editForm" label-width="70px">
        <ElFormItem label="标题">
          <ElInput v-model="editForm.title" placeholder="请输入标题" />
        </ElFormItem>
        <ElFormItem label="分类">
          <ElSelect v-model="editForm.category" style="width: 100%;">
            <ElSelectOption value="" label="请选择" />
            <ElSelectOption
              v-for="cat in categories"
              :key="cat"
              :value="cat"
              :label="cat"
            />
          </ElSelect>
        </ElFormItem>
        <ElFormItem label="封面">
          <ElInput v-model="editForm.cover" placeholder="请输入封面URL" />
        </ElFormItem>
        <ElFormItem label="内容">
          <ElInput
            v-model="editForm.content"
            type="textarea"
            :rows="6"
            placeholder="请输入内容"
          />
        </ElFormItem>
        <ElFormItem label="排序">
          <ElInput v-model.number="editForm.sort" type="number" placeholder="排序值" />
        </ElFormItem>
        <ElFormItem label="状态">
          <ElSelect v-model="editForm.status">
            <ElSelectOption :value="1" label="发布" />
            <ElSelectOption :value="0" label="草稿" />
          </ElSelect>
        </ElFormItem>
      </ElForm>
      <template #footer>
        <ElButton @click="dialogVisible = false">
          取消
        </ElButton>
        <ElButton type="primary" @click="saveArticle">
          保存
        </ElButton>
      </template>
    </ElDialog>
  </div>
</template>

<style scoped>
.page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; }
.page-title { font-size: 1.1rem; font-weight: 700; color: #1e293b; }
.card { background: white; border-radius: 12px; padding: 20px; border: 1px solid #f1f5f9; }
.search-bar { display: flex; gap: 10px; margin-bottom: 16px; flex-wrap: wrap; }
.status-tag { display: inline-block; padding: 2px 10px; border-radius: 10px; font-size: 0.75rem; font-weight: 500; }
.status-tag.active { background: #ecfdf5; color: #10b981; }
.status-tag.draft { background: #fffbeb; color: #f59e0b; }
.pagination-wrap { display: flex; justify-content: flex-end; margin-top: 16px; }
</style>
