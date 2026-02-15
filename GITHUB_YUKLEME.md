# 🚀 GitHub'a Yükleme Talimatları

## Adım 1: Git Repository Başlat

```bash
cd "/Users/boztech/Desktop/programm yedek/CANSAN/DURUSTAKİP"

# Git'i başlat
git init

# .gitignore dosyası zaten var, kontrol et
cat .gitignore
```

## Adım 2: GitHub'da Yeni Repository Oluştur

1. https://github.com adresine git
2. Sağ üstteki **+** butonuna tıkla → **New repository**
3. Repository adı: `durustaip` (veya istediğiniz isim)
4. **Private** seçin (özel proje için)
5. **Create repository** butonuna tıklayın
6. Açılan sayfadaki komutları KOPYALAMAYIN, aşağıdakileri kullanın

## Adım 3: Dosyaları Ekle ve Commit Et

```bash
# Tüm dosyaları ekle
git add .

# İlk commit
git commit -m "Initial commit: CANSAN Duruş Takip Sistemi

- Duruş kayıt sistemi
- Raporlama modülü (günlük/aylık/yıllık)
- Hata kodu analizi
- Chart.js grafikleri
- Excel geçmiş veri import
- Admin paneli
- Kullanıcı yönetimi
- Mobil responsive tasarım"
```

## Adım 4: GitHub'a Yükle

```bash
# GitHub repository URL'inizi buraya yazın
# Örnek: https://github.com/kullaniciadi/durustaip.git

git remote add origin https://github.com/KULLANICI_ADI/REPO_ADI.git

# Ana branch'i main olarak ayarla
git branch -M main

# GitHub'a yükle
git push -u origin main
```

**Not:** İlk push'ta GitHub kullanıcı adı ve şifre/token isteyecek.

---

## Gelecekte Güncelleme Yapmak İçin

```bash
# Değişiklikleri ekle
git add .

# Commit et
git commit -m "Açıklama buraya"

# GitHub'a yükle
git push
```

---

## Önemli: .env Dosyası

`.env` dosyası `.gitignore` içinde olduğu için GitHub'a yüklenmez (güvenlik için doğru).

Sunucuda `.env` dosyasını manuel olarak oluşturmanız gerekir:

```bash
# Sunucuda
cp .env.example .env
php artisan key:generate
```

Sonra database ayarlarını düzenleyin.

---

## Branch Stratejisi (Önerilen)

```bash
# Geliştirme için development branch
git checkout -b development
git push -u origin development

# Production için main branch kullanın
# Yeni özellikler development'ta test edin
# Sonra main'e merge edin
```

---

## Hassas Dosyalar (GitHub'a Yüklenmeyecekler)

✅ `.gitignore` zaten şunları hariç tutuyor:
- `.env` (database şifreleri)
- `node_modules/`
- `vendor/`
- `database/database.sqlite` (veritabanı)
- `storage/` (loglar, cache)

Bu dosyaları sunucuda ayrıca oluşturmanız gerekir.

---

## Sorun Giderme

### "Permission denied" hatası alırsanız:
```bash
# SSH key oluşturun
ssh-keygen -t ed25519 -C "email@example.com"

# Public key'i kopyalayın
cat ~/.ssh/id_ed25519.pub

# GitHub Settings → SSH Keys → Add SSH key
# Sonra HTTPS yerine SSH kullanın:
git remote set-url origin git@github.com:KULLANICI_ADI/REPO_ADI.git
```

### "Author identity unknown" hatası:
```bash
git config --global user.email "email@example.com"
git config --global user.name "İsminiz"
```

---

## Hızlı Komutlar

```bash
# 1. Repository başlat
git init

# 2. Dosyaları ekle
git add .

# 3. Commit et
git commit -m "Initial commit"

# 4. GitHub'a bağla (URL'i değiştirin)
git remote add origin https://github.com/KULLANICI_ADI/REPO_ADI.git

# 5. Yükle
git branch -M main
git push -u origin main
```
