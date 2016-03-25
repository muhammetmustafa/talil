-- phpMyAdmin SQL Dump
-- version 4.1.12
-- http://www.phpmyadmin.net
--
-- Anamakine: 127.0.0.1
-- Üretim Zamanı: 27 Ara 2014, 10:35:32
-- Sunucu sürümü: 5.6.16
-- PHP Sürümü: 5.5.11

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Veritabanı: `talil`
--

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `dosyalar`
--

CREATE TABLE IF NOT EXISTS `dosyalar` (
  `id` int(15) NOT NULL AUTO_INCREMENT,
  `yukleyen_id` int(10) NOT NULL,
  `test_id` int(10) DEFAULT NULL,
  `tur_id` int(5) NOT NULL,
  `kok_dizin` varchar(250) COLLATE utf8_turkish_ci NOT NULL,
  `dizin` varchar(300) COLLATE utf8_turkish_ci NOT NULL,
  `ad` varchar(100) COLLATE utf8_turkish_ci NOT NULL,
  `genislik` int(10) NOT NULL,
  `yukseklik` int(10) NOT NULL,
  `durum_id` int(5) NOT NULL,
  `eklenme_tarihi` varchar(100) COLLATE utf8_turkish_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `test_id` (`test_id`),
  KEY `tur_id` (`tur_id`),
  KEY `durum` (`durum_id`),
  KEY `yukleyen_id` (`yukleyen_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci AUTO_INCREMENT=630 ;

--
-- Tablo döküm verisi `dosyalar`
--

INSERT INTO `dosyalar` (`id`, `yukleyen_id`, `test_id`, `tur_id`, `kok_dizin`, `dizin`, `ad`, `genislik`, `yukseklik`, `durum_id`, `eklenme_tarihi`) VALUES
(609, 3, 117, 1, 'D:/xampp/htdocs/talil/', 'null', '3__16092014173723.jpg', 1944, 2592, 3, '16/09/2014 17:37:23'),
(610, 3, 117, 1, 'D:/xampp/htdocs/talil/', 'null', 'parmak__3__16092014173723.jpg', 150, 200, 3, '16/09/2014 17:37:23'),
(611, 3, 117, 1, 'D:/xampp/htdocs/talil/', 'null', 'arama__3__16092014173723.jpg', 75, 100, 3, '16/09/2014 17:37:23'),
(624, 3, 117, 1, 'D:/xampp/htdocs/talil/', 'img/yuklemeler/orjinal/', '3__20092014113054.jpg', 302, 530, 2, '20/09/2014 11:30:54'),
(625, 3, 117, 1, 'D:/xampp/htdocs/talil/', 'img/yuklemeler/parmak/', 'parmak__3__20092014113054.jpg', 125, 220, 2, '20/09/2014 11:30:54'),
(626, 3, 117, 1, 'D:/xampp/htdocs/talil/', 'img/yuklemeler/arama/', 'arama__3__20092014113054.jpg', 63, 110, 2, '20/09/2014 11:30:54'),
(627, 3, 119, 1, 'D:/xampp/htdocs/talil/', 'img/yuklemeler/orjinal/', '3__20092014113134.jpg', 594, 654, 2, '20/09/2014 11:31:34'),
(628, 3, 119, 1, 'D:/xampp/htdocs/talil/', 'img/yuklemeler/parmak/', 'parmak__3__20092014113134.jpg', 150, 165, 2, '20/09/2014 11:31:34'),
(629, 3, 119, 1, 'D:/xampp/htdocs/talil/', 'img/yuklemeler/arama/', 'arama__3__20092014113134.jpg', 75, 83, 2, '20/09/2014 11:31:34');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `dosya_durumlari`
--

CREATE TABLE IF NOT EXISTS `dosya_durumlari` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `durum` varchar(50) COLLATE utf8_turkish_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci AUTO_INCREMENT=6 ;

--
-- Tablo döküm verisi `dosya_durumlari`
--

INSERT INTO `dosya_durumlari` (`id`, `durum`) VALUES
(1, 'Geçici'),
(2, 'Kalıcı'),
(3, 'Silinmiş'),
(4, 'Şikayetli'),
(5, 'Değiştirilme Aşamasında');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `dosya_turleri`
--

CREATE TABLE IF NOT EXISTS `dosya_turleri` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `tur` varchar(100) COLLATE utf8_turkish_ci NOT NULL,
  `uzanti` varchar(100) COLLATE utf8_turkish_ci NOT NULL,
  `aciklama` varchar(150) COLLATE utf8_turkish_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci AUTO_INCREMENT=7 ;

--
-- Tablo döküm verisi `dosya_turleri`
--

INSERT INTO `dosya_turleri` (`id`, `tur`, `uzanti`, `aciklama`) VALUES
(1, 'Resim', 'jpg', 'Resim Dosyası'),
(2, 'Resim', 'jpeg', 'Resim Dosyası'),
(3, 'Resim', 'png', 'Resim Dosyası'),
(4, 'Resim', 'gif', 'Resim Dosyası'),
(5, 'Video', 'avi', 'Video dosyası'),
(6, 'Video', 'mpg', 'Video Dosyası');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `istatistikler`
--

CREATE TABLE IF NOT EXISTS `istatistikler` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `test_id` int(9) NOT NULL,
  `istatistik_id` int(4) NOT NULL,
  `deger` int(30) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `kullanicilar`
--

CREATE TABLE IF NOT EXISTS `kullanicilar` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `yetki` varchar(20) COLLATE utf8_turkish_ci NOT NULL,
  `kullanici_adi` varchar(250) COLLATE utf8_turkish_ci NOT NULL,
  `sifre` varchar(40) COLLATE utf8_turkish_ci NOT NULL,
  `ad` varchar(75) COLLATE utf8_turkish_ci DEFAULT NULL,
  `soyad` varchar(50) COLLATE utf8_turkish_ci DEFAULT NULL,
  `email` varchar(250) COLLATE utf8_turkish_ci DEFAULT NULL,
  `adres` varchar(200) COLLATE utf8_turkish_ci DEFAULT NULL,
  `telefon` varchar(15) COLLATE utf8_turkish_ci DEFAULT NULL,
  `dogum_tarihi` varchar(10) COLLATE utf8_turkish_ci DEFAULT NULL,
  `resim` varchar(250) COLLATE utf8_turkish_ci DEFAULT NULL,
  `puan` int(15) NOT NULL,
  `seviye` int(3) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `seviye` (`seviye`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci AUTO_INCREMENT=10 ;

--
-- Tablo döküm verisi `kullanicilar`
--

INSERT INTO `kullanicilar` (`id`, `yetki`, `kullanici_adi`, `sifre`, `ad`, `soyad`, `email`, `adres`, `telefon`, `dogum_tarihi`, `resim`, `puan`, `seviye`) VALUES
(3, 'admin', 'ahmet', '123456', NULL, NULL, 'ahmet@ahmet.com', 'aeaea', NULL, NULL, NULL, 0, 3),
(4, 'kullanıcı', 'ali', '626243121', NULL, NULL, 'ali@ali.com', 'aeaea', NULL, NULL, NULL, 0, 1),
(5, 'kullanıcı', 'ahmeti', 'eauiea', NULL, NULL, 'ali@gamem.com', NULL, NULL, NULL, NULL, 0, 1),
(6, 'kullanıcı', 'ahmetiee', 'eauiea', NULL, NULL, 'ali@gameem.com', NULL, NULL, NULL, NULL, 0, 1),
(7, 'kullanıcı', '33aei', 'ug22eğma', NULL, NULL, '2ae@gmal.com', NULL, NULL, NULL, NULL, 0, 1),
(8, 'kullanıcı', 'uieagğa', 'uliemalmakg', NULL, NULL, 'uielma@gmail.com', NULL, NULL, NULL, NULL, 0, 1),
(9, 'admin', '11m2m', '321', NULL, NULL, 'amem@mg22m.com', NULL, NULL, NULL, NULL, 0, 1);

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `seviyeler`
--

CREATE TABLE IF NOT EXISTS `seviyeler` (
  `id` int(4) NOT NULL AUTO_INCREMENT,
  `seviye` varchar(30) COLLATE utf8_turkish_ci NOT NULL,
  `esik_degeri` int(15) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci AUTO_INCREMENT=8 ;

--
-- Tablo döküm verisi `seviyeler`
--

INSERT INTO `seviyeler` (`id`, `seviye`, `esik_degeri`) VALUES
(1, 'Yeni Eleman', 0),
(2, 'Çırak', 1000),
(3, 'Mahir', 50000),
(4, 'Usta', 100000),
(5, 'Watson', 500000),
(6, 'Sherlock', 10000000),
(7, 'Çekirge', 25000);

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `siklar`
--

CREATE TABLE IF NOT EXISTS `siklar` (
  `id` int(15) NOT NULL AUTO_INCREMENT,
  `soru_id` int(10) NOT NULL,
  `sik` varchar(3) COLLATE utf8_turkish_ci NOT NULL,
  `deger` varchar(200) COLLATE utf8_turkish_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `soru_id` (`soru_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci AUTO_INCREMENT=175 ;

--
-- Tablo döküm verisi `siklar`
--

INSERT INTO `siklar` (`id`, `soru_id`, `sik`, `deger`) VALUES
(161, 145, 'A', 'dondurma'),
(162, 145, 'B', 'çikolata'),
(163, 145, 'C', 'hamur'),
(164, 145, 'D', 'it'),
(165, 147, 'A', 'kamera'),
(166, 147, 'B', 'kendisi'),
(167, 147, 'C', 'götünü gıdıklıyorlar'),
(168, 147, 'D', 'ne biliyim'),
(169, 148, 'A', '10'),
(170, 148, 'B', '20'),
(171, 148, 'C', '2'),
(172, 148, 'D', '15'),
(173, 149, 'A', 'babasının evinde'),
(174, 149, 'B', 'anasının evinde');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `sorular`
--

CREATE TABLE IF NOT EXISTS `sorular` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `test_id` int(10) NOT NULL,
  `sira_no` int(3) NOT NULL,
  `soru` varchar(300) COLLATE utf8_turkish_ci NOT NULL,
  `olusturulma_tarihi` varchar(100) COLLATE utf8_turkish_ci NOT NULL,
  `dogru_sik` varchar(5) COLLATE utf8_turkish_ci NOT NULL COMMENT 'Doğru Şık',
  `zorluk_id` int(3) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `test_id` (`test_id`),
  KEY `zorluk_derecesi_id` (`zorluk_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci AUTO_INCREMENT=152 ;

--
-- Tablo döküm verisi `sorular`
--

INSERT INTO `sorular` (`id`, `test_id`, `sira_no`, `soru`, `olusturulma_tarihi`, `dogru_sik`, `zorluk_id`) VALUES
(145, 119, 1, 'ağzındaki nedir', '20/09/2014 22:07:13', 'C', 2),
(146, 119, 2, '8 yaşındadır', '20/09/2014 22:07:13', 'hayir', 2),
(147, 119, 3, 'niye gülüyor', '20/09/2014 22:07:13', 'B', 2),
(148, 119, 4, 'kolları kaç cm', '20/09/2014 22:07:13', 'B', 3),
(149, 117, 1, 'oda neresidir', '14/10/2014 20:47:06', 'A', 1),
(150, 117, 2, 'ayakkabısı var mı', '14/10/2014 20:47:06', 'hayir', 1),
(151, 117, 3, 'dişleri fırçalı mı', '14/10/2014 20:47:06', 'evet', 1);

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `testler`
--

CREATE TABLE IF NOT EXISTS `testler` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `olusturan_id` int(10) NOT NULL,
  `zorluk_id` int(3) NOT NULL,
  `etiket` varchar(100) COLLATE utf8_turkish_ci DEFAULT NULL,
  `aciklama` varchar(300) COLLATE utf8_turkish_ci DEFAULT NULL,
  `olusturulma_tarihi` varchar(100) COLLATE utf8_turkish_ci DEFAULT NULL,
  `kategori_id` int(4) NOT NULL,
  `cozulme_sayisi` int(30) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `etiket` (`etiket`),
  KEY `olusturan_id` (`olusturan_id`),
  KEY `zorluk_derecesi_id` (`zorluk_id`),
  KEY `kategori_id` (`kategori_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci AUTO_INCREMENT=120 ;

--
-- Tablo döküm verisi `testler`
--

INSERT INTO `testler` (`id`, `olusturan_id`, `zorluk_id`, `etiket`, `aciklama`, `olusturulma_tarihi`, `kategori_id`, `cozulme_sayisi`) VALUES
(117, 3, 5, 'nebi', 'çalışkan', '16/09/2014 17:37:56', 1, 0),
(119, 3, 4, 'ibo', 'etyemez', '20/09/2014 11:32:11', 1, 0);

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `test_kategorileri`
--

CREATE TABLE IF NOT EXISTS `test_kategorileri` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `kategori` varchar(50) COLLATE utf8_turkish_ci NOT NULL,
  `ust_kategori_id` int(5) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `ust_kategori_id` (`ust_kategori_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci AUTO_INCREMENT=9 ;

--
-- Tablo döküm verisi `test_kategorileri`
--

INSERT INTO `test_kategorileri` (`id`, `kategori`, `ust_kategori_id`) VALUES
(1, 'Genel', 0),
(2, 'Sanat', 0),
(3, 'Politika', 0),
(4, 'Siyaset', 0),
(5, 'Müzik', 0),
(6, 'Film', 0),
(7, 'Tarih', 0),
(8, 'Bilim', 0);

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `turler`
--

CREATE TABLE IF NOT EXISTS `turler` (
  `id` int(3) NOT NULL AUTO_INCREMENT,
  `aciklama` varchar(50) COLLATE utf8_turkish_ci NOT NULL,
  `sik_sayisi` int(3) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci COMMENT='Şık Türleri' AUTO_INCREMENT=6 ;

--
-- Tablo döküm verisi `turler`
--

INSERT INTO `turler` (`id`, `aciklama`, `sik_sayisi`) VALUES
(1, 'Evet/Hayır', 2),
(2, '2 Şıklı', 2),
(3, '3 Şıklı', 3),
(4, '4 Şıklı', 4),
(5, '5 Şıklı', 5);

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `zorluk`
--

CREATE TABLE IF NOT EXISTS `zorluk` (
  `id` int(3) NOT NULL AUTO_INCREMENT,
  `etiket` varchar(100) COLLATE utf8_turkish_ci NOT NULL,
  `aciklama` varchar(300) COLLATE utf8_turkish_ci NOT NULL,
  `deger` int(5) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci AUTO_INCREMENT=8 ;

--
-- Tablo döküm verisi `zorluk`
--

INSERT INTO `zorluk` (`id`, `etiket`, `aciklama`, `deger`) VALUES
(1, 'Çok Basit', '', 1),
(2, 'Basit', '', 2),
(3, 'Zor', '', 3),
(4, 'Çok Zor', '', 4),
(5, 'Deha', '', 5),
(6, 'Watson', '', 90),
(7, 'Sherlock', '', 100);

--
-- Dökümü yapılmış tablolar için kısıtlamalar
--

--
-- Tablo kısıtlamaları `dosyalar`
--
ALTER TABLE `dosyalar`
  ADD CONSTRAINT `dosyalar_dosyadurumlari` FOREIGN KEY (`durum_id`) REFERENCES `dosya_durumlari` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `dosyalar_dosyaturleri` FOREIGN KEY (`tur_id`) REFERENCES `dosya_turleri` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `dosyalar_kullanicilar` FOREIGN KEY (`yukleyen_id`) REFERENCES `kullanicilar` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `dosyalar_testler` FOREIGN KEY (`test_id`) REFERENCES `testler` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Tablo kısıtlamaları `kullanicilar`
--
ALTER TABLE `kullanicilar`
  ADD CONSTRAINT `seviye_seviyeler` FOREIGN KEY (`seviye`) REFERENCES `seviyeler` (`id`);

--
-- Tablo kısıtlamaları `siklar`
--
ALTER TABLE `siklar`
  ADD CONSTRAINT `siklar__sorular` FOREIGN KEY (`soru_id`) REFERENCES `sorular` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Tablo kısıtlamaları `sorular`
--
ALTER TABLE `sorular`
  ADD CONSTRAINT `sorular_zorluk` FOREIGN KEY (`zorluk_id`) REFERENCES `zorluk` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `sorular__testler` FOREIGN KEY (`test_id`) REFERENCES `testler` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Tablo kısıtlamaları `testler`
--
ALTER TABLE `testler`
  ADD CONSTRAINT `testler__kullanicilar` FOREIGN KEY (`olusturan_id`) REFERENCES `kullanicilar` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `testler__zorluk` FOREIGN KEY (`zorluk_id`) REFERENCES `zorluk` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `test_kategori` FOREIGN KEY (`kategori_id`) REFERENCES `test_kategorileri` (`id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
