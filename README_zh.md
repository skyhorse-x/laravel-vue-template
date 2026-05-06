# Luma - Laravel 13 + Vue 3 模板项目

[English](./README.md) | 中文 - 当前语言

基于 Laravel 13、Vue 3 和 Vite 构建的全栈模板项目，包含后台管理、前台用户和会员中心。

## 技术栈

| 层级 | 技术 | 版本 |
|------|------|------|
| 后端 | Laravel 13 / PHP | ^8.3 |
| 前端 | Vue 3 / Vite | ^3.5 / ^6.0 |
| UI 组件库 | Element Plus | ^2.13 |
| 状态管理 | Pinia | ^3.0 |
| 路由 | Vue Router | ^5.0 |
| 认证 | JWT (firebase/php-jwt) | ^7.0 |
| 缓存 | Predis (Redis) | ^3.4 |
| API 文档 | L5-Swagger | ^11.0 |

## 功能模块

### 用户模块
- 用户注册和登录
- 密码找回（邮箱验证）
- 用户信息管理
- 推荐奖励系统

### 管理员模块
- 管理员登录
- 角色权限管理（RBAC）
- 管理员增删改查
- 操作日志记录

### 内容管理
- 文章管理（支持分类）
- 菜单管理

### 财务管理
- 财务记录管理（充值/提现）
- 收入支出统计

### 会员中心
- 个人资料管理
- 密码修改
- 财务记录查看
- 推荐推广系统
- 通知消息

### 系统功能
- IP 黑名单
- 网站设置管理
- 定时任务
- 队列任务

## 项目结构

```
laravel-vue-template/
├── app/
│   ├── Console/
│   │   └── Commands/
│   │       ├── JwtSecretCommand.php     # 生成 JWT 密钥
│   │       └── ScheduledTasks.php        # 定时任务
│   ├── Helpers/
│   │   └── ApiResponse.php              # API 响应辅助类
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/                   # 管理员控制器
│   │   │   │   ├── AdminController.php
│   │   │   │   ├── ArticleController.php
│   │   │   │   ├── FinancialController.php
│   │   │   │   ├── IpBlacklistController.php
│   │   │   │   ├── MenuController.php
│   │   │   │   ├── NotificationController.php
│   │   │   │   ├── OperationLogController.php
│   │   │   │   ├── RoleController.php
│   │   │   │   ├── SettingController.php
│   │   │   │   └── UserController.php
│   │   │   ├── Auth/                    # 认证控制器
│   │   │   │   ├── AdminAuthController.php
│   │   │   │   └── UserAuthController.php
│   │   │   ├── Front/                   # 前台公开接口
│   │   │   │   ├── ArticleController.php
│   │   │   │   ├── FinancialController.php
│   │   │   │   ├── ReferralController.php
│   │   │   │   ├── SettingController.php
│   │   │   │   └── SitemapController.php
│   │   │   ├── User/                    # 会员中心控制器
│   │   │   │   ├── ArticleController.php
│   │   │   │   ├── NotificationController.php
│   │   │   │   └── UserController.php
│   │   │   └── Controller.php
│   │   ├── Middleware/
│   │   │   ├── AdminAuth.php            # 管理员 JWT 认证
│   │   │   ├── AdminPermission.php       # 权限检查中间件
│   │   │   └── ApiAuth.php              # 用户 JWT 认证
│   │   ├── Requests/                    # 表单请求验证
│   │   └── Resources/                    # API 资源转换
│   ├── Jobs/                             # 队列任务
│   │   ├── CleanOldLogsJob.php
│   │   ├── SendBulkNotificationJob.php
│   │   └── SendNotificationJob.php
│   ├── Models/                           # Eloquent 模型
│   │   ├── Admin.php
│   │   ├── Article.php
│   │   ├── FinancialRecord.php
│   │   ├── IpBlacklist.php
│   │   ├── Menu.php
│   │   ├── Notification.php
│   │   ├── NotificationTemplate.php
│   │   ├── OperationLog.php
│   │   ├── Permission.php
│   │   ├── Referral.php
│   │   ├── Role.php
│   │   ├── Setting.php
│   │   ├── User.php
│   │   └── UserExtend.php
│   └── Services/
│       ├── EmailService.php
│       └── JwtService.php
├── config/                               # Laravel 配置
├── database/
│   ├── factories/                        # 模型工厂
│   ├── migrations/                       # 数据库迁移
│   └── seeders/                          # 数据填充
│       ├── AdminSeeder.php
│       ├── MenuSeeder.php
│       ├── PermissionSeeder.php
│       ├── RoleSeeder.php
│       ├── SettingSeeder.php
│       └── UserSeeder.php
├── routes/
│   ├── api.php                           # API 路由
│   ├── console.php                       # 控制台路由
│   └── web.php                           # Web 路由
└── web/                                  # Vue 3 前端
    └── src/
        ├── api/                          # API 客户端封装
        ├── assets/                        # 静态资源
        ├── components/                    # Vue 组件
        ├── i18n/                         # 国际化
        ├── router/                        # Vue Router 配置
        ├── stores/                        # Pinia 状态管理
        │   ├── admin.js
        │   └── user.js
        ├── views/
        │   ├── admin/                     # 后台管理页面
        │   │   ├── Admins.vue
        │   │   ├── Articles.vue
        │   │   ├── Dashboard.vue
        │   │   ├── Financial.vue
        │   │   ├── IpBlacklist.vue
        │   │   ├── Layout.vue
        │   │   ├── Login.vue
        │   │   ├── Menus.vue
        │   │   ├── Notifications.vue
        │   │   ├── OperationLogs.vue
        │   │   ├── Profile.vue
        │   │   ├── Roles.vue
        │   │   ├── Settings.vue
        │   │   └── Users.vue
        │   ├── front/                     # 前台公开页面
        │   │   ├── ArticleDetail.vue
        │   │   ├── Articles.vue
        │   │   ├── ForgotPassword.vue
        │   │   ├── Home.vue
        │   │   ├── Login.vue
        │   │   ├── Register.vue
        │   │   └── ResetPassword.vue
        │   └── member/                    # 会员中心页面
        │       ├── Articles.vue
        │       ├── ChangePassword.vue
        │       ├── Financial.vue
        │       ├── Home.vue
        │       ├── Layout.vue
        │       ├── Notifications.vue
        │       ├── Profile.vue
        │       └── Referral.vue
        ├── App.vue
        └── main.js
```

## 数据库模型

| 模型 | 说明 |
|------|------|
| `User` | 普通用户账户 |
| `Admin` | 管理员账户 |
| `Role` | RBAC 角色 |
| `Permission` | RBAC 权限 |
| `Article` | 文章内容 |
| `Menu` | 导航菜单 |
| `FinancialRecord` | 财务流水记录 |
| `Referral` | 推荐关系 |
| `UserExtend` | 用户扩展信息 |
| `Notification` | 用户通知 |
| `NotificationTemplate` | 通知模板 |
| `OperationLog` | 管理员操作日志 |
| `IpBlacklist` | IP 黑名单 |
| `Setting` | 系统配置 |

## API 路由

### 公开接口（无需认证）

#### 用户认证
| 方法 | 路由 | 说明 |
|------|------|------|
| POST | `/api/auth/register` | 用户注册 |
| POST | `/api/auth/login` | 用户登录 |
| POST | `/api/auth/forgot-password` | 忘记密码 |
| POST | `/api/auth/reset-password` | 重置密码 |

#### 前台接口
| 方法 | 路由 | 说明 |
|------|------|------|
| GET | `/api/front/articles` | 文章列表 |
| GET | `/api/front/articles/{id}` | 文章详情 |
| GET | `/api/front/articles/categories` | 获取文章分类 |
| GET | `/api/front/settings` | 获取所有配置 |
| GET | `/api/front/settings/{key}` | 获取指定配置 |
| GET | `/api/front/referral/info/{code}` | 获取推荐人信息 |
| GET | `/api/front/sitemap.xml` | 网站地图 |

#### 管理员认证
| 方法 | 路由 | 说明 |
|------|------|------|
| POST | `/api/admin/login` | 管理员登录 |
| POST | `/api/admin/forgot-password` | 管理员忘记密码 |
| POST | `/api/admin/reset-password` | 管理员重置密码 |

### 已认证用户接口（需要用户 JWT）

| 方法 | 路由 | 说明 |
|------|------|------|
| GET | `/api/auth/me` | 获取当前用户信息 |
| POST | `/api/auth/logout` | 退出登录 |
| PUT | `/api/auth/profile` | 更新个人资料 |
| PUT | `/api/auth/change-password` | 修改密码 |
| GET | `/api/financial` | 财务记录列表 |
| GET | `/api/financial/{id}` | 财务记录详情 |
| POST | `/api/financial/deposit` | 申请充值 |
| POST | `/api/financial/withdraw` | 申请提现 |
| GET | `/api/referral` | 获取推荐码 |
| GET | `/api/user/balance` | 获取用户余额 |
| GET | `/api/user/articles` | 用户的文章列表 |
| GET | `/api/user/articles/{id}` | 用户文章详情 |
| POST | `/api/user/articles` | 创建文章 |
| PUT | `/api/user/articles/{id}` | 更新文章 |
| DELETE | `/api/user/articles/{id}` | 删除文章 |
| GET | `/api/user/notifications` | 通知列表 |
| GET | `/api/user/notifications/{id}` | 通知详情 |
| POST | `/api/user/notifications/{id}/read` | 标记已读 |
| POST | `/api/user/notifications/read-all` | 全部标记已读 |
| DELETE | `/api/user/notifications/{id}` | 删除通知 |

### 管理员接口（需要管理员 JWT）

#### 个人信息
| 方法 | 路由 | 说明 |
|------|------|------|
| GET | `/api/admin/me` | 获取当前管理员 |
| POST | `/api/admin/logout` | 退出登录 |
| PUT | `/api/admin/profile` | 更新管理员资料 |
| PUT | `/api/admin/change-password` | 修改密码 |

#### 用户管理
| 方法 | 路由 | 说明 |
|------|------|------|
| GET | `/api/admin/users` | 用户列表 |
| GET | `/api/admin/users/{id}` | 用户详情 |
| POST | `/api/admin/users` | 创建用户 |
| PUT | `/api/admin/users/{id}` | 更新用户 |
| DELETE | `/api/admin/users/{id}` | 删除用户 |
| DELETE | `/api/admin/users/batch` | 批量删除用户 |

#### 管理员管理
| 方法 | 路由 | 说明 |
|------|------|------|
| GET | `/api/admin/admins` | 管理员列表 |
| GET | `/api/admin/admins/{id}` | 管理员详情 |
| POST | `/api/admin/admins` | 创建管理员 |
| PUT | `/api/admin/admins/{id}` | 更新管理员 |
| DELETE | `/api/admin/admins/{id}` | 删除管理员 |

#### 角色与权限
| 方法 | 路由 | 说明 |
|------|------|------|
| GET | `/api/admin/roles` | 角色列表 |
| GET | `/api/admin/roles/{id}` | 角色详情 |
| POST | `/api/admin/roles` | 创建角色 |
| PUT | `/api/admin/roles/{id}` | 更新角色 |
| DELETE | `/api/admin/roles/{id}` | 删除角色 |
| GET | `/api/admin/permissions` | 获取所有权限 |

#### 文章管理
| 方法 | 路由 | 说明 |
|------|------|------|
| GET | `/api/admin/articles` | 文章列表 |
| GET | `/api/admin/articles/{id}` | 文章详情 |
| POST | `/api/admin/articles` | 创建文章 |
| PUT | `/api/admin/articles/{id}` | 更新文章 |
| DELETE | `/api/admin/articles/{id}` | 删除文章 |

#### 菜单管理
| 方法 | 路由 | 说明 |
|------|------|------|
| GET | `/api/admin/menus` | 菜单列表（树形） |
| GET | `/api/admin/menus/all` | 所有菜单 |
| GET | `/api/admin/menus/{id}` | 菜单详情 |
| POST | `/api/admin/menus` | 创建菜单 |
| PUT | `/api/admin/menus/{id}` | 更新菜单 |
| DELETE | `/api/admin/menus/{id}` | 删除菜单 |

#### 财务管理
| 方法 | 路由 | 说明 |
|------|------|------|
| GET | `/api/admin/financial/statistics` | 财务统计 |
| GET | `/api/admin/financial` | 财务记录列表 |
| GET | `/api/admin/financial/{id}` | 财务记录详情 |
| POST | `/api/admin/financial` | 创建财务记录 |

#### 通知管理
| 方法 | 路由 | 说明 |
|------|------|------|
| GET | `/api/admin/notifications` | 通知列表 |
| POST | `/api/admin/notifications` | 发送通知 |
| DELETE | `/api/admin/notifications/{id}` | 删除通知 |
| DELETE | `/api/admin/notifications/batch` | 批量删除 |

#### 操作日志
| 方法 | 路由 | 说明 |
|------|------|------|
| GET | `/api/admin/operation-logs` | 日志列表 |
| GET | `/api/admin/operation-logs/modules` | 模块列表 |
| GET | `/api/admin/operation-logs/{id}` | 日志详情 |
| DELETE | `/api/admin/operation-logs/{id}` | 删除日志 |
| DELETE | `/api/admin/operation-logs` | 清空所有日志 |

#### IP 黑名单
| 方法 | 路由 | 说明 |
|------|------|------|
| GET | `/api/admin/ip-blacklist` | 黑名单列表 |
| POST | `/api/admin/ip-blacklist` | 添加 IP |
| DELETE | `/api/admin/ip-blacklist/{id}` | 移除 IP |
| DELETE | `/api/admin/ip-blacklist/batch` | 批量移除 |

#### 系统设置
| 方法 | 路由 | 说明 |
|------|------|------|
| GET | `/api/admin/settings` | 所有设置 |
| POST | `/api/admin/settings` | 创建设置项 |
| GET | `/api/admin/settings/{key}` | 获取设置 |
| PUT | `/api/admin/settings/{key}` | 更新设置 |
| GET | `/api/admin/settings/group/{group}` | 按分组获取 |
| PUT | `/api/admin/settings/group/{group}` | 按分组更新 |

## 环境要求

- PHP 8.3+
- Composer 2.x
- MySQL 8.0+ / PostgreSQL / SQLite
- Redis 7.0+
- Node.js 18+
- npm / yarn / pnpm

## 安装部署

### 1. 克隆项目

```bash
git clone <仓库地址>
cd laravel-vue-template
```

### 2. 后端安装

```bash
# 安装 PHP 依赖
composer install

# 复制环境配置文件
cp .env.example .env

# 生成应用密钥
php artisan key:generate

# 生成 JWT 密钥
php artisan jwt:secret

# 运行数据库迁移
php artisan migrate

# 填充初始数据（创建默认管理员）
php artisan db:seed
```

### 3. 前端安装

```bash
cd web

# 安装 Node.js 依赖
npm install

# 启动开发服务器
npm run dev
```

### 4. 启动开发服务

在项目根目录执行：

```bash
# 使用 npm 一键启动（同时启动多个服务）
npm run dev

# 或手动启动
php artisan serve --port=8000
# 前端访问：http://localhost:5173
# 后端 API：http://localhost:8000
```

## 默认管理员账号

数据填充后可使用以下账号登录后台：

- **邮箱**: admin@example.com
- **密码**: password

## 环境配置

编辑 `.env` 文件（其他配置通过后台网站设置管理）：

```env
APP_NAME=Luma
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost

# 数据库配置
DB_CONNECTION=sqlite
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=root
DB_PASSWORD=

# 队列和缓存使用数据库驱动
SESSION_DRIVER=database
SESSION_LIFETIME=120
QUEUE_CONNECTION=database
CACHE_STORE=database

# JWT 配置（运行 php artisan jwt:secret 生成）
JWT_SECRET=
JWT_EXPIRE=7200
```

### 后台可配置项

通过 **后台 -> 系统设置** 可管理以下配置：

| 配置组 | 说明 |
|--------|------|
| site | 网站名称、Logo、描述、关键词、版权、ICP备案等 |
| auth | 登录注册设置（邮箱/用户名登录、开放注册、邀请码） |
| contact | 联系方式（电话、邮箱、地址、QQ、微信） |
| email | 邮件服务（SMTP服务器、端口、加密方式、账号密码） |
| redis | Redis 缓存（主机、端口、密码、数据库、缓存前缀） |
| referral | 推荐奖励设置 |
| payment | 支付配置（支付宝、微信支付、银行转账） |
| recommendation | 智能推荐算法配置 |

## 定时任务

项目中的定时任务定义在 `app/Console/Commands/ScheduledTasks.php`：

| 任务 | 执行时间 | 说明 |
|------|----------|------|
| CleanOldLogsJob | 每日 | 清理 30 天前的操作日志 |

查看定时任务列表：
```bash
php artisan schedule:list
```

启动定时任务调度器：
```bash
php artisan schedule:work
```

## 队列任务

| 任务类 | 说明 |
|--------|------|
| CleanOldLogsJob | 清理旧操作日志 |
| SendNotificationJob | 发送单个通知 |
| SendBulkNotificationJob | 批量发送通知 |

启动队列监听器：
```bash
php artisan queue:work
```

## 中间件

| 中间件 | 说明 |
|--------|------|
| `api.auth` | 用户 JWT 认证 |
| `admin.auth` | 管理员 JWT 认证 |
| `admin.permission` | 管理员权限检查 |
| `throttle:login` | 登录限流 |
| `throttle:auth-api` | 认证接口限流 |

## API 响应格式

成功响应：
```json
{
    "success": true,
    "message": "操作成功",
    "data": { ... }
}
```

错误响应：
```json
{
    "success": false,
    "message": "错误信息",
    "errors": { ... }
}
```

## 测试

```bash
# 运行所有测试
composer test

# 或通过 Laravel
php artisan test

# 带覆盖率
php artisan test --coverage
```

## 生产环境部署

### 1. 后端优化

```bash
# 优化配置
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 设置权限
chmod -R 775 storage bootstrap/cache

# 确保使用正确的生产环境 .env
```

### 2. 前端构建

```bash
cd web
npm run build
```

### 3. Nginx 配置示例

```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /path/to/laravel-vue-template/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location /web/ {
        alias /path/to/laravel-vue-template/web/dist/;
        try_files $uri $uri/ /web/index.html;
    }
}
```

## 参与贡献

1. Fork 本仓库
2. 创建特性分支 (`git checkout -b feature/amazing-feature`)
3. 提交更改 (`git commit -m 'Add amazing feature'`)
4. 推送到分支 (`git push origin feature/amazing-feature`)
5. 创建 Pull Request

## 许可证

MIT License
