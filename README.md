# Fitness Site Full – Kurulum ve Çalıştırma Rehberi 🏋️‍♂️

Merhaba, bu projeyi localde XAMPP ile ayağa kaldırmak için aşağıdaki adımları izle. Kaslarını değil ama, web sunucunu çalıştıracaksın! 😄

---

## 1. Gerekli Programlar

- [XAMPP](https://www.apachefriends.org/tr/index.html) (PHP 8+ ve MySQL/MariaDB içeren bir sürüm)
- [Composer](https://getcomposer.org/) (PHP bağımlılıkları için, gerekirse)

---

## 2. Proje Dosyalarını Kopyala

- İndirilen `fitness-site-Full` klasörünü, **XAMPP'ın kurulu olduğu dizindeki `htdocs` klasörüne** taşı.

---

## 3. XAMPP’ı Başlat

- **XAMPP Kontrol Paneli**ni aç.
- **Apache** ve **MySQL** servislerini başlat (yeşil ışıklar tamam!).

---

## 4. Veritabanı Kurulumu

1. Tarayıcıdan [http://localhost/phpmyadmin](http://localhost/phpmyadmin) adresine git.
2. Sol üstte **"Yeni"** butonuna tıkla, veritabanı adını (ör: `fitness`) gir ve oluştur.
3. Menüden oluşturduğun veritabanını seç.
4. Üstteki **"İçe Aktar"** sekmesine tıkla.
5. Proje dosyasında gelen `.sql` dosyalarından **en güncel olanı** (`fitness_27-05-2025.sql` gibi) seç ve içe aktar.

---

## 5. Ayar Dosyası (config.php)

1. `config.sample.php` dosyasını **kopyala** ve aynı dizinde `config.php` olarak adlandır.
2. `config.php` içindeki aşağıdaki ayarları kendi local ortamına göre düzenle:

 ```php
 define('DB_SERVER', 'localhost');
 define('DB_USERNAME', 'root');    // XAMPP'da genelde root
 define('DB_PASSWORD', '');        // Şifre yoksa boş bırak
 define('DB_NAME', 'fitness');     // Az önce oluşturduğun veritabanı ismi
 define('BASE_URL', 'http://localhost/fitness-site-Full');
 define('BASE_PATH', __DIR__);
````



##.6 Composer Bağımlılıkları
cd C:\xampp\htdocs\fitness-site-Full

--- 

##7. Siteyi Çalıştır!
composer install
http://localhost/fitness-site-Full/
