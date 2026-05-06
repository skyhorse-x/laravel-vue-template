<template>
  <div class="settings-page">
    <el-tabs v-model="activeTab" class="settings-tabs">
      <!-- 网站设置 -->
      <el-tab-pane label="网站设置" name="site">
        <el-card class="settings-card">
          <el-form :model="siteSettings" label-width="120px">
            <el-form-item label="网站名称">
              <el-input v-model="siteSettings.site_name" placeholder="请输入网站名称" />
            </el-form-item>
            <el-form-item label="网站Logo">
              <el-input v-model="siteSettings.site_logo" placeholder="请输入Logo URL" />
            </el-form-item>
            <el-form-item label="网站URL">
              <el-input v-model="siteSettings.site_url" placeholder="请输入网站完整URL，如 https://example.com" />
            </el-form-item>
            <el-form-item label="网站描述">
              <el-input
                v-model="siteSettings.site_description"
                type="textarea"
                :rows="3"
                placeholder="请输入网站描述"
              />
            </el-form-item>
            <el-form-item label="关键词">
              <el-input v-model="siteSettings.site_keywords" placeholder="请输入网站关键词，多个用逗号分隔" />
            </el-form-item>
            <el-form-item label="默认语言">
              <el-select v-model="siteSettings.site_language" placeholder="请选择默认语言">
                <el-option label="简体中文" value="zh-CN" />
                <el-option label="繁体中文" value="zh-TW" />
                <el-option label="English" value="en" />
                <el-option label="日本語" value="ja" />
              </el-select>
            </el-form-item>
            <el-form-item label="时区">
              <el-select v-model="siteSettings.site_timezone" placeholder="请选择时区">
                <el-option label="中国上海 (Asia/Shanghai)" value="Asia/Shanghai" />
                <el-option label="中国北京 (Asia/Hong_Kong)" value="Asia/Hong_Kong" />
                <el-option label="日本东京 (Asia/Tokyo)" value="Asia/Tokyo" />
                <el-option label="美国洛杉矶 (America/Los_Angeles)" value="America/Los_Angeles" />
                <el-option label="UTC" value="UTC" />
              </el-select>
            </el-form-item>
            <el-divider content-position="left">
              维护设置
            </el-divider>
            <el-form-item label="维护模式">
              <el-switch v-model="siteSettings.site_maintenance" />
              <span class="form-tip">开启后网站将显示维护页面</span>
            </el-form-item>
            <el-form-item label="维护提示">
              <el-input
                v-model="siteSettings.site_maintenance_message"
                type="textarea"
                :rows="2"
                placeholder="请输入维护提示信息"
                :disabled="!siteSettings.site_maintenance"
              />
            </el-form-item>
            <el-divider content-position="left">
              SEO与备案
            </el-divider>
            <el-form-item label="版权信息">
              <el-input v-model="siteSettings.site_copyright" placeholder="请输入版权信息" />
            </el-form-item>
            <el-form-item label="ICP备案号">
              <el-input v-model="siteSettings.site_icp" placeholder="请输入ICP备案号" />
            </el-form-item>
            <el-form-item>
              <el-button type="primary" @click="saveSiteSettings">
                保存设置
              </el-button>
            </el-form-item>
          </el-form>
        </el-card>
      </el-tab-pane>

      <!-- 登录注册 -->
      <el-tab-pane label="登录注册" name="auth">
        <el-card class="settings-card">
          <el-form :model="authSettings" label-width="140px">
            <el-form-item label="允许邮箱登录">
              <el-switch v-model="authSettings.auth_email_login" />
              <span class="form-tip">允许用户使用邮箱密码登录</span>
            </el-form-item>
            <el-form-item label="允许用户名登录">
              <el-switch v-model="authSettings.auth_username_login" />
              <span class="form-tip">允许用户使用用户名登录</span>
            </el-form-item>
            <el-divider />
            <el-form-item label="开放注册">
              <el-switch v-model="authSettings.auth_register_enabled" />
              <span class="form-tip">允许新用户注册</span>
            </el-form-item>
            <el-form-item label="注册需邀请码">
              <el-switch v-model="authSettings.auth_require_referral" />
              <span class="form-tip">注册时必须填写邀请码</span>
            </el-form-item>
            <el-form-item>
              <el-button type="primary" @click="saveAuthSettings">
                保存设置
              </el-button>
            </el-form-item>
          </el-form>
        </el-card>
      </el-tab-pane>

      <!-- 联系方式 -->
      <el-tab-pane label="联系方式" name="contact">
        <el-card class="settings-card">
          <el-form :model="contactSettings" label-width="120px">
            <el-form-item label="客服电话">
              <el-input v-model="contactSettings.contact_phone" placeholder="请输入客服电话" />
            </el-form-item>
            <el-form-item label="客服邮箱">
              <el-input v-model="contactSettings.contact_email" placeholder="请输入客服邮箱" />
            </el-form-item>
            <el-form-item label="公司地址">
              <el-input v-model="contactSettings.contact_address" placeholder="请输入公司地址" />
            </el-form-item>
            <el-form-item label="QQ">
              <el-input v-model="contactSettings.contact_qq" placeholder="请输入QQ号码" />
            </el-form-item>
            <el-form-item label="微信">
              <el-input v-model="contactSettings.contact_wechat" placeholder="请输入微信号" />
            </el-form-item>
            <el-form-item>
              <el-button type="primary" @click="saveContactSettings">
                保存设置
              </el-button>
            </el-form-item>
          </el-form>
        </el-card>
      </el-tab-pane>

      <!-- 邮箱服务 -->
      <el-tab-pane label="邮箱服务" name="email">
        <el-card class="settings-card">
          <el-form :model="emailSettings" label-width="140px">
            <el-form-item label="启用邮箱服务">
              <el-switch v-model="emailSettings.email_enabled" />
            </el-form-item>
            <el-divider />
            <el-form-item label="SMTP服务器">
              <el-input v-model="emailSettings.email_host" placeholder="smtp.qq.com" :disabled="!emailSettings.email_enabled" />
            </el-form-item>
            <el-form-item label="端口">
              <el-input-number
                v-model="emailSettings.email_port"
                :min="1"
                :max="65535"
                :disabled="!emailSettings.email_enabled"
              />
              <span class="form-tip">465(SSL) 或 587(TLS)</span>
            </el-form-item>
            <el-form-item label="加密方式">
              <el-radio-group v-model="emailSettings.email_encryption" :disabled="!emailSettings.email_enabled">
                <el-radio value="ssl">
                  SSL
                </el-radio>
                <el-radio value="tls">
                  TLS
                </el-radio>
              </el-radio-group>
            </el-form-item>
            <el-form-item label="邮箱账号">
              <el-input v-model="emailSettings.email_username" placeholder="请输入邮箱账号" :disabled="!emailSettings.email_enabled" />
            </el-form-item>
            <el-form-item label="邮箱密码">
              <el-input
                v-model="emailSettings.email_password"
                type="password"
                placeholder="请输入邮箱密码或授权码"
                show-password
                :disabled="!emailSettings.email_enabled"
              />
              <div class="form-tip">
                QQ邮箱使用授权码，非QQ密码
              </div>
            </el-form-item>
            <el-form-item label="发件人名称">
              <el-input v-model="emailSettings.email_from_name" placeholder="请输入发件人名称" :disabled="!emailSettings.email_enabled" />
            </el-form-item>
            <el-form-item label="发件人邮箱">
              <el-input v-model="emailSettings.email_from_address" placeholder="请输入发件人邮箱" :disabled="!emailSettings.email_enabled" />
            </el-form-item>
            <el-form-item>
              <el-button type="primary" :disabled="!emailSettings.email_enabled" @click="saveEmailSettings">
                保存配置
              </el-button>
            </el-form-item>
          </el-form>
        </el-card>
      </el-tab-pane>

      <!-- 推荐设置 -->
      <el-tab-pane label="推荐设置" name="referral">
        <el-card class="settings-card">
          <el-form :model="referralSettings" label-width="140px">
            <el-form-item label="推荐奖励金额">
              <el-input-number
                v-model="referralSettings.referral_bonus"
                :min="0"
                :precision="2"
                :step="1"
              />
              <span class="form-tip">成功推荐一名用户注册后的奖励金额</span>
            </el-form-item>
            <el-form-item label="推荐自动完成">
              <el-switch v-model="referralSettings.referral_auto_complete" />
              <span class="form-tip">被推荐人注册后自动完成推荐，否则需要手动确认</span>
            </el-form-item>
            <el-form-item>
              <el-button type="primary" @click="saveReferralSettings">
                保存设置
              </el-button>
            </el-form-item>
          </el-form>
        </el-card>
      </el-tab-pane>

      <!-- 支付设置 -->
      <el-tab-pane label="支付设置" name="payment">
        <el-card class="settings-card">
          <el-form :model="paymentSettings" label-width="140px">
            <el-divider content-position="left">
              支付开关
            </el-divider>
            <el-form-item label="启用支付功能">
              <el-switch v-model="paymentSettings.payment_enabled" />
              <span class="form-tip">开启后用户可使用在线支付功能</span>
            </el-form-item>
            <el-form-item label="启用支付宝">
              <el-switch v-model="paymentSettings.payment_alipay_enabled" :disabled="!paymentSettings.payment_enabled" />
            </el-form-item>
            <el-form-item label="启用微信支付">
              <el-switch v-model="paymentSettings.payment_wechat_enabled" :disabled="!paymentSettings.payment_enabled" />
            </el-form-item>
            <el-form-item label="启用银行转账">
              <el-switch v-model="paymentSettings.payment_bank_enabled" :disabled="!paymentSettings.payment_enabled" />
            </el-form-item>

            <el-divider content-position="left">
              支付宝配置
            </el-divider>
            <el-form-item label="AppID">
              <el-input v-model="paymentSettings.alipay_app_id" placeholder="请输入支付宝AppID" :disabled="!paymentSettings.payment_enabled || !paymentSettings.payment_alipay_enabled" />
            </el-form-item>
            <el-form-item label="支付宝私钥">
              <el-input
                v-model="paymentSettings.alipay_private_key"
                type="password"
                placeholder="请输入支付宝私钥"
                show-password
                :disabled="!paymentSettings.payment_enabled || !paymentSettings.payment_alipay_enabled"
              />
            </el-form-item>
            <el-form-item label="支付宝公钥">
              <el-input
                v-model="paymentSettings.alipay_public_key"
                type="password"
                placeholder="请输入支付宝公钥"
                show-password
                :disabled="!paymentSettings.payment_enabled || !paymentSettings.payment_alipay_enabled"
              />
            </el-form-item>
            <el-form-item label="网关地址">
              <el-input v-model="paymentSettings.alipay_gateway_url" placeholder="https://openapi.alipay.com/gateway.do" :disabled="!paymentSettings.payment_enabled || !paymentSettings.payment_alipay_enabled" />
            </el-form-item>

            <el-divider content-position="left">
              微信支付配置
            </el-divider>
            <el-form-item label="AppID">
              <el-input v-model="paymentSettings.wechat_app_id" placeholder="请输入微信AppID" :disabled="!paymentSettings.payment_enabled || !paymentSettings.payment_wechat_enabled" />
            </el-form-item>
            <el-form-item label="商户号">
              <el-input v-model="paymentSettings.wechat_mch_id" placeholder="请输入微信商户号" :disabled="!paymentSettings.payment_enabled || !paymentSettings.payment_wechat_enabled" />
            </el-form-item>
            <el-form-item label="API密钥">
              <el-input
                v-model="paymentSettings.wechat_api_key"
                type="password"
                placeholder="请输入微信API密钥"
                show-password
                :disabled="!paymentSettings.payment_enabled || !paymentSettings.payment_wechat_enabled"
              />
            </el-form-item>
            <el-form-item label="证书路径">
              <el-input v-model="paymentSettings.wechat_cert_path" placeholder="请输入微信证书路径" :disabled="!paymentSettings.payment_enabled || !paymentSettings.payment_wechat_enabled" />
            </el-form-item>
            <el-form-item label="密钥路径">
              <el-input v-model="paymentSettings.wechat_key_path" placeholder="请输入微信密钥路径" :disabled="!paymentSettings.payment_enabled || !paymentSettings.payment_wechat_enabled" />
            </el-form-item>

            <el-divider content-position="left">
              银行转账信息
            </el-divider>
            <el-form-item label="开户姓名">
              <el-input v-model="paymentSettings.bank_account_name" placeholder="请输入开户姓名" :disabled="!paymentSettings.payment_enabled || !paymentSettings.payment_bank_enabled" />
            </el-form-item>
            <el-form-item label="银行账号">
              <el-input v-model="paymentSettings.bank_account_number" placeholder="请输入银行账号" :disabled="!paymentSettings.payment_enabled || !paymentSettings.payment_bank_enabled" />
            </el-form-item>
            <el-form-item label="开户银行">
              <el-input v-model="paymentSettings.bank_name" placeholder="请输入开户银行" :disabled="!paymentSettings.payment_enabled || !paymentSettings.payment_bank_enabled" />
            </el-form-item>
            <el-form-item label="支行名称">
              <el-input v-model="paymentSettings.bank_branch" placeholder="请输入支行名称" :disabled="!paymentSettings.payment_enabled || !paymentSettings.payment_bank_enabled" />
            </el-form-item>

            <el-form-item>
              <el-button type="primary" :disabled="!paymentSettings.payment_enabled" @click="savePaymentSettings">
                保存配置
              </el-button>
            </el-form-item>
          </el-form>
        </el-card>
      </el-tab-pane>

      <!-- 智能推荐设置 -->
      <el-tab-pane label="智能推荐" name="recommendation">
        <el-card class="settings-card">
          <el-form :model="recommendationSettings" label-width="140px">
            <el-form-item label="启用智能推荐">
              <el-switch v-model="recommendationSettings.recommendation_enabled" />
              <span class="form-tip">开启后显示个性化推荐内容</span>
            </el-form-item>
            <el-divider />
            <el-form-item label="推荐算法">
              <el-select v-model="recommendationSettings.recommendation_algorithm" placeholder="请选择推荐算法" :disabled="!recommendationSettings.recommendation_enabled">
                <el-option label="协同过滤 (Collaborative Filtering)" value="collaborative" />
                <el-option label="基于内容 (Content-Based)" value="content" />
                <el-option label="混合推荐 (Hybrid)" value="hybrid" />
                <el-option label="热门推荐 (Popular)" value="popular" />
              </el-select>
            </el-form-item>
            <el-form-item label="最大推荐数">
              <el-input-number
                v-model="recommendationSettings.recommendation_max_items"
                :min="1"
                :max="50"
                :disabled="!recommendationSettings.recommendation_enabled"
              />
              <span class="form-tip">每个用户最多显示的推荐数量</span>
            </el-form-item>
            <el-form-item label="缓存时间">
              <el-input-number
                v-model="recommendationSettings.recommendation_cache_ttl"
                :min="60"
                :max="86400"
                :step="60"
                :disabled="!recommendationSettings.recommendation_enabled"
              />
              <span class="form-tip">推荐结果缓存时间，单位：秒</span>
            </el-form-item>
            <el-form-item label="更新间隔">
              <el-input-number
                v-model="recommendationSettings.recommendation_update_interval"
                :min="1"
                :max="168"
                :disabled="!recommendationSettings.recommendation_enabled"
              />
              <span class="form-tip">推荐模型更新间隔，单位：小时</span>
            </el-form-item>
            <el-divider content-position="left">
              推荐权重
            </el-divider>
            <el-form-item label="最低推荐分数">
              <el-input-number
                v-model="recommendationSettings.recommendation_min_score"
                :min="0"
                :max="1"
                :precision="2"
                :step="0.1"
                :disabled="!recommendationSettings.recommendation_enabled"
              />
              <span class="form-tip">低于此分数的推荐将不会显示</span>
            </el-form-item>
            <el-form-item label="热门权重">
              <el-input-number
                v-model="recommendationSettings.recommendation_popular_weight"
                :min="0"
                :max="1"
                :precision="2"
                :step="0.1"
                :disabled="!recommendationSettings.recommendation_enabled"
              />
              <span class="form-tip">热门内容在推荐中的权重 (0-1)</span>
            </el-form-item>
            <el-form-item label="个性化权重">
              <el-input-number
                v-model="recommendationSettings.recommendation_personal_weight"
                :min="0"
                :max="1"
                :precision="2"
                :step="0.1"
                :disabled="!recommendationSettings.recommendation_enabled"
              />
              <span class="form-tip">个性化推荐在推荐中的权重 (0-1)</span>
            </el-form-item>
            <el-form-item>
              <el-button type="primary" :disabled="!recommendationSettings.recommendation_enabled" @click="saveRecommendationSettings">
                保存设置
              </el-button>
            </el-form-item>
          </el-form>
        </el-card>
      </el-tab-pane>

      <!-- 其他设置 -->
      <el-tab-pane label="其他设置" name="other">
        <el-card class="settings-card">
          <el-form :model="otherSettings" label-width="140px">
            <el-form-item label="用户注册开关">
              <el-switch v-model="otherSettings.allow_register" />
            </el-form-item>
            <el-form-item label="文章审核">
              <el-switch v-model="otherSettings.article_review" />
              <span class="form-tip">新发布的文章需要审核后才显示</span>
            </el-form-item>
            <el-form-item label="邮件通知">
              <el-switch v-model="otherSettings.email_notify" />
              <span class="form-tip">系统事件时发送邮件通知</span>
            </el-form-item>
            <el-form-item label="统计代码">
              <el-input
                v-model="otherSettings.traffic_stats_code"
                type="textarea"
                :rows="6"
                placeholder="请输入网站流量统计代码"
              />
            </el-form-item>
            <el-form-item>
              <el-button type="primary" @click="saveOtherSettings">
                保存设置
              </el-button>
            </el-form-item>
          </el-form>
        </el-card>
      </el-tab-pane>
    </el-tabs>
  </div>
</template>

<script setup>
import { reactive, ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { ElMessage } from 'element-plus'
import api from '@/api'

const route = useRoute()
const activeTab = ref('site')
const loading = ref(false)

const siteSettings = reactive({
  site_name: '',
  site_logo: '',
  site_url: '',
  site_description: '',
  site_keywords: '',
  site_language: 'zh-CN',
  site_timezone: 'Asia/Shanghai',
  site_maintenance: false,
  site_maintenance_message: '网站正在维护中，请稍后再访问',
  site_copyright: '',
  site_icp: ''
})

const authSettings = reactive({
  auth_email_login: true,
  auth_username_login: true,
  auth_register_enabled: true,
  auth_require_referral: false
})

const contactSettings = reactive({
  contact_phone: '',
  contact_email: '',
  contact_address: '',
  contact_qq: '',
  contact_wechat: ''
})

const emailSettings = reactive({
  email_enabled: false,
  email_host: 'smtp.qq.com',
  email_port: 465,
  email_encryption: 'ssl',
  email_username: '',
  email_password: '',
  email_from_name: '',
  email_from_address: ''
})

const referralSettings = reactive({
  referral_bonus: 10.00,
  referral_auto_complete: true
})

const paymentSettings = reactive({
  payment_enabled: false,
  payment_alipay_enabled: false,
  payment_wechat_enabled: false,
  payment_bank_enabled: false,
  alipay_app_id: '',
  alipay_private_key: '',
  alipay_public_key: '',
  alipay_gateway_url: 'https://openapi.alipay.com/gateway.do',
  wechat_app_id: '',
  wechat_mch_id: '',
  wechat_api_key: '',
  wechat_cert_path: '',
  wechat_key_path: '',
  wechat_gateway_url: 'https://api.mch.weixin.qq.com/pay/unifiedorder',
  bank_account_name: '',
  bank_account_number: '',
  bank_name: '',
  bank_branch: ''
})

const recommendationSettings = reactive({
  recommendation_enabled: false,
  recommendation_algorithm: 'collaborative',
  recommendation_max_items: 10,
  recommendation_cache_ttl: 3600,
  recommendation_update_interval: 24,
  recommendation_min_score: 0.6,
  recommendation_popular_weight: 0.3,
  recommendation_personal_weight: 0.7
})

const otherSettings = reactive({
  allow_register: true,
  article_review: false,
  email_notify: true,
  traffic_stats_code: ''
})

const loadSettings = async () => {
  loading.value = true
  try {
    const res = await api.settings.list()
    if (res.code === 200 && res.data) {
      const settings = Array.isArray(res.data) ? res.data : []
      settings.forEach(item => {
        const val = item.typed_value !== undefined ? item.typed_value : item.value
        switch (item.key) {
          case 'site_name': siteSettings.site_name = val || ''; break
          case 'site_logo': siteSettings.site_logo = val || ''; break
          case 'site_url': siteSettings.site_url = val || ''; break
          case 'site_description': siteSettings.site_description = val || ''; break
          case 'site_keywords': siteSettings.site_keywords = val || ''; break
          case 'site_language': siteSettings.site_language = val || 'zh-CN'; break
          case 'site_timezone': siteSettings.site_timezone = val || 'Asia/Shanghai'; break
          case 'site_maintenance': siteSettings.site_maintenance = val === true || val === 'true'; break
          case 'site_maintenance_message': siteSettings.site_maintenance_message = val || ''; break
          case 'site_copyright': siteSettings.site_copyright = val || ''; break
          case 'site_icp': siteSettings.site_icp = val || ''; break
          case 'auth_email_login': authSettings.auth_email_login = val !== false && val !== 'false'; break
          case 'auth_username_login': authSettings.auth_username_login = val !== false && val !== 'false'; break
          case 'auth_register_enabled': authSettings.auth_register_enabled = val !== false && val !== 'false'; break
          case 'auth_require_referral': authSettings.auth_require_referral = val === true || val === 'true'; break
          case 'contact_phone': contactSettings.contact_phone = val || ''; break
          case 'contact_email': contactSettings.contact_email = val || ''; break
          case 'contact_address': contactSettings.contact_address = val || ''; break
          case 'contact_qq': contactSettings.contact_qq = val || ''; break
          case 'contact_wechat': contactSettings.contact_wechat = val || ''; break
          case 'email_enabled': emailSettings.email_enabled = val === true || val === 'true'; break
          case 'email_host': emailSettings.email_host = val || 'smtp.qq.com'; break
          case 'email_port': emailSettings.email_port = parseInt(val) || 465; break
          case 'email_encryption': emailSettings.email_encryption = val || 'ssl'; break
          case 'email_username': emailSettings.email_username = val || ''; break
          case 'email_password': emailSettings.email_password = ''; break // 密码不从后端回显
          case 'email_from_name': emailSettings.email_from_name = val || ''; break
          case 'email_from_address': emailSettings.email_from_address = val || ''; break
          case 'referral_bonus': referralSettings.referral_bonus = parseFloat(val) || 10.00; break
          case 'referral_auto_complete': referralSettings.referral_auto_complete = val !== false && val !== 'false'; break
          case 'payment_enabled': paymentSettings.payment_enabled = val === true || val === 'true'; break
          case 'payment_alipay_enabled': paymentSettings.payment_alipay_enabled = val === true || val === 'true'; break
          case 'payment_wechat_enabled': paymentSettings.payment_wechat_enabled = val === true || val === 'true'; break
          case 'payment_bank_enabled': paymentSettings.payment_bank_enabled = val === true || val === 'true'; break
          case 'alipay_app_id': paymentSettings.alipay_app_id = val || ''; break
          case 'alipay_private_key': paymentSettings.alipay_private_key = ''; break
          case 'alipay_public_key': paymentSettings.alipay_public_key = ''; break
          case 'alipay_gateway_url': paymentSettings.alipay_gateway_url = val || 'https://openapi.alipay.com/gateway.do'; break
          case 'wechat_app_id': paymentSettings.wechat_app_id = val || ''; break
          case 'wechat_mch_id': paymentSettings.wechat_mch_id = val || ''; break
          case 'wechat_api_key': paymentSettings.wechat_api_key = ''; break
          case 'wechat_cert_path': paymentSettings.wechat_cert_path = val || ''; break
          case 'wechat_key_path': paymentSettings.wechat_key_path = val || ''; break
          case 'wechat_gateway_url': paymentSettings.wechat_gateway_url = val || 'https://api.mch.weixin.qq.com/pay/unifiedorder'; break
          case 'bank_account_name': paymentSettings.bank_account_name = val || ''; break
          case 'bank_account_number': paymentSettings.bank_account_number = val || ''; break
          case 'bank_name': paymentSettings.bank_name = val || ''; break
          case 'bank_branch': paymentSettings.bank_branch = val || ''; break
          case 'recommendation_enabled': recommendationSettings.recommendation_enabled = val === true || val === 'true'; break
          case 'recommendation_algorithm': recommendationSettings.recommendation_algorithm = val || 'collaborative'; break
          case 'recommendation_max_items': recommendationSettings.recommendation_max_items = parseInt(val) || 10; break
          case 'recommendation_cache_ttl': recommendationSettings.recommendation_cache_ttl = parseInt(val) || 3600; break
          case 'recommendation_update_interval': recommendationSettings.recommendation_update_interval = parseInt(val) || 24; break
          case 'recommendation_min_score': recommendationSettings.recommendation_min_score = parseFloat(val) || 0.6; break
          case 'recommendation_popular_weight': recommendationSettings.recommendation_popular_weight = parseFloat(val) || 0.3; break
          case 'recommendation_personal_weight': recommendationSettings.recommendation_personal_weight = parseFloat(val) || 0.7; break
          case 'allow_register': otherSettings.allow_register = val !== false && val !== 'false'; break
          case 'article_review': otherSettings.article_review = val === true || val === 'true'; break
          case 'email_notify': otherSettings.email_notify = val !== false && val !== 'false'; break
          case 'traffic_stats_code': otherSettings.traffic_stats_code = val || ''; break
        }
      })
    }
  } catch (e) {
    console.error('加载设置失败', e)
  } finally {
    loading.value = false
  }
}

const saveSiteSettings = async () => {
  try {
    const res = await api.settings.save({ ...siteSettings })
    if (res.code === 200) {
      ElMessage.success('网站设置保存成功')
    } else {
      ElMessage.error(res.message || '保存失败')
    }
  } catch (e) {
    ElMessage.error('保存失败')
  }
}

const saveAuthSettings = async () => {
  try {
    const res = await api.settings.save({ ...authSettings })
    if (res.code === 200) {
      ElMessage.success('登录注册设置保存成功')
    } else {
      ElMessage.error(res.message || '保存失败')
    }
  } catch (e) {
    ElMessage.error('保存失败')
  }
}

const saveContactSettings = async () => {
  try {
    const res = await api.settings.save({ ...contactSettings })
    if (res.code === 200) {
      ElMessage.success('联系方式保存成功')
    } else {
      ElMessage.error(res.message || '保存失败')
    }
  } catch (e) {
    ElMessage.error('保存失败')
  }
}

const saveEmailSettings = async () => {
  try {
    const data = { ...emailSettings }
    // 如果密码为空则不提交密码字段（保留原密码）
    if (!data.email_password) {
      delete data.email_password
    }
    const res = await api.settings.save(data)
    if (res.code === 200) {
      ElMessage.success('邮箱配置保存成功')
    } else {
      ElMessage.error(res.message || '保存失败')
    }
  } catch (e) {
    ElMessage.error('保存失败')
  }
}

const saveReferralSettings = async () => {
  try {
    const res = await api.settings.save({ ...referralSettings })
    if (res.code === 200) {
      ElMessage.success('推荐设置保存成功')
    } else {
      ElMessage.error(res.message || '保存失败')
    }
  } catch (e) {
    ElMessage.error('保存失败')
  }
}

const savePaymentSettings = async () => {
  try {
    const data = { ...paymentSettings }
    if (!data.alipay_private_key) delete data.alipay_private_key
    if (!data.alipay_public_key) delete data.alipay_public_key
    if (!data.wechat_api_key) delete data.wechat_api_key
    const res = await api.settings.save(data)
    if (res.code === 200) {
      ElMessage.success('支付配置保存成功')
    } else {
      ElMessage.error(res.message || '保存失败')
    }
  } catch (e) {
    ElMessage.error('保存失败')
  }
}

const saveRecommendationSettings = async () => {
  try {
    const res = await api.settings.save({ ...recommendationSettings })
    if (res.code === 200) {
      ElMessage.success('智能推荐设置保存成功')
    } else {
      ElMessage.error(res.message || '保存失败')
    }
  } catch (e) {
    ElMessage.error('保存失败')
  }
}

const saveOtherSettings = async () => {
  try {
    const res = await api.settings.save({ ...otherSettings })
    if (res.code === 200) {
      ElMessage.success('其他设置保存成功')
    } else {
      ElMessage.error(res.message || '保存失败')
    }
  } catch (e) {
    ElMessage.error('保存失败')
  }
}

onMounted(() => {
  if (route.query.group) {
    activeTab.value = route.query.group
  }
  loadSettings()
})
</script>

<style scoped>
.settings-page {
  padding: 20px;
}

.settings-tabs {
  background: #fff;
  padding: 20px;
  border-radius: 8px;
}

.settings-card {
  max-width: 800px;
}

.form-tip {
  margin-left: 12px;
  font-size: 12px;
  color: #86909C;
}

:deep(.el-divider) {
  margin: 20px 0;
}
</style>
