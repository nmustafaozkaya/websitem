# 📜 Database Script'leri

## 🔧 İki Farklı Script Var:

### 1️⃣ `first_setup.bat` / `first_setup.sh` 
**İLK KURULUM İÇİN**

✅ Ne zaman kullanılır:
- İlk defa veritabanı kurulumu yaparken
- Tüm veritabanını sıfırlamak istediğinizde
- Baştan başlamak istediğinizde

⚠️ UYARI:
- **TÜM VERİLER SİLİNİR!**
- Kullanıcılar, biletler, her şey temizlenir
- Yeni başlangıç yapar

📦 Ne yapar:
```
✓ Veritabanını sıfırlar (migrate:fresh)
✓ 81 il ekler
✓ 160+ sinema ekler
✓ 400+ salon oluşturur
✓ 40,000+ koltuk ekler
✓ 200+ güncel film (2024-2025)
✓ Yaklaşan filmler (coming soon)
✓ Test kullanıcıları oluşturur
✓ Roller ve yetkiler
✓ Seanslar oluşturur
```

**Kullanım:**
```bash
# Windows
first_setup.bat

# Linux/Mac
chmod +x first_setup.sh
./first_setup.sh
```

---

### 2️⃣ `update_database.bat` / `update_database.sh`
**GÜNCELLEME İÇİN**

✅ Ne zaman kullanılır:
- Sadece yeni filmler eklemek istediğinizde
- Kullanıcıları ve biletleri korumak istediğinizde
- Mevcut veriyi bozmadan güncelleme yaparken

✨ Güvenli:
- **Kullanıcılar korunur**
- **Biletler korunur**
- **Sinemalar korunur**
- Sadece yeni filmler eklenir

📦 Ne yapar:
```
✓ Yeni migration'ları çalıştırır (varsa)
✓ 2024-2025 filmlerini ekler (yeni olanlar)
✓ Yaklaşan filmleri ekler (coming soon)
✓ Mevcut veriyi korur
```

**Kullanım:**
```bash
# Windows
update_database.bat

# Linux/Mac
chmod +x update_database.sh
./update_database.sh
```

---

## 📊 Karşılaştırma

| Özellik | first_setup | update_database |
|---------|-------------|-----------------|
| Veritabanını sıfırlar | ✅ Evet | ❌ Hayır |
| Kullanıcıları siler | ✅ Evet | ❌ Hayır |
| Biletleri siler | ✅ Evet | ❌ Hayır |
| Sinemaları siler | ✅ Evet | ❌ Hayır |
| Yeni film ekler | ✅ Evet | ✅ Evet |
| Coming soon ekler | ✅ Evet | ✅ Evet |
| Güvenli | ⚠️ Dikkatli kullan | ✅ Güvenli |

---

## 🎯 Hangi Script'i Kullanmalıyım?

### İlk Kurulum:
```bash
first_setup.bat
```
Veritabanı yoksa veya sıfırdan başlamak istiyorsanız.

### Sadece Film Güncellemesi:
```bash
update_database.bat
```
Kullanıcılarınız ve biletleriniz varsa, bunları korumak istiyorsanız.

---

## 🔄 Manuel Güncelleme

Sadece belirli bir şeyi güncellemek isterseniz:

### Sadece 2024-2025 Filmleri:
```bash
php artisan db:seed --class=Database\Seeders\Movies\Movies2025Seeder
```

### Sadece Coming Soon Filmler:
```bash
php artisan db:seed --class=Database\Seeders\Movies\FutureMoviesSeeder
```

### Sadece Yeni Seanslar:
```bash
php artisan db:seed --class=Database\Seeders\Showtimes\ShowtimeSeeder
```

---

## 📝 Notlar

1. **TMDB API**: Her iki script de TMDB'den film çeker, internet gerektirir
2. **Süre**: Film çekme ~5-10 dakika sürebilir (rate limit)
3. **Tekrar Çalıştırma**: `update_database` birden fazla çalıştırılabilir, duplicate oluşturmaz
4. **Yedekleme**: `first_setup` çalıştırmadan önce veritabanını yedekleyin!

---

## 🆘 Sorun mu var?

### Script çalışmıyor:
```bash
# Composer'ı güncelle
composer dump-autoload
```

### TMDB API hatası:
- İnternet bağlantınızı kontrol edin
- Birkaç dakika bekleyin (rate limit)

### "Class not found" hatası:
```bash
composer install
composer dump-autoload
```

---

## 📱 Sonra Ne Yapmalıyım?

```bash
# API sunucusunu başlat
php artisan serve

# Flutter uygulamasını çalıştır (başka terminal)
cd ..
flutter run
```

---

✨ **Artık hazırsınız!** 200+ güncel film ve coming soon filmlerle!

