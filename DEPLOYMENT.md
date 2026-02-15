# 🚀 Sunucuya Deployment Talimatları

## Seçenek 1: GitHub'dan Çekme (ÖNERİLEN)

### İlk Kurulum (Sunucuda ilk kez kuruyorsanız)

```bash
# 1. Sunucuya SSH ile bağlanın
ssh kullanici@sunucu-ip

# 2. Web dizinine gidin (örnek: /var/www/html veya /home/kullanici/public_html)
cd /var/www/html

# 3. GitHub'dan projeyi klonlayın
git clone https://github.com/artistimben/cansandurus.git
cd cansandurus

# 4. Composer bağımlılıklarını yükleyin
composer install --optimize-autoloader --no-dev

# 5. NPM bağımlılıklarını yükleyin
npm install
npm run build

# 6. .env dosyasını oluşturun
cp .env.example .env
nano .env  # veya vi .env

# 7. .env dosyasını düzenleyin:
# - APP_ENV=production
# - APP_DEBUG=false
# - APP_URL=https://siteniz.com
# - Database bilgilerini girin
# - APP_KEY'i oluşturun (sonraki adımda)

# 8. Uygulama anahtarı oluşturun
php artisan key:generate

# 9. Database'i oluşturun ve migrate edin
php artisan migrate --force

# 10. Seeders'ı çalıştırın (ilk kurulumda)
php artisan db:seed --class=AdminUserSeeder
php artisan db:seed --class=MachineSeeder
php artisan db:seed --class=ErrorCodeSeeder

# 11. Storage linkini oluşturun
php artisan storage:link

# 12. İzinleri ayarlayın
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

# 13. Cache'leri oluşturun
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

### Güncelleme (Zaten kurulu, sadece değişiklikleri çekmek için)

```bash
# 1. Sunucuya SSH ile bağlanın
ssh kullanici@sunucu-ip

# 2. Proje dizinine gidin
cd /var/www/html/cansandurus

# 3. Maintenance mode'a alın (kullanıcılar "bakım" mesajı görecek)
php artisan down

# 4. GitHub'dan son değişiklikleri çekin
git pull origin main

# 5. Composer bağımlılıklarını güncelleyin
composer install --optimize-autoloader --no-dev

# 6. NPM bağımlılıklarını güncelleyin (CSS/JS değişmişse)
npm install
npm run build

# 7. Migration'ları çalıştırın (yeni migration varsa)
php artisan migrate --force

# 8. Cache'leri temizleyin
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

# 9. Yeni cache'leri oluşturun
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 10. Maintenance mode'dan çıkın
php artisan up
```

---

## Seçenek 2: FTP ile Manuel Yükleme

### İlk Kurulum

1. **FileZilla veya benzeri FTP programı ile bağlanın**

2. **Şu dosyaları YÜKLEMEYIN:**
   - `.env` (sunucuda oluşturacaksınız)
   - `node_modules/`
   - `vendor/`
   - `database/database.sqlite`
   - `storage/` içindeki dosyalar (klasör yapısını yükleyin)

3. **Tüm diğer dosyaları yükleyin**

4. **SSH ile sunucuya bağlanın ve şu komutları çalıştırın:**

```bash
cd /var/www/html/cansandurus

# Composer
composer install --optimize-autoloader --no-dev

# NPM
npm install
npm run build

# .env oluştur
cp .env.example .env
nano .env

# Key generate
php artisan key:generate

# Database
php artisan migrate --force
php artisan db:seed --class=AdminUserSeeder

# İzinler
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

# Cache
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Güncelleme (FTP)

1. **Değişen dosyaları FTP ile yükleyin:**
   - `app/`
   - `resources/`
   - `database/migrations/`
   - `routes/`
   - `config/`
   - `public/css/`
   - `public/js/`

2. **SSH ile komutları çalıştırın:**

```bash
cd /var/www/html/cansandurus

php artisan down
php artisan migrate --force
php artisan cache:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan up
```

---

## Seçenek 3: cPanel File Manager

1. **cPanel'e giriş yapın**
2. **File Manager'ı açın**
3. **public_html** klasörüne gidin
4. **Upload** butonuna tıklayın
5. **Tüm dosyaları ZIP olarak yükleyin**
6. **Extract** edin
7. **Terminal** (cPanel'de varsa) veya SSH ile yukarıdaki komutları çalıştırın

---

## Önemli Notlar

### 1. .env Dosyası Ayarları

```env
APP_NAME="CANSAN Duruş Takip"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://siteniz.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=veritabani_adi
DB_USERNAME=veritabani_kullanici
DB_PASSWORD=veritabani_sifre

MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=email@gmail.com
MAIL_PASSWORD=sifre
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=email@gmail.com
MAIL_FROM_NAME="${APP_NAME}"
```

### 2. Web Server Ayarları

**Apache (.htaccess zaten var)**
- `public` klasörü root olmalı
- `mod_rewrite` aktif olmalı

**Nginx (nginx.conf)**
```nginx
server {
    listen 80;
    server_name siteniz.com;
    root /var/www/html/cansandurus/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

### 3. Gerekli PHP Uzantıları

```bash
# Kontrol edin
php -m

# Gerekli olanlar:
- BCMath
- Ctype
- Fileinfo
- JSON
- Mbstring
- OpenSSL
- PDO
- Tokenizer
- XML
- ZIP
- GD
```

### 4. Dosya İzinleri

```bash
# Laravel için gerekli izinler
chmod -R 755 storage
chmod -R 755 bootstrap/cache
chown -R www-data:www-data storage
chown -R www-data:www-data bootstrap/cache
```

### 5. SSL Sertifikası (HTTPS)

```bash
# Let's Encrypt ile ücretsiz SSL
sudo certbot --nginx -d siteniz.com -d www.siteniz.com
```

---

## Sorun Giderme

### "500 Internal Server Error"
```bash
# Log'lara bakın
tail -f storage/logs/laravel.log

# İzinleri kontrol edin
ls -la storage/
ls -la bootstrap/cache/

# Cache temizleyin
php artisan cache:clear
php artisan config:clear
```

### "Permission denied"
```bash
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

### "Class not found"
```bash
composer dump-autoload
php artisan clear-compiled
php artisan optimize
```

### Database bağlantı hatası
```bash
# .env dosyasını kontrol edin
cat .env | grep DB_

# MySQL'e bağlanabilir misiniz?
mysql -u kullanici -p
```

---

## Hızlı Güncelleme Scripti

Sunucuda bir script oluşturun:

```bash
# deploy.sh
#!/bin/bash

cd /var/www/html/cansandurus

echo "🔄 Maintenance mode..."
php artisan down

echo "📥 Pulling from GitHub..."
git pull origin main

echo "📦 Installing dependencies..."
composer install --optimize-autoload --no-dev

echo "🏗️ Building assets..."
npm install
npm run build

echo "🗄️ Running migrations..."
php artisan migrate --force

echo "🧹 Clearing cache..."
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

echo "💾 Caching..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "✅ Going live..."
php artisan up

echo "🎉 Deployment complete!"
```

Kullanımı:
```bash
chmod +x deploy.sh
./deploy.sh
```

---

## Güvenlik Kontrol Listesi

- [ ] `.env` dosyası `APP_DEBUG=false`
- [ ] `.env` dosyası `APP_ENV=production`
- [ ] SSL sertifikası kurulu (HTTPS)
- [ ] Güçlü database şifresi
- [ ] `storage/` ve `bootstrap/cache/` yazılabilir
- [ ] `.git` klasörü public'te değil
- [ ] Firewall aktif
- [ ] Düzenli backup alınıyor
- [ ] Admin şifresi değiştirildi (varsayılan değil)

---

## Yardım

Sorun yaşarsanız:
1. `storage/logs/laravel.log` dosyasına bakın
2. Web server error log'larını kontrol edin
3. `php artisan` komutlarını çalıştırın
