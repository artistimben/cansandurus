# Cansan Duruş Takip Sistemi

Cansan çelik haddehane fabrikası için geliştirilen yüksek güvenlikli duruş takip sistemi.

## 🎯 Özellikler

- ✅ Makine duruş takibi (başlat/bitir)
- ✅ Rol bazlı yetkilendirme (Admin, Manager, Operator, Maintenance)
- ✅ Günlük/Aylık/Yıllık raporlama
- ✅ Activity logging ve audit trail
- ✅ Yüksek güvenlik (CSRF, XSS, SQL Injection koruması)
- ✅ Rate limiting
- ✅ Responsive tasarım (Tailwind CSS)
- ✅ Türkçe dil desteği

## 🔧 Teknoloji Stack

- **Backend:** PHP 8.1+, Laravel 12.x
- **Database:** MySQL 8.0+
- **Frontend:** Blade Templates, Tailwind CSS, Alpine.js
- **Security:** Spatie Permission, Rate Limiting, Activity Logging
- **Export:** Excel (Maatwebsite), PDF (DomPDF)

## 📦 Kurulum

### 1. Gereksinimler

- PHP 8.1 veya üzeri
- Composer
- MySQL 8.0 veya üzeri
- Node.js & NPM

### 2. Projeyi İndirin

```bash
git clone <repository-url>
cd DURUSTAKİP
```

### 3. Bağımlılıkları Yükleyin

```bash
# Composer paketleri
composer install

# NPM paketleri
npm install
```

### 4. .env Dosyasını Yapılandırın

```bash
cp .env.example .env
```

**ENV_CONFIGURATION.md** dosyasındaki talimatları takip ederek `.env` dosyanızı düzenleyin:

- `APP_ENV=production`
- `APP_DEBUG=false`
- Veritabanı bilgilerinizi girin
- Güvenlik ayarlarını yapın

### 5. Uygulama Anahtarı Oluşturun

```bash
php artisan key:generate
```

### 6. Veritabanını Oluşturun

```sql
CREATE DATABASE cansan_durus_takip CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'cansan_user'@'localhost' IDENTIFIED BY 'GÜÇLÜ_ŞİFRE';
GRANT SELECT, INSERT, UPDATE, DELETE, CREATE, INDEX, ALTER, DROP, REFERENCES 
ON cansan_durus_takip.* TO 'cansan_user'@'localhost';
FLUSH PRIVILEGES;
```

### 7. Migration ve Seed

```bash
# Migration'ları çalıştır
php artisan migrate

# Örnek verileri yükle
php artisan db:seed
```

### 8. Frontend Asset'leri Derleyin

```bash
# Development
npm run dev

# Production
npm run build
```

### 9. Sunucuyu Başlatın

```bash
php artisan serve
```

Tarayıcınızda `http://localhost:8000` adresine gidin.

## 👤 Varsayılan Kullanıcılar

Seeder çalıştırıldığında aşağıdaki kullanıcılar oluşturulur:

| Role | Email | Şifre |
|------|-------|-------|
| Admin | admin@cansan.local | Admin@Cansan2026 |
| Manager | manager@cansan.local | Manager@Cansan2026 |
| Operator | operator@cansan.local | Operator@Cansan2026 |
| Maintenance | maintenance@cansan.local | Maintenance@Cansan2026 |

⚠️ **ÖNEMLİ:** Üretim ortamında bu şifreleri mutlaka değiştirin!

## 🔒 Güvenlik

### Uygulama Seviyesi Güvenlik

- ✅ CSRF koruması (tüm formlarda)
- ✅ XSS koruması (Blade auto-escaping)
- ✅ SQL Injection koruması (Eloquent ORM)
- ✅ Rate limiting (login denemelerinde)
- ✅ Password policy (min 12 karakter, karışık)
- ✅ Session güvenliği (secure cookies)
- ✅ Activity logging (tüm önemli işlemler)
- ✅ Rol bazlı erişim kontrolü

### Güvenlik Headers

```
X-Frame-Options: DENY
X-Content-Type-Options: nosniff
X-XSS-Protection: 1; mode=block
Strict-Transport-Security: max-age=31536000
Content-Security-Policy: default-src 'self'
Referrer-Policy: strict-origin-when-cross-origin
```

### Öneriler

1. SSL/TLS sertifikası kullanın
2. Firewall yapılandırması yapın (sadece port 80/443)
3. Düzenli güvenlik güncellemeleri yapın
4. Günlük backup alın
5. Activity log'ları düzenli kontrol edin

## 📊 Kullanım

### Duruş Başlatma

1. Dashboard'a giriş yapın
2. "Yeni Duruş Başlat" butonuna tıklayın
3. Makine ve hata kodunu seçin
4. Notlar ekleyin (opsiyonel)
5. "Duruşu Başlat" butonuna tıklayın

### Duruş Bitirme

1. "Duruşlar" menüsüne gidin
2. Aktif duruşu bulun
3. "Bitir" butonuna tıklayın
4. Duruş süresi otomatik hesaplanır

### Raporlama

1. "Raporlar" menüsüne gidin
2. Günlük, Aylık veya Yıllık rapor seçin
3. Tarih seçin
4. Raporları görüntüleyin
5. Excel/PDF olarak dışa aktarın (yakında)

## 🛠️ Geliştirme

### Cache Temizleme

```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### Optimizasyon (Production)

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
composer install --no-dev --optimize-autoloader
```

## 📝 Lisans

Bu proje Cansan Çelik Haddehane için özel olarak geliştirilmiştir.
Tüm hakları saklıdır.

## 📞 Destek

Herhangi bir sorun yaşarsanız, sistem yöneticisi ile iletişime geçin.
