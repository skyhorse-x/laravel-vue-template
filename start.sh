#!/bin/bash

# ===========================================
# Laravel Vue Template 启动脚本
# ===========================================

# 颜色定义
GREEN='\033[0;32m'
BLUE='\033[0;34m'
YELLOW='\033[1;33m'
NC='\033[0m'

# 项目根目录
PROJECT_ROOT="$(cd "$(dirname "$0")" && pwd)"
cd "$PROJECT_ROOT"

# 检查 .env
if [ ! -f "$PROJECT_ROOT/.env" ]; then
    echo -e "${YELLOW}[WARNING]${NC} .env 文件不存在"
    if [ -f "$PROJECT_ROOT/.env.example" ]; then
        cp "$PROJECT_ROOT/.env.example" "$PROJECT_ROOT/.env"
        echo -e "${GREEN}[OK]${NC} 已从 .env.example 创建 .env"
    fi
fi

if [ ! -f "$PROJECT_ROOT/web/.env" ] && [ -f "$PROJECT_ROOT/web/.env.example" ]; then
    cp "$PROJECT_ROOT/web/.env.example" "$PROJECT_ROOT/web/.env"
fi

# 检查依赖目录是否存在，不存在则安装
if [ ! -d "$PROJECT_ROOT/vendor" ]; then
    echo -e "${BLUE}[INFO]${NC} 安装 PHP 依赖..."
    composer install --no-interaction
fi

if [ ! -d "$PROJECT_ROOT/web/node_modules" ]; then
    echo -e "${BLUE}[INFO]${NC} 安装 Node.js 依赖..."
    cd "$PROJECT_ROOT/web" && npm install --legacy-peer-deps && cd "$PROJECT_ROOT"
fi

echo ""
echo -e "${GREEN}========================================${NC}"
echo -e "${GREEN}  启动中...${NC}"
echo -e "${GREEN}========================================${NC}"
echo ""
echo -e "  后端 API:    ${BLUE}http://localhost:8000${NC}"
echo -e "  前端:        ${BLUE}http://localhost:5173${NC}"
echo ""
echo -e "${YELLOW}按 Ctrl+C 停止所有服务${NC}"
echo ""

# 启动 Laravel 后端
php artisan serve --host=0.0.0.0 --port=8000 &

# 启动 Vite 前端开发服务器
cd "$PROJECT_ROOT/web" && npm run dev -- --host 0.0.0.0 &

wait
