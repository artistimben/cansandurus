# 🚀 Sunucu Güncelleme Talimatları

## Sorun
Hata kodu düzenlenirken "Attempt to read property 'category' on null" hatası alınıyor.

## Çözüm
Category alanı null olan kayıtlar düzeltildi ve view'lara null check'ler eklendi.

---

## Sunucuda Yapılacak İşlemler

### 1. Kod Güncellemesi
```bash
# Projeyi git'ten çek (veya dosyaları FTP ile yükle)
git pull origin main
# VEYA
# Değişen dosyaları FTP ile yükle (aşağıdaki listeye bakın)
```

### 2. Migration Çalıştır
```bash
php artisan migrate
```

Bu migration:
- Null olan category değerlerini 'other' yapar
- Category kolonunu NOT NULL yapar

### 3. Cache Temizle
```bash
php artisan config:clear
php artisan view:clear
php artisan route:clear
php artisan cache:clear
```

---

## Değişen Dosyalar (FTP için)

Eğer Git kullanmıyorsanız, bu dosyaları manuel olarak yükleyin:

### View Dosyaları (resources/views/)
1. `admin/error-codes/index.blade.php` - Null check eklendi
2. `admin/error-codes/edit.blade.php` - Null check eklendi
3. `admin/error-codes/show.blade.php` - Null check eklendi

### Migration Dosyası (database/migrations/)
4. `2026_02_15_231335_fix_null_categories_in_error_codes.php` - YENİ

### Import Command (app/Console/Commands/)
5. `ImportHistoricalDowntime.php` - is_active eklendi

---

## Test

Sunucuda güncelleme sonrası test edin:

1. **Admin panele giriş yapın**
   - `/admin/error-codes` sayfasına gidin
   - Bir hata kodu düzenleyin
   - Hata almamalısınız

2. **Raporları kontrol edin**
   - `/reports/yearly?year=2025` 
   - Hata almamalısınız

---

## Rollback (Sorun Olursa)

Eğer bir sorun olursa:

```bash
php artisan migrate:rollback --step=1
```

---

## Notlar

- ✅ Mevcut veriler korunur
- ✅ Kullanıcı hesapları etkilenmez
- ✅ Sadece null category değerleri 'other' olarak güncellenir
- ✅ Tüm view'larda null check var, gelecekte sorun çıkmaz

---

## Destek

Sorun yaşarsanız log dosyasını kontrol edin:
```bash
tail -f storage/logs/laravel.log
```
