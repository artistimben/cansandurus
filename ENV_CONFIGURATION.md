# Cansan Duruş Takip Sistemi - .env Yapılandırma Kılavuzu

## Önemli Güvenlik Notları

Bu dosya `.env` dosyasının nasıl yapılandırılması gerektiğini açıklar.
**DİKKAT:** `.env` dosyası asla Git'e eklenmemelidir!

## Gerekli .env Ayarları

Aşağıdaki ayarları `.env` dosyanıza ekleyin:

```bash
# Uygulama Ayarları
APP_NAME="Cansan Duruş Takip"
APP_ENV=production
APP_KEY=base64:XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX
APP_DEBUG=false
APP_URL=http://localhost

# Log Ayarları
LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=warning

# Veritabanı Ayarları (MySQL)
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=cansan_durus_takip
DB_USERNAME=cansan_user
DB_PASSWORD=GÜÇLÜ_ŞİFRE_BURAYA

# Session Güvenlik Ayarları
SESSION_DRIVER=database
SESSION_LIFETIME=30
SESSION_ENCRYPT=true
SESSION_PATH=/
SESSION_DOMAIN=null
SESSION_SECURE_COOKIE=true
SESSION_HTTP_ONLY=true
SESSION_SAME_SITE=strict

# Cache Ayarları
CACHE_DRIVER=database
CACHE_PREFIX=cansan_

# Queue Ayarları
QUEUE_CONNECTION=database

# Güvenlik Ayarları
SESSION_TIMEOUT=30
LOGIN_MAX_ATTEMPTS=5
LOGIN_DECAY_MINUTES=15
TWO_FACTOR_ENABLED=false
TWO_FACTOR_REQUIRED_ADMIN=true
IP_BINDING_ENABLED=false

# Filesystem
FILESYSTEM_DISK=local

# Mail Ayarları (Opsiyonel - bildirimler için)
MAIL_MAILER=smtp
MAIL_HOST=mailhog
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="noreply@cansan.local"
MAIL_FROM_NAME="${APP_NAME}"
```

## Üretim Ortamı İçin Kritik Ayarlar

1. **APP_ENV=production** - Mutlaka production olmalı
2. **APP_DEBUG=false** - Debug modunu kapatın
3. **APP_KEY** - `php artisan key:generate` komutu ile oluşturun
4. **DB_PASSWORD** - Güçlü bir şifre kullanın (min 20 karakter)
5. **SESSION_SECURE_COOKIE=true** - HTTPS kullanıyorsanız true yapın
6. **SESSION_ENCRYPT=true** - Session verilerini şifreler

## MySQL Veritabanı Oluşturma

```sql
-- MySQL'e root olarak bağlanın
CREATE DATABASE cansan_durus_takip CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Özel kullanıcı oluşturun (güvenlik için)
CREATE USER 'cansan_user'@'localhost' IDENTIFIED BY 'GÜÇLÜ_ŞİFRE_BURAYA';

-- Sadece gerekli yetkileri verin
GRANT SELECT, INSERT, UPDATE, DELETE, CREATE, INDEX, ALTER, DROP, REFERENCES 
ON cansan_durus_takip.* TO 'cansan_user'@'localhost';

-- Yetkileri uygulayın
FLUSH PRIVILEGES;
```

## İlk Kurulum Adımları

```bash
# 1. .env dosyasını oluşturun
cp .env.example .env

# 2. .env dosyasını yukarıdaki ayarlarla düzenleyin

# 3. Uygulama anahtarını oluşturun
php artisan key:generate

# 4. Veritabanı migration'larını çalıştırın
php artisan migrate

# 5. İlk admin kullanıcısını oluşturun
php artisan db:seed --class=AdminSeeder

# 6. Cache ve yapılandırmaları optimize edin
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## Güvenlik Kontrol Listesi

- [ ] APP_DEBUG=false olarak ayarlandı
- [ ] APP_ENV=production olarak ayarlandı
- [ ] Güçlü veritabanı şifresi kullanıldı
- [ ] SESSION_SECURE_COOKIE=true (HTTPS varsa)
- [ ] .env dosyası .gitignore'da
- [ ] Dosya izinleri doğru ayarlandı (storage/ ve bootstrap/cache/ yazılabilir)
- [ ] SSL/TLS sertifikası yüklendi
- [ ] Firewall yapılandırıldı
- [ ] Düzenli backup stratejisi oluşturuldu
