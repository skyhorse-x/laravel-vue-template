<script setup>
import { ref, onMounted, computed } from 'vue'
import { useRouter } from 'vue-router'
import api from '@/api'
import { ElInput, ElSelect, ElOption as ElSelectOption, ElButton } from 'element-plus'

const router = useRouter()
const products = ref([])
const categories = ref([])
const loading = ref(false)
const searchForm = ref({ keyword: '', category_id: '' })
const page = ref(1)
const total = ref(0)
const perPage = 12

onMounted(async () => {
  await loadCategories()
  await loadProducts()
})

async function loadCategories() {
  const res = await api.products.categories()
  if (res.code === 200) {
    categories.value = res.data || []
  }
}

async function loadProducts() {
  loading.value = true
  const res = await api.products.list({
    page: page.value,
    per_page: perPage,
    ...searchForm.value
  })
  loading.value = false
  if (res.code === 200) {
    products.value = res.data.data || []
    total.value = res.data.total || 0
  }
}

function handlePageChange(val) {
  page.value = val
  loadProducts()
  window.scrollTo({ top: 0, behavior: 'smooth' })
}

function search() {
  page.value = 1
  loadProducts()
}

function resetSearch() {
  searchForm.value = { keyword: '', category_id: '' }
  page.value = 1
  loadProducts()
}

function viewDetail(product) {
  router.push(`/product/${product.id}`)
}

const totalPages = computed(() => Math.ceil(total.value / perPage))
</script>

<template>
  <div class="products-page">
    <div class="hero-section">
      <div class="hero-bg">
        <div class="orb orb-1"></div>
        <div class="orb orb-2"></div>
        <div class="orb orb-3"></div>
      </div>
      <div class="hero-content">
        <h1>精选商品</h1>
        <p>发现品质好物，享受购物乐趣</p>
      </div>
    </div>

    <div class="container">
      <div class="filter-section">
        <div class="filter-left">
          <div class="search-box">
            <svg class="search-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <circle cx="11" cy="11" r="8"/>
              <path d="m21 21-4.35-4.35"/>
            </svg>
            <ElInput
              v-model="searchForm.keyword"
              placeholder="搜索商品..."
              @keyup.enter="search"
              clearable
            />
          </div>
          <ElSelect
            v-model="searchForm.category_id"
            placeholder="全部分类"
            clearable
            @change="search"
          >
            <ElSelectOption
              v-for="cat in categories"
              :key="cat.id"
              :value="cat.id"
              :label="cat.name"
            />
          </ElSelect>
          <ElButton type="primary" @click="search">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16">
              <circle cx="11" cy="11" r="8"/>
              <path d="m21 21-4.35-4.35"/>
            </svg>
            搜索
          </ElButton>
          <ElButton @click="resetSearch">重置</ElButton>
        </div>
        <div class="filter-right">
          <span class="result-count">共 {{ total }} 件商品</span>
        </div>
      </div>

      <div v-if="loading" class="loading-state">
        <div class="spinner"></div>
        <p>加载中...</p>
      </div>

      <div v-else-if="products.length === 0" class="empty-state">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="64" height="64">
          <path d="M4 7h16M4 7a2 2 0 00-2 2v10a2 2 0 002 2h16a2 2 0 002-2V9a2 2 0 00-2-2M4 7l1.5-3h13L20 7M9 11h6"/>
        </svg>
        <h3>暂无商品</h3>
        <p>商品正在上架中，敬请期待...</p>
      </div>

      <div v-else class="product-grid">
        <div
          v-for="product in products"
          :key="product.id"
          class="product-card"
          @click="viewDetail(product)"
        >
          <div class="card-image">
            <img
              v-if="product.cover"
              :src="product.cover"
              :alt="product.name"
            />
            <div v-else class="image-placeholder">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1">
                <path d="M4 7h16M4 7a2 2 0 00-2 2v10a2 2 0 002 2h16a2 2 0 002-2V9a2 2 0 00-2-2M4 7l1.5-3h13L20 7M9 11h6"/>
              </svg>
            </div>
            <div class="card-overlay">
              <span class="view-btn">查看详情</span>
            </div>
            <div class="badges">
              <span v-if="product.is_hot" class="badge hot">
                <svg viewBox="0 0 24 24" fill="currentColor" width="12" height="12">
                  <path d="M17.66 11.2C17.43 10.9 17.15 10.64 16.89 10.38C16.22 9.78 15.46 9.35 14.82 8.72C13.33 7.26 13 4.85 13.95 3C13 3.23 12.17 3.75 11.46 4.32C8.87 6.4 7.85 10.07 9.07 13.22C9.11 13.32 9.15 13.42 9.15 13.55C9.15 13.77 9 13.97 8.8 14.05C8.57 14.15 8.33 14.09 8.14 13.93C8.08 13.88 8.04 13.83 8 13.76C6.87 12.33 6.69 10.28 7.45 8.64C5.78 10 4.87 12.3 5 14.47C5.06 14.97 5.12 15.47 5.29 15.97C5.43 16.57 5.7 17.17 6 17.7C7.08 19.43 8.95 20.67 10.96 20.92C13.1 21.19 15.39 20.8 17.03 19.32C18.86 17.66 19.5 15 18.56 12.72L18.43 12.46C18.22 12 17.66 11.2 17.66 11.2Z"/>
                </svg>
                热门
              </span>
              <span v-if="product.is_new" class="badge new">
                <svg viewBox="0 0 24 24" fill="currentColor" width="12" height="12">
                  <path d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z"/>
                </svg>
                新品
              </span>
              <span v-if="product.is_featured" class="badge featured">
                <svg viewBox="0 0 24 24" fill="currentColor" width="12" height="12">
                  <path d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z"/>
                </svg>
                推荐
              </span>
            </div>
          </div>
          <div class="card-body">
            <h3 class="product-name">{{ product.name }}</h3>
            <p class="product-desc">{{ product.description || '暂无描述' }}</p>
            <div class="card-bottom">
              <div class="price-info">
                <span class="current-price">¥{{ product.price }}</span>
                <span v-if="product.original_price > product.price" class="original-price">
                  ¥{{ product.original_price }}
                </span>
              </div>
              <div class="meta-info">
                <span v-if="product.category_name" class="category-tag">{{ product.category_name }}</span>
                <span class="sales">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="12" height="12">
                    <path d="M20.59 13.41l-7.17 7.17a2 2 0 01-2.83 0L2 12V2h10l8.59 8.59a2 2 0 010 2.82z"/>
                    <line x1="7" y1="7" x2="7.01" y2="7"/>
                  </svg>
                  {{ product.sales || 0 }}
                </span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div v-if="totalPages > 1" class="pagination">
        <button class="page-btn" :disabled="page === 1" @click="handlePageChange(page - 1)">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <polyline points="15 18 9 12 15 6"/>
          </svg>
          上一页
        </button>
        <div class="page-numbers">
          <button
            v-for="p in totalPages"
            :key="p"
            class="page-number"
            :class="{ active: p === page }"
            @click="handlePageChange(p)"
          >
            {{ p }}
          </button>
        </div>
        <button class="page-btn" :disabled="page >= totalPages" @click="handlePageChange(page + 1)">
          下一页
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <polyline points="9 18 15 12 9 6"/>
          </svg>
        </button>
      </div>
    </div>
  </div>
</template>

<style scoped>
.products-page {
  min-height: 100vh;
  background: linear-gradient(135deg, #fafbfc 0%, #f0f9ff 100%);
}

.hero-section {
  position: relative;
  padding: 5rem 2rem 4rem;
  text-align: center;
  overflow: hidden;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #f093fb 100%);
}

.hero-bg {
  position: absolute;
  inset: 0;
}

.orb {
  position: absolute;
  border-radius: 50%;
  filter: blur(60px);
  opacity: 0.4;
}

.orb-1 {
  width: 300px;
  height: 300px;
  background: #00d9ff;
  top: -100px;
  left: 10%;
}

.orb-2 {
  width: 250px;
  height: 250px;
  background: #ff6b6b;
  bottom: -80px;
  right: 15%;
}

.orb-3 {
  width: 200px;
  height: 200px;
  background: #ffd93d;
  top: 20%;
  right: 25%;
}

.hero-content {
  position: relative;
  z-index: 1;
}

.hero-content h1 {
  font-size: 3rem;
  font-weight: 800;
  color: white;
  margin-bottom: 0.75rem;
  text-shadow: 0 2px 20px rgba(0,0,0,0.15);
}

.hero-content p {
  font-size: 1.2rem;
  color: rgba(255,255,255,0.9);
}

.container {
  max-width: 1280px;
  margin: 0 auto;
  padding: 2rem;
}

.filter-section {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 2rem;
  gap: 1rem;
  flex-wrap: wrap;
}

.filter-left {
  display: flex;
  gap: 0.75rem;
  align-items: center;
  flex-wrap: wrap;
}

.search-box {
  position: relative;
  display: flex;
  align-items: center;
}

.search-icon {
  position: absolute;
  left: 12px;
  width: 18px;
  height: 18px;
  color: #94a3b8;
  z-index: 1;
  pointer-events: none;
}

.search-box :deep(.el-input__wrapper) {
  padding-left: 38px;
}

.filter-left :deep(.el-select) {
  width: 140px;
}

.result-count {
  color: #64748b;
  font-size: 0.9rem;
}

.loading-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 6rem 2rem;
  color: #64748b;
}

.spinner {
  width: 48px;
  height: 48px;
  border: 3px solid #e2e8f0;
  border-top-color: #6366f1;
  border-radius: 50%;
  animation: spin 0.8s linear infinite;
  margin-bottom: 1rem;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

.empty-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 6rem 2rem;
  color: #94a3b8;
}

.empty-state svg {
  margin-bottom: 1.5rem;
  opacity: 0.5;
}

.empty-state h3 {
  font-size: 1.25rem;
  font-weight: 600;
  color: #64748b;
  margin-bottom: 0.5rem;
}

.empty-state p {
  font-size: 0.95rem;
}

.product-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 1.5rem;
}

@media (max-width: 1200px) {
  .product-grid { grid-template-columns: repeat(3, 1fr); }
}

@media (max-width: 900px) {
  .product-grid { grid-template-columns: repeat(2, 1fr); }
}

@media (max-width: 600px) {
  .product-grid { grid-template-columns: 1fr; }
  .filter-left { width: 100%; }
  .filter-left :deep(.el-select) { flex: 1; }
}

.product-card {
  background: white;
  border-radius: 16px;
  overflow: hidden;
  border: 1px solid rgba(0,0,0,0.04);
  cursor: pointer;
  transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
  box-shadow: 0 1px 3px rgba(0,0,0,0.04);
}

.product-card:hover {
  transform: translateY(-8px);
  box-shadow: 0 20px 40px rgba(99, 102, 241, 0.15);
  border-color: rgba(99, 102, 241, 0.2);
}

.card-image {
  position: relative;
  aspect-ratio: 1;
  overflow: hidden;
  background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
}

.card-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.5s ease;
}

.product-card:hover .card-image img {
  transform: scale(1.08);
}

.image-placeholder {
  width: 100%;
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #cbd5e1;
}

.image-placeholder svg {
  width: 64px;
  height: 64px;
}

.card-overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(to top, rgba(0,0,0,0.6) 0%, transparent 60%);
  display: flex;
  align-items: flex-end;
  justify-content: center;
  padding-bottom: 1.5rem;
  opacity: 0;
  transition: opacity 0.3s ease;
}

.product-card:hover .card-overlay {
  opacity: 1;
}

.view-btn {
  padding: 0.6rem 1.5rem;
  background: white;
  color: #1e293b;
  border-radius: 25px;
  font-size: 0.85rem;
  font-weight: 600;
  transform: translateY(20px);
  transition: transform 0.3s ease;
}

.product-card:hover .view-btn {
  transform: translateY(0);
}

.badges {
  position: absolute;
  top: 12px;
  left: 12px;
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.badge {
  display: inline-flex;
  align-items: center;
  gap: 4px;
  padding: 4px 10px;
  border-radius: 20px;
  font-size: 0.7rem;
  font-weight: 600;
  color: white;
  backdrop-filter: blur(4px);
}

.badge svg {
  flex-shrink: 0;
}

.badge.hot {
  background: linear-gradient(135deg, #ff416c, #ff4b2b);
  box-shadow: 0 2px 8px rgba(255, 65, 108, 0.4);
}

.badge.new {
  background: linear-gradient(135deg, #00b4db, #0083b0);
  box-shadow: 0 2px 8px rgba(0, 180, 219, 0.4);
}

.badge.featured {
  background: linear-gradient(135deg, #f59e0b, #d97706);
  box-shadow: 0 2px 8px rgba(245, 158, 11, 0.4);
}

.card-body {
  padding: 1.25rem;
}

.product-name {
  font-size: 1rem;
  font-weight: 600;
  color: #1e293b;
  margin-bottom: 0.5rem;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.product-desc {
  font-size: 0.85rem;
  color: #94a3b8;
  margin-bottom: 1rem;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
  line-height: 1.4;
}

.card-bottom {
  display: flex;
  justify-content: space-between;
  align-items: flex-end;
}

.price-info {
  display: flex;
  align-items: baseline;
  gap: 0.5rem;
}

.current-price {
  font-size: 1.35rem;
  font-weight: 700;
  background: linear-gradient(135deg, #ef4444, #f97316);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

.original-price {
  font-size: 0.8rem;
  color: #cbd5e1;
  text-decoration: line-through;
}

.meta-info {
  display: flex;
  flex-direction: column;
  align-items: flex-end;
  gap: 4px;
}

.category-tag {
  background: linear-gradient(135deg, #f1f5f9, #e2e8f0);
  padding: 3px 8px;
  border-radius: 6px;
  font-size: 0.7rem;
  color: #64748b;
}

.sales {
  display: flex;
  align-items: center;
  gap: 3px;
  font-size: 0.75rem;
  color: #94a3b8;
}

.pagination {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 0.5rem;
  margin-top: 3rem;
  flex-wrap: wrap;
}

.page-btn {
  display: flex;
  align-items: center;
  gap: 0.35rem;
  padding: 0.6rem 1rem;
  border: 1px solid #e2e8f0;
  background: white;
  border-radius: 10px;
  color: #64748b;
  font-size: 0.85rem;
  cursor: pointer;
  transition: all 0.2s;
}

.page-btn:hover:not(:disabled) {
  border-color: #6366f1;
  color: #6366f1;
}

.page-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.page-btn svg {
  width: 16px;
  height: 16px;
}

.page-numbers {
  display: flex;
  gap: 0.35rem;
}

.page-number {
  width: 36px;
  height: 36px;
  border: 1px solid #e2e8f0;
  background: white;
  border-radius: 10px;
  color: #64748b;
  font-size: 0.9rem;
  cursor: pointer;
  transition: all 0.2s;
}

.page-number:hover {
  border-color: #6366f1;
  color: #6366f1;
}

.page-number.active {
  background: linear-gradient(135deg, #6366f1, #8b5cf6);
  border-color: transparent;
  color: white;
  font-weight: 600;
}
</style>