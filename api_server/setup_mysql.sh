#!/bin/bash

# ========================================
# MySQL Veritabanı Kurulum Scripti
# ========================================

set -e

echo "========================================"
echo "  MySQL Veritabanı Kurulumu"
echo "========================================"
echo ""

# Renk kodları
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m'

# MySQL root şifresi
echo -e "${YELLOW}MySQL root şifresini girin:${NC}"
read -s MYSQL_ROOT_PASSWORD

# Veritabanı bilgileri
echo -e "${YELLOW}Veritabanı adı (varsayılan: cinema_fresh):${NC}"
read DB_NAME
DB_NAME=${DB_NAME:-cinema_fresh}

echo -e "${YELLOW}Veritabanı kullanıcı adı (varsayılan: cinema_user):${NC}"
read DB_USER
DB_USER=${DB_USER:-cinema_user}

echo -e "${YELLOW}Veritabanı kullanıcı şifresi:${NC}"
read -s DB_PASSWORD

# Veritabanı oluştur
echo -e "${GREEN}Veritabanı oluşturuluyor...${NC}"
mysql -u root -p"$MYSQL_ROOT_PASSWORD" <<EOF
CREATE DATABASE IF NOT EXISTS $DB_NAME CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER IF NOT EXISTS '$DB_USER'@'localhost' IDENTIFIED BY '$DB_PASSWORD';
GRANT ALL PRIVILEGES ON $DB_NAME.* TO '$DB_USER'@'localhost';
FLUSH PRIVILEGES;
EOF

echo ""
echo -e "${GREEN}========================================"
echo "  Veritabanı Kurulumu Tamamlandı!"
echo "========================================"
echo ""
echo "Veritabanı Bilgileri:"
echo "  Veritabanı Adı: $DB_NAME"
echo "  Kullanıcı Adı: $DB_USER"
echo "  Şifre: [gizli]"
echo ""
echo "Bu bilgileri .env dosyanıza ekleyin:"
echo "  DB_DATABASE=$DB_NAME"
echo "  DB_USERNAME=$DB_USER"
echo "  DB_PASSWORD=$DB_PASSWORD"
echo ""
echo -e "${NC}"

