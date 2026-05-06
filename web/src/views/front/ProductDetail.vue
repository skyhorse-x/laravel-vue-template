<script setup>
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import api from '@/api'
import { ElMessage } from 'element-plus'

const route = useRoute()
const router = useRouter()
const product = ref(null)
const loading = ref(true)
const quantity = ref(1)
const activeImage = ref(0)

onMounted(async () => {
  const res = await api.products.get(route.params.id)
  loading.value = false
  if (res.code === 200) {
    product.value = res.data
  }
})

function goBack() { router.back() }

function selectImage(index) { activeImage.value = index }

function changeQuantity(delta) {
  const newVal = quantity.value + delta
  if (newVal >= 1 && newVal <= (product.value?.stock || 999)) {
    quantity.value = newVal
  }
}

async function addToCart() {
  ElMessage.success('商品已添加到购物车')
}

function buyNow() {
  ElMessage.info('购买功能开发中...')
}
</script>

<template>
  <div class="product-detail-page">
    <div class="top-bar">
      <div class="container">
        <button class="back-btn" @click="goBack">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M19 12H5M12 19l-7-7 7-7"/>
          </svg>
          返回
        </button>
        <nav class="breadcrumb">
          <router-link to="/">首页</router-link>
          <span class="separator">/</span>
          <router-link to="/products">商品</router-link>
          <span class="separator" v-if="product">/</span>
          <span v-if="product" class="current">{{ product.name }}</span>
        </nav>
      </div>
    </div>

    <div v-if="loading" class="loading-state">
      <div class="spinner"></div>
      <p>加载中...</p>
    </div>

    <div v-else-if="!product" class="not-found">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="80" height="80">
        <path d="M4 7h16M4 7a2 2 0 00-2 2v10a2 2 0 002 2h16a2 2 0 002-2V9a2 2 0 00-2-2M4 7l1.5-3h13L20 7M9 11h6"/>
      </svg>
      <h2>商品不存在</h2>
      <p>该商品可能已下架或已被删除</p>
      <button class="btn-primary" @click="router.push('/products')">返回商品列表</button>
    </div>

    <div v-else class="product-content">
      <div class="container">
        <div class="product-main">
          <div class="gallery-section">
            <div class="main-image">
              <div class="image-wrapper">
                <img
                  v-if="product.cover && activeImage === 0"
                  :src="product.cover"
                  :alt="product.name"
                />
                <img
                  v-else-if="product.images && product.images[activeImage - 1]"
                  :src="product.images[activeImage - 1]"
                  :alt="product.name"
                />
                <div v-else class="image-placeholder">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1">
                    <path d="M4 7h16M4 7a2 2 0 00-2 2v10a2 2 0 002 2h16a2 2 0 002-2V9a2 2 0 00-2-2M4 7l1.5-3h13L20 7M9 11h6"/>
                  </svg>
                </div>
              </div>
              <div v-if="product.is_hot || product.is_new || product.is_featured" class="badges">
                <span v-if="product.is_hot" class="badge hot">
                  <svg viewBox="0 0 24 24" fill="currentColor" width="14" height="14">
                    <path d="M17.66 11.2C17.43 10.9 17.15 10.64 16.89 10.38C16.22 9.78 15.46 9.35 14.82 8.72C13.33 7.26 13 4.85 13.95 3C13 3.23 12.17 3.75 11.46 4.32C8.87 6.4 7.85 10.07 9.07 13.22C9.11 13.32 9.15 13.42 9.15 13.55C9.15 13.77 9 13.97 8.8 14.05C8.57 14.15 8.33 14.09 8.14 13.93C8.08 13.88 8.04 13.83 8 13.76C6.87 12.33 6.69 10.28 7.45 8.64C5.78 10 4.87 12.3 5 14.47C5.06 14.97 5.12 15.47 5.29 15.97C5.43 16.57 5.7 17.17 6 17.7C7.08 19.43 8.95 20.67 10.96 20.92C13.1 21.19 15.39 20.8 17.03 19.32C18.86 17.66 19.5 15 18.56 12.72L18.43 12.46C18.22 12 17.66 11.2 17.66 11.2Z"/>
                  </svg>
                  热门商品
                </span>
                <span v-if="product.is_new" class="badge new">
                  <svg viewBox="0 0 24 24" fill="currentColor" width="14" height="14">
                    <path d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z"/>
                  </svg>
                  新品上市
                </span>
                <span v-if="product.is_featured" class="badge featured">
                  <svg viewBox="0 0 24 24" fill="currentColor" width="14" height="14">
                    <path d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z"/>
                  </svg>
                  精选推荐
                </span>
              </div>
            </div>
            <div v-if="product.images && product.images.length" class="thumbnail-list">
              <div
                class="thumbnail"
                :class="{ active: activeImage === 0 }"
                @click="selectImage(0)"
              >
                <img :src="product.cover" :alt="product.name" />
              </div>
              <div
                v-for="(img, index) in product.images"
                :key="index"
                class="thumbnail"
                :class="{ active: activeImage === index + 1 }"
                @click="selectImage(index + 1)"
              >
                <img :src="img" :alt="product.name" />
              </div>
            </div>
          </div>

          <div class="info-section">
            <h1 class="product-name">{{ product.name }}</h1>

            <p class="product-desc">{{ product.description }}</p>

            <div class="price-card">
              <div class="price-row">
                <span class="price-label">售价</span>
                <div class="price-value">
                  <span class="current-price">¥{{ product.price }}</span>
                  <span v-if="product.original_price > product.price" class="original-price">
                    ¥{{ product.original_price }}
                  </span>
                </div>
              </div>
              <div v-if="product.original_price > product.price" class="discount-tag">
                节省 ¥{{ (product.original_price - product.price).toFixed(2) }}
              </div>
            </div>

            <div class="stats-row">
              <div class="stat-item">
                <span class="stat-label">销量</span>
                <span class="stat-value">{{ product.sales || 0 }} 件</span>
              </div>
              <div class="stat-item">
                <span class="stat-label">库存</span>
                <span class="stat-value" :class="{ low: product.stock < 10 }">
                  {{ product.stock }} 件
                  <span v-if="product.stock < 10" class="low-stock">库存紧张</span>
                </span>
              </div>
              <div v-if="product.category_name" class="stat-item">
                <span class="stat-label">分类</span>
                <span class="stat-value category">{{ product.category_name }}</span>
              </div>
            </div>

            <div class="quantity-section">
              <span class="quantity-label">购买数量</span>
              <div class="quantity-control">
                <button class="qty-btn" @click="changeQuantity(-1)">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="5" y1="12" x2="19" y2="12"/>
                  </svg>
                </button>
                <input type="number" v-model="quantity" min="1" :max="product.stock" />
                <button class="qty-btn" @click="changeQuantity(1)">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="12" y1="5" x2="12" y2="19"/>
                    <line x1="5" y1="12" x2="19" y2="12"/>
                  </svg>
                </button>
              </div>
            </div>

            <div class="action-buttons">
              <button class="btn-buy" @click="buyNow">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="18" height="18">
                  <path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/>
                  <line x1="3" y1="6" x2="21" y2="6"/>
                  <path d="M16 10a4 4 0 01-8 0"/>
                </svg>
                立即购买
              </button>
              <button class="btn-cart" @click="addToCart">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="18" height="18">
                  <circle cx="9" cy="21" r="1"/>
                  <circle cx="20" cy="21" r="1"/>
                  <path d="M1 1h4l2.68 13.39a2 2 0 002 1.61h9.72a2 2 0 002-1.61L23 6H6"/>
                </svg>
                加入购物车
              </button>
            </div>

            <div class="service-promises">
              <div class="promise-item">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
                正品保证
              </div>
              <div class="promise-item">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <rect x="1" y="3" width="15" height="13"/>
                  <polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/>
                  <circle cx="5.5" cy="18.5" r="2.5"/>
                  <circle cx="18.5" cy="18.5" r="2.5"/>
                </svg>
                快速发货
              </div>
              <div class="promise-item">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/>
                </svg>
                7天退换
              </div>
            </div>
          </div>
        </div>

        <div v-if="product.content" class="detail-section">
          <div class="section-header">
            <h2>
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="22" height="22">
                <path d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"/>
              </svg>
              商品详情
            </h2>
          </div>
          <div class="detail-content" v-html="product.content"></div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.product-detail-page {
  min-height: 100vh;
  background: linear-gradient(135deg, #fafbfc 0%, #f0f9ff 100%);
}

.top-bar {
  background: white;
  border-bottom: 1px solid rgba(0,0,0,0.04);
  padding: 1rem 0;
  position: sticky;
  top: 0;
  z-index: 100;
  box-shadow: 0 2px 10px rgba(0,0,0,0.04);
}

.container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 1.5rem;
}

.back-btn {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.6rem 1.2rem;
  border: 1px solid #e2e8f0;
  background: white;
  border-radius: 10px;
  cursor: pointer;
  font-size: 0.9rem;
  color: #64748b;
  transition: all 0.2s;
  margin-bottom: 0.75rem;
}

.back-btn:hover {
  border-color: #6366f1;
  color: #6366f1;
}

.back-btn svg {
  width: 18px;
  height: 18px;
}

.breadcrumb {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-size: 0.85rem;
}

.breadcrumb a {
  color: #64748b;
  text-decoration: none;
  transition: color 0.2s;
}

.breadcrumb a:hover {
  color: #6366f1;
}

.breadcrumb .separator {
  color: #cbd5e1;
}

.breadcrumb .current {
  color: #1e293b;
  font-weight: 500;
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

.not-found {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 8rem 2rem;
  color: #94a3b8;
}

.not-found svg {
  margin-bottom: 1.5rem;
  opacity: 0.5;
}

.not-found h2 {
  font-size: 1.5rem;
  font-weight: 700;
  color: #64748b;
  margin-bottom: 0.5rem;
}

.not-found p {
  margin-bottom: 2rem;
}

.btn-primary {
  padding: 0.875rem 2rem;
  background: linear-gradient(135deg, #6366f1, #8b5cf6);
  color: white;
  border: none;
  border-radius: 12px;
  font-size: 1rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s;
}

.btn-primary:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 25px rgba(99, 102, 241, 0.35);
}

.product-content {
  padding: 2rem 0 4rem;
}

.product-main {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 3rem;
  margin-bottom: 2rem;
}

@media (max-width: 900px) {
  .product-main {
    grid-template-columns: 1fr;
    gap: 2rem;
  }
}

.gallery-section {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.main-image {
  position: relative;
  border-radius: 20px;
  overflow: hidden;
  background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
  box-shadow: 0 4px 20px rgba(0,0,0,0.06);
}

.image-wrapper {
  aspect-ratio: 1;
  display: flex;
  align-items: center;
  justify-content: center;
}

.image-wrapper img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.image-placeholder {
  color: #cbd5e1;
}

.image-placeholder svg {
  width: 100px;
  height: 100px;
}

.badges {
  position: absolute;
  top: 16px;
  left: 16px;
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.badge {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 6px 14px;
  border-radius: 25px;
  font-size: 0.8rem;
  font-weight: 600;
  color: white;
  backdrop-filter: blur(4px);
}

.badge svg {
  flex-shrink: 0;
}

.badge.hot {
  background: linear-gradient(135deg, #ff416c, #ff4b2b);
  box-shadow: 0 4px 12px rgba(255, 65, 108, 0.4);
}

.badge.new {
  background: linear-gradient(135deg, #00b4db, #0083b0);
  box-shadow: 0 4px 12px rgba(0, 180, 219, 0.4);
}

.badge.featured {
  background: linear-gradient(135deg, #f59e0b, #d97706);
  box-shadow: 0 4px 12px rgba(245, 158, 11, 0.4);
}

.thumbnail-list {
  display: flex;
  gap: 0.75rem;
  flex-wrap: wrap;
}

.thumbnail {
  width: 80px;
  height: 80px;
  border-radius: 12px;
  overflow: hidden;
  cursor: pointer;
  border: 2px solid transparent;
  transition: all 0.2s;
  opacity: 0.6;
}

.thumbnail:hover {
  opacity: 1;
}

.thumbnail.active {
  border-color: #6366f1;
  opacity: 1;
  box-shadow: 0 0 0 2px rgba(99, 102, 241, 0.2);
}

.thumbnail img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.info-section {
  display: flex;
  flex-direction: column;
  gap: 1.25rem;
}

.product-name {
  font-size: 1.75rem;
  font-weight: 700;
  color: #1e293b;
  line-height: 1.3;
}

.product-desc {
  color: #64748b;
  font-size: 1rem;
  line-height: 1.6;
}

.price-card {
  background: linear-gradient(135deg, #fef2f2, #fff7ed);
  border-radius: 16px;
  padding: 1.25rem;
  border: 1px solid rgba(239, 68, 68, 0.1);
}

.price-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.price-label {
  color: #64748b;
  font-size: 0.95rem;
}

.price-value {
  display: flex;
  align-items: baseline;
  gap: 0.75rem;
}

.current-price {
  font-size: 2.25rem;
  font-weight: 800;
  background: linear-gradient(135deg, #ef4444, #f97316);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

.original-price {
  font-size: 1rem;
  color: #94a3b8;
  text-decoration: line-through;
}

.discount-tag {
  display: inline-block;
  margin-top: 0.75rem;
  padding: 4px 12px;
  background: linear-gradient(135deg, #ef4444, #f97316);
  color: white;
  border-radius: 20px;
  font-size: 0.75rem;
  font-weight: 600;
}

.stats-row {
  display: flex;
  gap: 2rem;
  padding: 1rem 0;
  border-top: 1px solid #f1f5f9;
  border-bottom: 1px solid #f1f5f9;
}

.stat-item {
  display: flex;
  flex-direction: column;
  gap: 0.25rem;
}

.stat-label {
  font-size: 0.8rem;
  color: #94a3b8;
}

.stat-value {
  font-size: 1rem;
  font-weight: 600;
  color: #1e293b;
}

.stat-value.low {
  color: #f59e0b;
}

.low-stock {
  font-size: 0.7rem;
  background: #fef3c7;
  color: #d97706;
  padding: 2px 6px;
  border-radius: 4px;
  margin-left: 4px;
}

.stat-value.category {
  color: #6366f1;
}

.quantity-section {
  display: flex;
  align-items: center;
  gap: 1.5rem;
}

.quantity-label {
  color: #64748b;
  font-size: 0.95rem;
}

.quantity-control {
  display: flex;
  align-items: center;
  border: 2px solid #e2e8f0;
  border-radius: 12px;
  overflow: hidden;
  background: white;
}

.qty-btn {
  width: 42px;
  height: 42px;
  border: none;
  background: #f8fafc;
  color: #64748b;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.2s;
}

.qty-btn:hover {
  background: #f1f5f9;
  color: #1e293b;
}

.qty-btn svg {
  width: 18px;
  height: 18px;
}

.quantity-control input {
  width: 60px;
  height: 42px;
  border: none;
  text-align: center;
  font-size: 1rem;
  font-weight: 600;
  color: #1e293b;
  background: white;
}

.quantity-control input:focus {
  outline: none;
}

.action-buttons {
  display: flex;
  gap: 1rem;
  margin-top: 0.5rem;
}

.btn-buy, .btn-cart {
  flex: 1;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
  padding: 1rem 1.5rem;
  border: none;
  border-radius: 14px;
  font-size: 1rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s;
}

.btn-buy {
  background: linear-gradient(135deg, #ef4444, #f97316);
  color: white;
  box-shadow: 0 4px 15px rgba(239, 68, 68, 0.3);
}

.btn-buy:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 25px rgba(239, 68, 68, 0.4);
}

.btn-cart {
  background: white;
  color: #6366f1;
  border: 2px solid #6366f1;
}

.btn-cart:hover {
  background: rgba(99, 102, 241, 0.05);
  transform: translateY(-2px);
}

.service-promises {
  display: flex;
  gap: 1.5rem;
  padding-top: 1rem;
  flex-wrap: wrap;
}

.promise-item {
  display: flex;
  align-items: center;
  gap: 0.4rem;
  font-size: 0.8rem;
  color: #64748b;
}

.promise-item svg {
  width: 16px;
  height: 16px;
  color: #10b981;
}

.detail-section {
  background: white;
  border-radius: 20px;
  overflow: hidden;
  box-shadow: 0 4px 20px rgba(0,0,0,0.04);
}

.section-header {
  padding: 1.5rem 2rem;
  border-bottom: 1px solid #f1f5f9;
}

.section-header h2 {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  font-size: 1.15rem;
  font-weight: 700;
  color: #1e293b;
}

.section-header svg {
  color: #6366f1;
}

.detail-content {
  padding: 2rem;
  color: #475569;
  line-height: 1.8;
  font-size: 0.95rem;
}

.detail-content :deep(img) {
  max-width: 100%;
  border-radius: 12px;
  margin: 1.5rem 0;
  box-shadow: 0 4px 15px rgba(0,0,0,0.08);
}

.detail-content :deep(p) {
  margin-bottom: 1rem;
}

.detail-content :deep(h1),
.detail-content :deep(h2),
.detail-content :deep(h3) {
  color: #1e293b;
  margin: 1.5rem 0 1rem;
}
</style>