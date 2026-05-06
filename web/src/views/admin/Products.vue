<script setup>
import { ref, onMounted } from 'vue'
import api from '@/api'
import { ElTable, ElTableColumn, ElPagination, ElButton, ElDialog, ElForm, ElFormItem, ElInput, ElMessage, ElSelect, ElOption as ElSelectOption, ElSwitch, ElImage, ElInputNumber, ElTabPane, ElTabs } from 'element-plus'

const activeTab = ref('list')
const products = ref([])
const categories = ref([])
const total = ref(0)
const page = ref(1)
const perPage = 15
const searchForm = ref({ keyword: '', category_id: '', status: '' })
const dialogVisible = ref(false)
const categoryDialogVisible = ref(false)
const editForm = ref({ id: '', name: '', slug: '', description: '', content: '', price: '', original_price: '', stock: 0, cover: '', images: [], category_id: '', category_name: '', status: 1, is_featured: 0, is_hot: 0, is_new: 0, sort: 0 })
const categoryForm = ref({ id: '', name: '', slug: '', description: '', parent_id: 0, sort: 0, status: 1 })
const selectedIds = ref([])

onMounted(async () => { await loadProducts(); await loadCategories() })

async function loadProducts() {
  const res = await api.admin.products.list({ page: page.value, per_page: perPage, ...searchForm.value })
  if (res.code === 200) { products.value = res.data.data; total.value = res.data.total }
}

async function loadCategories() {
  const res = await api.admin.products.categories()
  if (res.code === 200) categories.value = res.data
}

function handlePageChange(val) { page.value = val; loadProducts() }
function search() { page.value = 1; loadProducts() }
function resetSearch() { searchForm.value = { keyword: '', category_id: '', status: '' }; page.value = 1; loadProducts() }

function handleSelectionChange(val) { selectedIds.value = val.map(item => item.id) }

function openAddDialog() {
  editForm.value = { id: '', name: '', slug: '', description: '', content: '', price: '', original_price: '', stock: 0, cover: '', images: [], category_id: '', category_name: '', status: 1, is_featured: 0, is_hot: 0, is_new: 0, sort: 0 }
  dialogVisible.value = true
}

function openEditDialog(product) {
  editForm.value = { ...product, price: Number(product.price), original_price: Number(product.original_price), images: product.images || [] }
  dialogVisible.value = true
}

async function saveProduct() {
  let res
  if (editForm.value.id) {
    res = await api.admin.products.update(editForm.value.id, editForm.value)
  } else {
    res = await api.admin.products.create(editForm.value)
  }
  if (res.code === 200) { ElMessage.success(editForm.value.id ? '更新成功' : '创建成功'); dialogVisible.value = false; loadProducts() }
  else { ElMessage.error(res.message) }
}

async function deleteProduct(id) {
  const res = await api.admin.products.delete(id)
  if (res.code === 200) { ElMessage.success('删除成功'); loadProducts() } else { ElMessage.error(res.message) }
}

async function batchDelete() {
  if (selectedIds.value.length === 0) { ElMessage.warning('请选择要删除的商品'); return }
  const res = await api.admin.products.batchDelete(selectedIds.value)
  if (res.code === 200) { ElMessage.success('批量删除成功'); selectedIds.value = []; loadProducts() } else { ElMessage.error(res.message) }
}

function openCategoryDialog(row = null) {
  if (row) {
    categoryForm.value = { ...row }
  } else {
    categoryForm.value = { id: '', name: '', slug: '', description: '', parent_id: 0, sort: 0, status: 1 }
  }
  categoryDialogVisible.value = true
}

async function saveCategory() {
  let res
  if (categoryForm.value.id) {
    res = await api.admin.products.updateCategory(categoryForm.value.id, categoryForm.value)
  } else {
    res = await api.admin.products.createCategory(categoryForm.value)
  }
  if (res.code === 200) { ElMessage.success(categoryForm.value.id ? '更新成功' : '创建成功'); categoryDialogVisible.value = false; loadCategories() }
  else { ElMessage.error(res.message) }
}

async function deleteCategory(id) {
  const res = await api.admin.products.deleteCategory(id)
  if (res.code === 200) { ElMessage.success('删除成功'); loadCategories() } else { ElMessage.error(res.message) }
}

function onCategoryChange(val) {
  const cat = categories.value.find(c => c.id === val)
  if (cat) editForm.value.category_name = cat.name
}
</script>

<template>
  <div class="page-container">
    <el-tabs v-model="activeTab">
      <el-tab-pane label="商品列表" name="list">
        <div class="page-header">
          <h2 class="page-title">商品管理</h2>
          <div class="page-actions">
            <ElButton type="primary" @click="openAddDialog">新增商品</ElButton>
            <ElButton type="danger" plain @click="batchDelete" :disabled="selectedIds.length === 0">批量删除</ElButton>
          </div>
        </div>

        <div class="card">
          <div class="search-bar">
            <ElInput v-model="searchForm.keyword" placeholder="搜索商品名称" style="width: 180px;" @keyup.enter="search" clearable />
            <ElSelect v-model="searchForm.category_id" placeholder="选择分类" style="width: 150px;" clearable @change="search">
              <ElSelectOption v-for="cat in categories" :key="cat.id" :value="cat.id" :label="cat.name" />
            </ElSelect>
            <ElSelect v-model="searchForm.status" placeholder="状态" style="width: 120px;" clearable @change="search">
              <ElSelectOption :value="1" label="上架" />
              <ElSelectOption :value="0" label="下架" />
            </ElSelect>
            <ElButton type="primary" plain @click="search">搜索</ElButton>
            <ElButton plain @click="resetSearch">重置</ElButton>
          </div>

          <ElTable :data="products" @selection-change="handleSelectionChange" :row-key="(row) => row.id" stripe>
            <ElTableColumn type="selection" width="50" />
            <ElTableColumn prop="id" label="ID" width="70" />
            <ElTableColumn prop="cover" label="封面" width="80">
              <template #default="scope">
                <ElImage v-if="scope.row.cover" :src="scope.row.cover" fit="cover" style="width: 50px; height: 50px; border-radius: 4px;" />
                <span v-else>-</span>
              </template>
            </ElTableColumn>
            <ElTableColumn prop="name" label="商品名称" min-width="150" show-overflow-tooltip />
            <ElTableColumn prop="category_name" label="分类" width="100" />
            <ElTableColumn prop="price" label="价格" width="100">
              <template #default="scope">¥{{ scope.row.price }}</template>
            </ElTableColumn>
            <ElTableColumn prop="stock" label="库存" width="80" />
            <ElTableColumn prop="sales" label="销量" width="80" />
            <ElTableColumn prop="is_featured" label="推荐" width="80">
              <template #default="scope"><ElSwitch v-model="scope.row.is_featured" :loading="true" /></template>
            </ElTableColumn>
            <ElTableColumn prop="is_hot" label="热门" width="80">
              <template #default="scope"><ElSwitch v-model="scope.row.is_hot" :loading="true" /></template>
            </ElTableColumn>
            <ElTableColumn prop="is_new" label="新品" width="80">
              <template #default="scope"><ElSwitch v-model="scope.row.is_new" :loading="true" /></template>
            </ElTableColumn>
            <ElTableColumn prop="status" label="状态" width="80">
              <template #default="scope">
                <span class="status-tag" :class="scope.row.status === 1 ? 'active' : 'disabled'">
                  {{ scope.row.status === 1 ? '上架' : '下架' }}
                </span>
              </template>
            </ElTableColumn>
            <ElTableColumn prop="created_at" label="创建时间" min-width="170" />
            <ElTableColumn label="操作" width="120" fixed="right">
              <template #default="scope">
                <ElButton link type="primary" size="small" @click="openEditDialog(scope.row)">编辑</ElButton>
                <ElButton link type="danger" size="small" @click="deleteProduct(scope.row.id)">删除</ElButton>
              </template>
            </ElTableColumn>
          </ElTable>

          <div class="pagination-wrap">
            <ElPagination v-model:current-page="page" :page-size="perPage" :total="total" @current-change="handlePageChange" layout="total, prev, pager, next" size="small" />
          </div>
        </div>
      </el-tab-pane>

      <el-tab-pane label="商品分类" name="categories">
        <div class="page-header">
          <h2 class="page-title">商品分类</h2>
          <ElButton type="primary" @click="openCategoryDialog()">新增分类</ElButton>
        </div>

        <div class="card">
          <ElTable :data="categories" :row-key="(row) => row.id" stripe>
            <ElTableColumn prop="id" label="ID" width="70" />
            <ElTableColumn prop="name" label="分类名称" min-width="120" />
            <ElTableColumn prop="slug" label="Slug" min-width="120" />
            <ElTableColumn prop="description" label="描述" min-width="200" show-overflow-tooltip />
            <ElTableColumn prop="sort" label="排序" width="80" />
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
                <ElButton link type="primary" size="small" @click="openCategoryDialog(scope.row)">编辑</ElButton>
                <ElButton link type="danger" size="small" @click="deleteCategory(scope.row.id)">删除</ElButton>
              </template>
            </ElTableColumn>
          </ElTable>
        </div>
      </el-tab-pane>
    </el-tabs>

    <ElDialog v-model="dialogVisible" :title="editForm.id ? '编辑商品' : '新增商品'" width="680px" @close="dialogVisible = false">
      <ElForm :model="editForm" label-width="100px">
        <ElFormItem label="商品名称"><ElInput v-model="editForm.name" placeholder="请输入商品名称" /></ElFormItem>
        <ElFormItem label="分类">
          <ElSelect v-model="editForm.category_id" placeholder="请选择分类" style="width: 100%;" @change="onCategoryChange">
            <ElSelectOption :value="0" label="无分类" />
            <ElSelectOption v-for="cat in categories" :key="cat.id" :value="cat.id" :label="cat.name" />
          </ElSelect>
        </ElFormItem>
        <ElFormItem label="价格"><ElInputNumber v-model="editForm.price" :min="0" :precision="2" style="width: 100%;" /></ElFormItem>
        <ElFormItem label="原价"><ElInputNumber v-model="editForm.original_price" :min="0" :precision="2" style="width: 100%;" /></ElFormItem>
        <ElFormItem label="库存"><ElInputNumber v-model="editForm.stock" :min="0" style="width: 100%;" /></ElFormItem>
        <ElFormItem label="封面图"><ElInput v-model="editForm.cover" placeholder="请输入封面图URL" /></ElFormItem>
        <ElFormItem label="商品描述"><ElInput v-model="editForm.description" type="textarea" :rows="2" placeholder="请输入商品描述" /></ElFormItem>
        <ElFormItem label="商品详情"><ElInput v-model="editForm.content" type="textarea" :rows="4" placeholder="请输入商品详情" /></ElFormItem>
        <ElFormItem label="排序"><ElInputNumber v-model="editForm.sort" :min="0" style="width: 100%;" /></ElFormItem>
        <ElFormItem label="状态">
          <ElSelect v-model="editForm.status" style="width: 100%;">
            <ElSelectOption :value="1" label="上架" />
            <ElSelectOption :value="0" label="下架" />
          </ElSelect>
        </ElFormItem>
        <ElFormItem label="标签">
          <ElSwitch v-model="editForm.is_featured" active-text="推荐" inactive-text="" style="margin-right: 16px;" />
          <ElSwitch v-model="editForm.is_hot" active-text="热门" inactive-text="" style="margin-right: 16px;" />
          <ElSwitch v-model="editForm.is_new" active-text="新品" inactive-text="" />
        </ElFormItem>
      </ElForm>
      <template #footer>
        <ElButton @click="dialogVisible = false">取消</ElButton>
        <ElButton type="primary" @click="saveProduct">保存</ElButton>
      </template>
    </ElDialog>

    <ElDialog v-model="categoryDialogVisible" :title="categoryForm.id ? '编辑分类' : '新增分类'" width="480px" @close="categoryDialogVisible = false">
      <ElForm :model="categoryForm" label-width="100px">
        <ElFormItem label="分类名称"><ElInput v-model="categoryForm.name" placeholder="请输入分类名称" /></ElFormItem>
        <ElFormItem label="Slug"><ElInput v-model="categoryForm.slug" placeholder="请输入slug" /></ElFormItem>
        <ElFormItem label="描述"><ElInput v-model="categoryForm.description" type="textarea" :rows="2" placeholder="请输入描述" /></ElFormItem>
        <ElFormItem label="排序"><ElInputNumber v-model="categoryForm.sort" :min="0" style="width: 100%;" /></ElFormItem>
        <ElFormItem label="状态">
          <ElSelect v-model="categoryForm.status" style="width: 100%;">
            <ElSelectOption :value="1" label="启用" />
            <ElSelectOption :value="0" label="禁用" />
          </ElSelect>
        </ElFormItem>
      </ElForm>
      <template #footer>
        <ElButton @click="categoryDialogVisible = false">取消</ElButton>
        <ElButton type="primary" @click="saveCategory">保存</ElButton>
      </template>
    </ElDialog>
  </div>
</template>

<style scoped>
.page-container {}

.page-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 16px;
}

.page-title {
  font-size: 1.1rem;
  font-weight: 700;
  color: var(--color-text);
}

.page-actions {
  display: flex;
  gap: 8px;
}

.card {
  background: var(--color-card);
  border-radius: 12px;
  padding: 20px;
  border: 1px solid var(--color-border-light);
}

.search-bar {
  display: flex;
  gap: 10px;
  margin-bottom: 16px;
  flex-wrap: wrap;
}

.status-tag {
  display: inline-block;
  padding: 2px 10px;
  border-radius: 10px;
  font-size: 0.75rem;
  font-weight: 500;
}

.status-tag.active { background: #ecfdf5; color: #10b981; }
.status-tag.disabled { background: #fef2f2; color: #ef4444; }

.pagination-wrap {
  margin-top: 16px;
  display: flex;
  justify-content: flex-end;
}
</style>