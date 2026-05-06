<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            // 网站设置 - site 组
            ['key' => 'site_name', 'value' => 'Laravel Vue Template', 'type' => 'string', 'group' => 'site', 'title' => '网站名称', 'sort' => 1],
            ['key' => 'site_logo', 'value' => '', 'type' => 'string', 'group' => 'site', 'title' => '网站Logo', 'sort' => 2],
            ['key' => 'site_description', 'value' => '基于 Laravel + Vue 的通用后台管理系统模板', 'type' => 'string', 'group' => 'site', 'title' => '网站描述', 'sort' => 3],
            ['key' => 'site_keywords', 'value' => 'Laravel,Vue,后台管理', 'type' => 'string', 'group' => 'site', 'title' => '关键词', 'sort' => 4],
            ['key' => 'site_copyright', 'value' => '© 2024 All Rights Reserved', 'type' => 'string', 'group' => 'site', 'title' => '版权信息', 'sort' => 5],
            ['key' => 'site_icp', 'value' => '', 'type' => 'string', 'group' => 'site', 'title' => 'ICP备案号', 'sort' => 6],

            // 登录注册 - auth 组
            ['key' => 'auth_email_login', 'value' => 'true', 'type' => 'boolean', 'group' => 'auth', 'title' => '允许邮箱登录', 'sort' => 1],
            ['key' => 'auth_username_login', 'value' => 'true', 'type' => 'boolean', 'group' => 'auth', 'title' => '允许用户名登录', 'sort' => 2],
            ['key' => 'auth_register_enabled', 'value' => 'true', 'type' => 'boolean', 'group' => 'auth', 'title' => '开放注册', 'sort' => 3],
            ['key' => 'auth_require_referral', 'value' => 'false', 'type' => 'boolean', 'group' => 'auth', 'title' => '注册需邀请码', 'sort' => 4],

            // 联系方式 - contact 组
            ['key' => 'contact_phone', 'value' => '', 'type' => 'string', 'group' => 'contact', 'title' => '客服电话', 'sort' => 1],
            ['key' => 'contact_email', 'value' => '', 'type' => 'string', 'group' => 'contact', 'title' => '客服邮箱', 'sort' => 2],
            ['key' => 'contact_address', 'value' => '', 'type' => 'string', 'group' => 'contact', 'title' => '公司地址', 'sort' => 3],
            ['key' => 'contact_qq', 'value' => '', 'type' => 'string', 'group' => 'contact', 'title' => 'QQ', 'sort' => 4],
            ['key' => 'contact_wechat', 'value' => '', 'type' => 'string', 'group' => 'contact', 'title' => '微信', 'sort' => 5],

            // 邮箱服务 - email 组
            ['key' => 'email_enabled', 'value' => 'false', 'type' => 'boolean', 'group' => 'email', 'title' => '启用邮箱服务', 'sort' => 1],
            ['key' => 'email_host', 'value' => 'smtp.qq.com', 'type' => 'string', 'group' => 'email', 'title' => 'SMTP服务器', 'sort' => 2],
            ['key' => 'email_port', 'value' => '465', 'type' => 'string', 'group' => 'email', 'title' => '端口', 'sort' => 3],
            ['key' => 'email_encryption', 'value' => 'ssl', 'type' => 'string', 'group' => 'email', 'title' => '加密方式', 'sort' => 4],
            ['key' => 'email_username', 'value' => '', 'type' => 'string', 'group' => 'email', 'title' => '邮箱账号', 'sort' => 5],
            ['key' => 'email_password', 'value' => '', 'type' => 'password', 'group' => 'email', 'title' => '邮箱密码', 'sort' => 6],
            ['key' => 'email_from_name', 'value' => '', 'type' => 'string', 'group' => 'email', 'title' => '发件人名称', 'sort' => 7],
            ['key' => 'email_from_address', 'value' => '', 'type' => 'string', 'group' => 'email', 'title' => '发件人邮箱', 'sort' => 8],

            // 推荐设置 - referral 组
            ['key' => 'referral_bonus', 'value' => '10', 'type' => 'number', 'group' => 'referral', 'title' => '推荐奖励金额', 'sort' => 1],
            ['key' => 'referral_auto_complete', 'value' => 'true', 'type' => 'boolean', 'group' => 'referral', 'title' => '推荐自动完成', 'sort' => 2],

            // 其他设置 - other 组
            ['key' => 'allow_register', 'value' => 'true', 'type' => 'boolean', 'group' => 'other', 'title' => '用户注册开关', 'sort' => 1],
            ['key' => 'article_review', 'value' => 'false', 'type' => 'boolean', 'group' => 'other', 'title' => '文章审核', 'sort' => 2],
            ['key' => 'email_notify', 'value' => 'true', 'type' => 'boolean', 'group' => 'other', 'title' => '邮件通知', 'sort' => 3],
            ['key' => 'traffic_stats_code', 'value' => '', 'type' => 'string', 'group' => 'other', 'title' => '统计代码', 'sort' => 4],
            ['key' => 'site_url', 'value' => '', 'type' => 'string', 'group' => 'site', 'title' => '网站URL', 'sort' => 7],
            ['key' => 'site_language', 'value' => 'zh-CN', 'type' => 'string', 'group' => 'site', 'title' => '默认语言', 'sort' => 8],
            ['key' => 'site_timezone', 'value' => 'Asia/Shanghai', 'type' => 'string', 'group' => 'site', 'title' => '时区', 'sort' => 9],
            ['key' => 'site_maintenance', 'value' => 'false', 'type' => 'boolean', 'group' => 'site', 'title' => '维护模式', 'sort' => 10],
            ['key' => 'site_maintenance_message', 'value' => '网站正在维护中，请稍后再访问', 'type' => 'string', 'group' => 'site', 'title' => '维护提示信息', 'sort' => 11],

            ['key' => 'payment_enabled', 'value' => 'false', 'type' => 'boolean', 'group' => 'payment', 'title' => '启用支付功能', 'sort' => 1],
            ['key' => 'payment_alipay_enabled', 'value' => 'false', 'type' => 'boolean', 'group' => 'payment', 'title' => '启用支付宝', 'sort' => 2],
            ['key' => 'payment_wechat_enabled', 'value' => 'false', 'type' => 'boolean', 'group' => 'payment', 'title' => '启用微信支付', 'sort' => 3],
            ['key' => 'payment_bank_enabled', 'value' => 'false', 'type' => 'boolean', 'group' => 'payment', 'title' => '启用银行转账', 'sort' => 4],
            ['key' => 'alipay_app_id', 'value' => '', 'type' => 'string', 'group' => 'payment', 'title' => '支付宝AppID', 'sort' => 5],
            ['key' => 'alipay_private_key', 'value' => '', 'type' => 'password', 'group' => 'payment', 'title' => '支付宝私钥', 'sort' => 6],
            ['key' => 'alipay_public_key', 'value' => '', 'type' => 'password', 'group' => 'payment', 'title' => '支付宝公钥', 'sort' => 7],
            ['key' => 'alipay_gateway_url', 'value' => 'https://openapi.alipay.com/gateway.do', 'type' => 'string', 'group' => 'payment', 'title' => '支付宝网关地址', 'sort' => 8],
            ['key' => 'wechat_app_id', 'value' => '', 'type' => 'string', 'group' => 'payment', 'title' => '微信AppID', 'sort' => 9],
            ['key' => 'wechat_mch_id', 'value' => '', 'type' => 'string', 'group' => 'payment', 'title' => '微信商户号', 'sort' => 10],
            ['key' => 'wechat_api_key', 'value' => '', 'type' => 'password', 'group' => 'payment', 'title' => '微信API密钥', 'sort' => 11],
            ['key' => 'wechat_cert_path', 'value' => '', 'type' => 'string', 'group' => 'payment', 'title' => '微信证书路径', 'sort' => 12],
            ['key' => 'wechat_key_path', 'value' => '', 'type' => 'string', 'group' => 'payment', 'title' => '微信密钥路径', 'sort' => 13],
            ['key' => 'wechat_gateway_url', 'value' => 'https://api.mch.weixin.qq.com/pay/unifiedorder', 'type' => 'string', 'group' => 'payment', 'title' => '微信支付网关', 'sort' => 14],
            ['key' => 'bank_account_name', 'value' => '', 'type' => 'string', 'group' => 'payment', 'title' => '银行户名', 'sort' => 15],
            ['key' => 'bank_account_number', 'value' => '', 'type' => 'string', 'group' => 'payment', 'title' => '银行账号', 'sort' => 16],
            ['key' => 'bank_name', 'value' => '', 'type' => 'string', 'group' => 'payment', 'title' => '开户银行', 'sort' => 17],
            ['key' => 'bank_branch', 'value' => '', 'type' => 'string', 'group' => 'payment', 'title' => '支行名称', 'sort' => 18],

            ['key' => 'recommendation_enabled', 'value' => 'false', 'type' => 'boolean', 'group' => 'recommendation', 'title' => '启用智能推荐', 'sort' => 1],
            ['key' => 'recommendation_algorithm', 'value' => 'collaborative', 'type' => 'string', 'group' => 'recommendation', 'title' => '推荐算法', 'sort' => 2],
            ['key' => 'recommendation_max_items', 'value' => '10', 'type' => 'number', 'group' => 'recommendation', 'title' => '最大推荐数量', 'sort' => 3],
            ['key' => 'recommendation_cache_ttl', 'value' => '3600', 'type' => 'number', 'group' => 'recommendation', 'title' => '缓存时间(秒)', 'sort' => 4],
            ['key' => 'recommendation_update_interval', 'value' => '24', 'type' => 'number', 'group' => 'recommendation', 'title' => '更新间隔(小时)', 'sort' => 5],
            ['key' => 'recommendation_min_score', 'value' => '0.6', 'type' => 'number', 'group' => 'recommendation', 'title' => '最低推荐分数', 'sort' => 6],
            ['key' => 'recommendation_popular_weight', 'value' => '0.3', 'type' => 'number', 'group' => 'recommendation', 'title' => '热门权重', 'sort' => 7],
            ['key' => 'recommendation_personal_weight', 'value' => '0.7', 'type' => 'number', 'group' => 'recommendation', 'title' => '个性化权重', 'sort' => 8],

            // Redis 缓存配置 - redis 组
            ['key' => 'redis_enabled', 'value' => 'false', 'type' => 'boolean', 'group' => 'redis', 'title' => '启用 Redis 缓存', 'sort' => 1],
            ['key' => 'redis_host', 'value' => '127.0.0.1', 'type' => 'string', 'group' => 'redis', 'title' => 'Redis 主机', 'sort' => 2],
            ['key' => 'redis_port', 'value' => '6379', 'type' => 'string', 'group' => 'redis', 'title' => 'Redis 端口', 'sort' => 3],
            ['key' => 'redis_password', 'value' => '', 'type' => 'password', 'group' => 'redis', 'title' => 'Redis 密码', 'sort' => 4],
            ['key' => 'redis_database', 'value' => '0', 'type' => 'number', 'group' => 'redis', 'title' => 'Redis 数据库', 'sort' => 5],
            ['key' => 'redis_prefix', 'value' => 'luma_', 'type' => 'string', 'group' => 'redis', 'title' => '缓存前缀', 'sort' => 6],
            ['key' => 'redis_cache_ttl', 'value' => '3600', 'type' => 'number', 'group' => 'redis', 'title' => '默认缓存时间(秒)', 'sort' => 7],
        ];

        foreach ($settings as $setting) {
            Setting::firstOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}
