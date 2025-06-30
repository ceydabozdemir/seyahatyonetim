# Seyahat ve Gider Yönetim Sistemi

Bu proje, seyahat giderlerini takip etmek ve yönetmek için geliştirilmiş bir Laravel tabanlı web uygulamasıdır. Kullanıcılar, seyahat planlarını oluşturabilir, giderlerini kaydedebilir ve raporlar oluşturabilir. Yönetim paneli üzerinden tüm işlemler kolayca gerçekleştirilebilir.

## 📋 Proje Özellikleri
- Seyahat planı oluşturma ve düzenleme
- Gider kategorileri ve detaylı gider takibi
- Admin paneli üzerinden kullanıcı ve veri yönetimi
- Raporlama ve analiz araçları


## 🔧 Geliştirme Ortamı
Projenin geliştirilmesi ve çalıştırılması için aşağıdaki araçlar kullanılmıştır:
- **Laravel**: 12.10.2
- **PHP**: 8.2.12
- **Composer**: 2.8.6
- **MySQL**: XAMPP üzerinden v3.3.0 (MariaDB 10.4.32)
- **Navicat Premium**: 17.1.12 – Enterprise
- **IDE**: PhpStorm 2024.3.3
- **Web Sunucusu**: Apache (XAMPP)
- **İşletim Sistemi**: Windows 10

**Not**: PHP 8.2.12 ve Laravel 12 uyumluluğu için bu sürümlerin kullanılması önerilir.

## 🚀 Kurulum Adımları

### 1. XAMPP Kurulumu
1. [XAMPP](https://www.apachefriends.org/download.html) uygulamasını indirin (PHP 8.2.12 sürümüyle uyumlu).
2. XAMPP’ı kurun ve **Apache** ile **MySQL** servislerini başlatın.
3. PHP sürümünün 8.2.12 olduğundan emin olun (`php -v` komutuyla kontrol edebilirsiniz).
4. Eğer Apache veya MySQL başlatılamıyorsa, port çakışmalarını kontrol edin:
   - XAMPP Kontrol Paneli’nde **Apache** satırındaki `Config` > `Apache (httpd.conf)` dosyasını açın.
   - `Listen 80` satırını `Listen 8080` olarak değiştirin.
   - `ServerName localhost:80` satırını `ServerName localhost:8080` olarak değiştirin.
   - Değişiklikleri kaydedip servisleri yeniden başlatın.

   

### 2. Proje Dosyalarını Hazırlama
1. Proje dosyasını bir dizine çıkarın (örneğin: `C:\xampp\htdocs\seyahatyonetim`).




### 4. Composer Kurulumu ve Kullanımı
- **Gerekli Araç**: Composer 2.8.6
- **Kurulum**:
  1. [Composer resmi sitesinden](https://getcomposer.org/download/) `Composer-Setup.exe` dosyasını indirin ve kurun.
  2. Kurulum sırasında PHP yolunu doğru seçin: `C:\xampp\php\php.exe`.
  3. Kurulumdan sonra terminalde `composer --version` ile kontrol edin.
- **Manuel Çalıştırma**:
  - Eğer Composer global olarak tanınmıyorsa:
    1. Proje dizinine `composer.phar` dosyasını indirin:
       ```bash
       php -r "copy('https://getcomposer.org/composer.phar', 'composer.phar');"
       ```
    2. Komutları şu şekilde çalıştırın:
       ```bash
       php composer.phar install
       php composer.phar update
       ```
- **Hata: "composer is not recognized"**:
  - `C:\Users\<KullanıcıAdınız>\AppData\Roaming\Composer\vendor\bin` dizinini sistem PATH’ine ekleyin.
  - Alternatif olarak, `php composer.phar install` komutunu kullanın.
- **PHP Yolu Sorunu**:
  - `php` komutu tanınmıyorsa, `C:\xampp\php` dizinini PATH’e ekleyin.




### 5. Bağımlılıkları Yükleme
Proje dizinine terminal üzerinden gidin ve aşağıdaki komutları sırayla çalıştırın:
```bash
cd C:\xampp\htdocs\seyahatyonetim
composer install  # Laravel bağımlılıklarını yükler
composer update   # Bağımlılıkları günceller
php artisan key:generate  # Uygulama anahtarını üretir



### 6..env Dosyasını Düzenleme

.env dosyasını açın ve aşağıdaki ayarları yapın:

APP_NAME="Seyahat Giderleri Takip ve Yönetim Sistemi"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=seyahatyonetim
DB_USERNAME=root
DB_PASSWORD=

EXCHANGE_RATE_API_KEY=0a42a99154d667ff95011365



6. Veritabanı Migration ve Seed
Veritabanı tablolarını oluşturmak için:

php artisan migrate  # Migration dosyalarını çalıştırır
php artisan db:seed  # Örnek veri ekler (varsa)

php artisan serve

Admin paneline erişmek için: http://127.0.0.1:8000/admin

Varsayılan admin giriş bilgileri:
Kullanıcı Adı: admin@gmail.com
Şifre: 12345678

8. Önbellek Temizleme
Gerekirse önbelleği temizlemek için:

php artisan config:clear
php artisan cache:clear
php artisan view:clear



php artisan serve