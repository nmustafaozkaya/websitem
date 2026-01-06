#!/bin/bash

# ========================================
# Cinema API - Ubuntu/EC2 Kurulum Scripti
# ========================================

set -e

echo "========================================"
echo "  Cinema API - Ubuntu Kurulum Başlıyor"
echo "========================================"
echo ""

# Renk kodları
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Sistem güncellemesi
echo -e "${GREEN}[1/10] Sistem güncelleniyor...${NC}"
sudo apt update && sudo apt upgrade -y

# Gerekli paketlerin kurulumu
echo -e "${GREEN}[2/10] Gerekli paketler kuruluyor...${NC}"
sudo apt install -y \
    software-properties-common \
    curl \
    wget \
    git \
    unzip \
    build-essential \
    ca-certificates \
    gnupg \
    lsb-release

# PHP 8.2 ve gerekli extension'lar
echo -e "${GREEN}[3/10] PHP 8.2 kuruluyor...${NC}"
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update
sudo apt install -y \
    php8.2 \
    php8.2-fpm \
    php8.2-cli \
    php8.2-common \
    php8.2-mysql \
    php8.2-zip \
    php8.2-gd \
    php8.2-mbstring \
    php8.2-curl \
    php8.2-xml \
    php8.2-bcmath \
    php8.2-intl \
    php8.2-readline \
    php8.2-tokenizer

# Composer kurulumu
echo -e "${GREEN}[4/10] Composer kuruluyor...${NC}"
if ! command -v composer &> /dev/null; then
    curl -sS https://getcomposer.org/installer | php
    sudo mv composer.phar /usr/local/bin/composer
    sudo chmod +x /usr/local/bin/composer
else
    echo "Composer zaten kurulu."
fi

# MySQL kurulumu
echo -e "${GREEN}[5/10] MySQL kuruluyor...${NC}"
if ! command -v mysql &> /dev/null; then
    sudo apt install -y mysql-server
    sudo systemctl start mysql
    sudo systemctl enable mysql
    
    echo -e "${YELLOW}MySQL root şifresi ayarlanıyor...${NC}"
    echo "Lütfen MySQL root şifresini girin:"
    read -s MYSQL_ROOT_PASSWORD
    
    # MySQL güvenlik ayarları
    sudo mysql_secure_installation <<EOF

y
$MYSQL_ROOT_PASSWORD
$MYSQL_ROOT_PASSWORD
y
y
y
y
EOF
else
    echo "MySQL zaten kurulu."
fi

# Nginx kurulumu
echo -e "${GREEN}[6/10] Nginx kuruluyor...${NC}"
if ! command -v nginx &> /dev/null; then
    sudo apt install -y nginx
    sudo systemctl start nginx
    sudo systemctl enable nginx
else
    echo "Nginx zaten kurulu."
fi

# Node.js ve NPM kurulumu
echo -e "${GREEN}[7/10] Node.js kuruluyor...${NC}"
if ! command -v node &> /dev/null; then
    curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash -
    sudo apt install -y nodejs
else
    echo "Node.js zaten kurulu."
fi

# Proje dizini oluşturma
echo -e "${GREEN}[8/10] Proje dizini hazırlanıyor...${NC}"
PROJECT_DIR="/var/www/cinema-api"
sudo mkdir -p $PROJECT_DIR
sudo chown -R $USER:$USER $PROJECT_DIR

# .env dosyası kontrolü
if [ ! -f "$PROJECT_DIR/.env" ]; then
    echo -e "${YELLOW}.env dosyası bulunamadı. Lütfen .env.example'dan kopyalayın ve düzenleyin.${NC}"
fi

# Nginx konfigürasyonu
echo -e "${GREEN}[9/10] Nginx konfigürasyonu yapılıyor...${NC}"
echo -e "${YELLOW}Lütfen domain adınızı girin (örn: api.yourdomain.com):${NC}"
read DOMAIN_NAME

# Nginx config dosyasını oluştur
sudo tee /etc/nginx/sites-available/cinema-api > /dev/null <<EOF
server {
    listen 80;
    listen [::]:80;
    server_name $DOMAIN_NAME;
    root $PROJECT_DIR/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    access_log /var/log/nginx/cinema-api-access.log;
    error_log /var/log/nginx/cinema-api-error.log;

    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME \$realpath_root\$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }

    location ~ ^/(storage|bootstrap/cache) {
        deny all;
    }

    location ~ ^/storage/app/public {
        allow all;
    }

    location ~* \.(jpg|jpeg|png|gif|ico|css|js|svg|woff|woff2|ttf|eot)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
        access_log off;
    }

    gzip on;
    gzip_vary on;
    gzip_proxied any;
    gzip_comp_level 6;
    gzip_types text/plain text/css text/xml text/javascript application/json application/javascript application/xml+rss application/rss+xml font/truetype font/opentype application/vnd.ms-fontobject image/svg+xml;
}
EOF

# Nginx site'ı aktif et
sudo ln -sf /etc/nginx/sites-available/cinema-api /etc/nginx/sites-enabled/
sudo rm -f /etc/nginx/sites-enabled/default

# Nginx test ve reload
sudo nginx -t
sudo systemctl reload nginx

# PHP-FPM ayarları
echo -e "${GREEN}[10/10] PHP-FPM ayarları yapılıyor...${NC}"
sudo sed -i 's/;cgi.fix_pathinfo=1/cgi.fix_pathinfo=0/' /etc/php/8.2/fpm/php.ini
sudo systemctl restart php8.2-fpm

echo ""
echo -e "${GREEN}========================================"
echo "  Kurulum Tamamlandı!"
echo "========================================"
echo ""
echo "Sonraki Adımlar:"
echo "1. Proje dosyalarını $PROJECT_DIR dizinine kopyalayın"
echo "2. .env dosyasını oluşturun ve düzenleyin"
echo "3. 'composer install --no-dev' komutunu çalıştırın"
echo "4. 'php artisan key:generate' komutunu çalıştırın"
echo "5. 'php artisan migrate' komutunu çalıştırın"
echo "6. 'php artisan storage:link' komutunu çalıştırın"
echo "7. Storage ve cache dizinlerine yazma izni verin:"
echo "   sudo chown -R www-data:www-data $PROJECT_DIR/storage"
echo "   sudo chown -R www-data:www-data $PROJECT_DIR/bootstrap/cache"
echo "   sudo chmod -R 775 $PROJECT_DIR/storage"
echo "   sudo chmod -R 775 $PROJECT_DIR/bootstrap/cache"
echo ""
echo -e "${NC}"

