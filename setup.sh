#!/bin/bash
set -e

echo "=========================================="
echo "  Smart Health Advisor - Setup Script"
echo "=========================================="
echo ""

# Colors
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m'

# Check prerequisites
command -v php >/dev/null 2>&1 || { echo -e "${RED}PHP is required but not installed.${NC}" >&2; exit 1; }
command -v composer >/dev/null 2>&1 || { echo -e "${RED}Composer is required but not installed.${NC}" >&2; exit 1; }

echo -e "${YELLOW}[1/9] Creating storage directories...${NC}"
mkdir -p storage/app/videos \
         storage/app/public \
         storage/framework/cache/data \
         storage/framework/sessions \
         storage/framework/views \
         storage/logs \
         bootstrap/cache

# Add .gitignore files
for dir in storage/app/videos storage/app/public storage/framework/cache/data \
           storage/framework/sessions storage/framework/views storage/logs bootstrap/cache; do
    if [ ! -f "$dir/.gitignore" ]; then
        printf '*\n!.gitignore\n' > "$dir/.gitignore"
    fi
done

echo -e "${YELLOW}[2/9] Setting permissions...${NC}"
chmod -R 775 storage bootstrap/cache 2>/dev/null || true

echo -e "${YELLOW}[3/9] Creating .env file...${NC}"
if [ ! -f .env ]; then
    cp .env.example .env
    echo -e "${GREEN}Created .env${NC}"
else
    echo -e "${GREEN}.env already exists, skipping${NC}"
fi

echo -e "${YELLOW}[4/9] Installing composer dependencies...${NC}"
composer install --no-interaction --prefer-dist --optimize-autoloader

echo -e "${YELLOW}[5/9] Installing npm dependencies...${NC}"
if command -v npm >/dev/null 2>&1; then
    npm install
else
    echo -e "${YELLOW}NPM not found, skipping frontend dependencies${NC}"
fi

echo -e "${YELLOW}[6/9] Generating application key...${NC}"
php artisan key:generate

echo -e "${YELLOW}[7/9] Setting up database...${NC}"
touch database/database.sqlite
php artisan migrate:fresh --seed --force

echo -e "${YELLOW}[8/9] Creating storage link...${NC}"
php artisan storage:link 2>/dev/null || true

echo -e "${YELLOW}[9/9] Building frontend assets...${NC}"
if command -v npm >/dev/null 2>&1; then
    npm run build 2>/dev/null || echo -e "${YELLOW}Asset build skipped${NC}"
fi

# Clear caches
php artisan config:clear 2>/dev/null || true
php artisan route:clear 2>/dev/null || true
php artisan view:clear 2>/dev/null || true

echo ""
echo -e "${GREEN}=========================================="
echo "  ✅ Setup Complete!"
echo "==========================================${NC}"
echo ""
echo -e "${GREEN}Default Login Credentials:${NC}"
echo "  Admin:    admin@hararepoly.ac.zw / Admin@123"
echo "  Advisor:  counselor@hararepoly.ac.zw / Advisor@123"
echo ""
echo -e "${GREEN}Start the server:${NC}"
echo "  php artisan serve"
echo ""
echo -e "${GREEN}Then visit:${NC}"
echo "  http://localhost:8000"
echo ""