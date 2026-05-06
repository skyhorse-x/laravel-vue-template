# Luma - Laravel 13 + Vue 3 Template

[中文版](./README_zh.md) | English

A full-stack template project built with Laravel 13, Vue 3, and Vite, featuring admin management, user portal, and member center.

## Tech Stack

| Layer | Technology | Version |
|-------|------------|---------|
| Backend | Laravel 13 / PHP | ^8.3 |
| Frontend | Vue 3 / Vite | ^3.5 / ^6.0 |
| UI Library | Element Plus | ^2.13 |
| State Management | Pinia | ^3.0 |
| Router | Vue Router | ^5.0 |
| Authentication | JWT (firebase/php-jwt) | ^7.0 |
| Cache | Predis (Redis) | ^3.4 |
| API Docs | L5-Swagger | ^11.0 |

## Features

### User Module
- User registration and login
- Password recovery (email verification)
- User profile management
- Referral reward system

### Admin Module
- Admin login
- Role and permission management (RBAC)
- Admin CRUD operations
- Operation logging

### Content Management
- Article management with categories
- Menu management

### Financial Management
- Financial records (deposit/withdraw)
- Income/expense statistics

### Member Center
- Personal profile
- Password change
- Financial records
- Referral system
- Notifications

### System Features
- IP blacklist
- Site settings management
- Scheduled tasks
- Queue jobs

## Project Structure

```
laravel-vue-template/
├── app/
│   ├── Console/
│   │   └── Commands/
│   │       ├── JwtSecretCommand.php     # Generate JWT secret
│   │       └── ScheduledTasks.php        # Scheduled tasks
│   ├── Helpers/
│   │   └── ApiResponse.php              # API response helper
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/                   # Admin controllers
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
│   │   │   ├── Auth/                    # Authentication
│   │   │   │   ├── AdminAuthController.php
│   │   │   │   └── UserAuthController.php
│   │   │   ├── Front/                   # Public API
│   │   │   │   ├── ArticleController.php
│   │   │   │   ├── FinancialController.php
│   │   │   │   ├── ReferralController.php
│   │   │   │   ├── SettingController.php
│   │   │   │   └── SitemapController.php
│   │   │   ├── User/                    # Member center
│   │   │   │   ├── ArticleController.php
│   │   │   │   ├── NotificationController.php
│   │   │   │   └── UserController.php
│   │   │   └── Controller.php
│   │   ├── Middleware/
│   │   │   ├── AdminAuth.php            # Admin JWT auth
│   │   │   ├── AdminPermission.php      # Permission check
│   │   │   └── ApiAuth.php              # User JWT auth
│   │   ├── Requests/                    # Form requests
│   │   └── Resources/                    # API resources
│   ├── Jobs/                             # Queue jobs
│   │   ├── CleanOldLogsJob.php
│   │   ├── SendBulkNotificationJob.php
│   │   └── SendNotificationJob.php
│   ├── Models/                           # Eloquent models
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
├── config/                               # Laravel config
├── database/
│   ├── factories/                        # Model factories
│   ├── migrations/                       # Database migrations
│   └── seeders/                          # Database seeders
│       ├── AdminSeeder.php
│       ├── MenuSeeder.php
│       ├── PermissionSeeder.php
│       ├── RoleSeeder.php
│       ├── SettingSeeder.php
│       └── UserSeeder.php
├── routes/
│   ├── api.php                           # API routes
│   ├── console.php                       # Console routes
│   └── web.php                           # Web routes
└── web/                                  # Vue 3 frontend
    └── src/
        ├── api/                          # API client
        ├── assets/                        # Static assets
        ├── components/                    # Vue components
        ├── i18n/                         # Internationalization
        ├── router/                        # Vue Router
        ├── stores/                        # Pinia stores
        │   ├── admin.js
        │   └── user.js
        ├── views/
        │   ├── admin/                     # Admin pages
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
        │   ├── front/                     # Public pages
        │   │   ├── ArticleDetail.vue
        │   │   ├── Articles.vue
        │   │   ├── ForgotPassword.vue
        │   │   ├── Home.vue
        │   │   ├── Login.vue
        │   │   ├── Register.vue
        │   │   └── ResetPassword.vue
        │   └── member/                    # Member pages
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

## Database Models

| Model | Description |
|-------|-------------|
| `User` | Regular user accounts |
| `Admin` | Admin user accounts |
| `Role` | User roles for RBAC |
| `Permission` | Permissions for RBAC |
| `Article` | Content articles |
| `Menu` | Navigation menus |
| `FinancialRecord` | Financial transactions |
| `Referral` | Referral relationships |
| `UserExtend` | Extended user information |
| `Notification` | User notifications |
| `NotificationTemplate` | Notification templates |
| `OperationLog` | Admin operation logs |
| `IpBlacklist` | Blocked IP addresses |
| `Setting` | System settings |

## API Routes

### Public Routes (No Authentication)

#### Authentication
| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/api/auth/register` | User registration |
| POST | `/api/auth/login` | User login |
| POST | `/api/auth/forgot-password` | Request password reset |
| POST | `/api/auth/reset-password` | Reset password with token |

#### Frontend
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/front/articles` | List articles |
| GET | `/api/front/articles/{id}` | Get article detail |
| GET | `/api/front/articles/categories` | Get article categories |
| GET | `/api/front/settings` | Get all settings |
| GET | `/api/front/settings/{key}` | Get setting by key |
| GET | `/api/front/referral/info/{code}` | Get referral info |
| GET | `/api/front/sitemap.xml` | Get sitemap |

#### Admin Authentication
| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/api/admin/login` | Admin login |
| POST | `/api/admin/forgot-password` | Admin password reset request |
| POST | `/api/admin/reset-password` | Admin password reset |

### Authenticated User Routes (Requires User JWT)

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/auth/me` | Get current user |
| POST | `/api/auth/logout` | Logout |
| PUT | `/api/auth/profile` | Update profile |
| PUT | `/api/auth/change-password` | Change password |
| GET | `/api/financial` | List financial records |
| GET | `/api/financial/{id}` | Get financial record detail |
| POST | `/api/financial/deposit` | Create deposit |
| POST | `/api/financial/withdraw` | Create withdrawal |
| GET | `/api/referral` | Get user's referral code |
| GET | `/api/user/balance` | Get user balance |
| GET | `/api/user/articles` | List user's articles |
| GET | `/api/user/articles/{id}` | Get user's article |
| POST | `/api/user/articles` | Create article |
| PUT | `/api/user/articles/{id}` | Update article |
| DELETE | `/api/user/articles/{id}` | Delete article |
| GET | `/api/user/notifications` | List notifications |
| GET | `/api/user/notifications/{id}` | Get notification |
| POST | `/api/user/notifications/{id}/read` | Mark as read |
| POST | `/api/user/notifications/read-all` | Mark all as read |
| DELETE | `/api/user/notifications/{id}` | Delete notification |

### Admin Routes (Requires Admin JWT)

#### Profile
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/admin/me` | Get current admin |
| POST | `/api/admin/logout` | Admin logout |
| PUT | `/api/admin/profile` | Update admin profile |
| PUT | `/api/admin/change-password` | Change admin password |

#### User Management
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/admin/users` | List users |
| GET | `/api/admin/users/{id}` | Get user detail |
| POST | `/api/admin/users` | Create user |
| PUT | `/api/admin/users/{id}` | Update user |
| DELETE | `/api/admin/users/{id}` | Delete user |
| DELETE | `/api/admin/users/batch` | Batch delete users |

#### Admin Management
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/admin/admins` | List admins |
| GET | `/api/admin/admins/{id}` | Get admin detail |
| POST | `/api/admin/admins` | Create admin |
| PUT | `/api/admin/admins/{id}` | Update admin |
| DELETE | `/api/admin/admins/{id}` | Delete admin |

#### Role & Permission
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/admin/roles` | List roles |
| GET | `/api/admin/roles/{id}` | Get role detail |
| POST | `/api/admin/roles` | Create role |
| PUT | `/api/admin/roles/{id}` | Update role |
| DELETE | `/api/admin/roles/{id}` | Delete role |
| GET | `/api/admin/permissions` | Get all permissions |

#### Article Management
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/admin/articles` | List articles |
| GET | `/api/admin/articles/{id}` | Get article detail |
| POST | `/api/admin/articles` | Create article |
| PUT | `/api/admin/articles/{id}` | Update article |
| DELETE | `/api/admin/articles/{id}` | Delete article |

#### Menu Management
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/admin/menus` | List menus (tree) |
| GET | `/api/admin/menus/all` | List all menus |
| GET | `/api/admin/menus/{id}` | Get menu detail |
| POST | `/api/admin/menus` | Create menu |
| PUT | `/api/admin/menus/{id}` | Update menu |
| DELETE | `/api/admin/menus/{id}` | Delete menu |

#### Financial Management
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/admin/financial/statistics` | Get statistics |
| GET | `/api/admin/financial` | List all records |
| GET | `/api/admin/financial/{id}` | Get record detail |
| POST | `/api/admin/financial` | Create record |

#### Notifications
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/admin/notifications` | List notifications |
| POST | `/api/admin/notifications` | Send notification |
| DELETE | `/api/admin/notifications/{id}` | Delete notification |
| DELETE | `/api/admin/notifications/batch` | Batch delete |

#### Operation Logs
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/admin/operation-logs` | List operation logs |
| GET | `/api/admin/operation-logs/modules` | Get module list |
| GET | `/api/admin/operation-logs/{id}` | Get log detail |
| DELETE | `/api/admin/operation-logs/{id}` | Delete log |
| DELETE | `/api/admin/operation-logs` | Clear all logs |

#### IP Blacklist
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/admin/ip-blacklist` | List blocked IPs |
| POST | `/api/admin/ip-blacklist` | Add IP to blacklist |
| DELETE | `/api/admin/ip-blacklist/{id}` | Remove from blacklist |
| DELETE | `/api/admin/ip-blacklist/batch` | Batch remove |

#### System Settings
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/admin/settings` | List all settings |
| POST | `/api/admin/settings` | Create/update settings |
| GET | `/api/admin/settings/{key}` | Get setting |
| PUT | `/api/admin/settings/{key}` | Update setting |
| GET | `/api/admin/settings/group/{group}` | Get by group |
| PUT | `/api/admin/settings/group/{group}` | Update by group |

## Requirements

- PHP 8.3+
- Composer 2.x
- MySQL 8.0+ / PostgreSQL / SQLite
- Redis 7.0+
- Node.js 18+
- npm / yarn / pnpm

## Installation

### 1. Clone the Project

```bash
git clone <repository-url>
cd laravel-vue-template
```

### 2. Backend Setup

```bash
# Install PHP dependencies
composer install

# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Generate JWT secret
php artisan jwt:secret

# Run migrations
php artisan migrate

# Seed database (creates default admin)
php artisan db:seed
```

### 3. Frontend Setup

```bash
cd web

# Install Node.js dependencies
npm install

# Start development server
npm run dev
```

### 4. Start Development Server

From project root:

```bash
# Using npm (runs all services)
npm run dev

# Or manually
php artisan serve --port=8000
# Frontend: http://localhost:5173
# Backend API: http://localhost:8000
```

## Quick Start with Default Admin

After seeding, login with:

- **Email**: admin@example.com
- **Password**: password

## Environment Configuration

Edit `.env` file (other configurations managed via admin panel):

```env
APP_NAME=Luma
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost

# Database
DB_CONNECTION=sqlite
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=root
DB_PASSWORD=

# Queue and Cache using database driver
SESSION_DRIVER=database
SESSION_LIFETIME=120
QUEUE_CONNECTION=database
CACHE_STORE=database

# JWT Configuration (run php artisan jwt:secret)
JWT_SECRET=
JWT_EXPIRE=7200
```

### Admin Configurable Options

Manage via **Admin Panel -> System Settings**:

| Group | Description |
|-------|-------------|
| site | Site name, logo, description, keywords, copyright, ICP |
| auth | Login/registration settings |
| contact | Contact info (phone, email, address, QQ, WeChat) |
| email | Email service (SMTP server, port, encryption, credentials) |
| redis | Redis cache (host, port, password, database, prefix) |
| referral | Referral reward settings |
| payment | Payment config (Alipay, WeChat Pay, Bank transfer) |
| recommendation | Smart recommendation algorithm config |

## Scheduled Tasks

The following tasks are scheduled in `app/Console/Commands/ScheduledTasks.php`:

| Task | Schedule | Description |
|------|----------|-------------|
| CleanOldLogsJob | Daily | Clean operation logs older than 30 days |

View scheduled tasks:
```bash
php artisan schedule:list
```

Run scheduler:
```bash
php artisan schedule:work
```

## Queue Jobs

| Job | Description |
|-----|-------------|
| CleanOldLogsJob | Cleanup old operation logs |
| SendNotificationJob | Send single notification |
| SendBulkNotificationJob | Send bulk notifications |

Start queue worker:
```bash
php artisan queue:work
```

## Middleware

| Middleware | Description |
|------------|-------------|
| `api.auth` | Authenticates regular users via JWT |
| `admin.auth` | Authenticates admin users via JWT |
| `admin.permission` | Checks admin permissions |
| `throttle:login` | Rate limiting for login attempts |
| `throttle:auth-api` | Rate limiting for auth endpoints |

## API Response Format

Standard success response:
```json
{
    "success": true,
    "message": "Operation successful",
    "data": { ... }
}
```

Standard error response:
```json
{
    "success": false,
    "message": "Error message",
    "errors": { ... }
}
```

## Testing

```bash
# Run all tests
composer test

# Or via Laravel
php artisan test

# Run with coverage
php artisan test --coverage
```

## Production Deployment

### 1. Backend

```bash
# Optimize
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Set permissions
chmod -R 775 storage bootstrap/cache

# Use proper .env for production
```

### 2. Frontend

```bash
cd web
npm run build
```

### 3. Nginx Configuration

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

## Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## License

MIT License
