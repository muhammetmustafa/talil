<?php
	session_start();
	
	require_once '../essek/class.Yukleme.php';
	require_once '../essek/class.Oturum.php';
	require_once '../essek/kutuphane.tarih.php';
	require_once '../essek/class.Resim.php';
	require_once '../essek/class.Cevap.php';
	require_once '../essek/class.Dosya.php';
	require_once '../essek/sql/class.Mysql.php';
	
	$talil = new Mysql("talil");
	$cevap = new Cevap();
	
	if (  giris_yapilmamis() &&
	      Oturum::al("yetki") != "admin"
	   )
	{
		$cevap->ekleHata("AJAX_ISTEK_HATASI", "Yetkisiz kullanıcı ve giriş yapılmamış");
	}
	else
	{
		try
		{
			$dizinTalil = Oturum::al("klasorTalil");
			$dizinOrjinal = "img/yuklemeler_gecici/orjinal/";
			$dizinParmak = "img/yuklemeler_gecici/parmak/";
			$dizinArama = "img/yuklemeler_gecici/arama/";
			
			$tarihDamgaDosya = dosyaSimdi();
			$tarihDamgaVeritabani = veritabaniSimdi();
			
			$yukleme = new Yukleme("yeniTestResmi");
			$yukleme
			->ataUzantilar( array("jpg", "jpeg") )
			->ataMIME( array("image/jpg", "image/jpeg") )
			->ataHedefKlasor($dizinTalil.$dizinOrjinal)
			->ataAd( sprintf("%d__%s%s", Oturum::al("id"), $tarihDamgaDosya, $yukleme->alUzantiNoktali()) )
			->yuklemeyiTamamla();
			
			$kucukResim = new Resim($yukleme->alHedefDosyaYolu());
			$kucukResim->orantiliKucult(150, 220)
			->ataKlasor($dizinTalil.$dizinParmak)
			->ataAd( sprintf("parmak__%d__%s%s", Oturum::al("id"), $tarihDamgaDosya, $yukleme->alUzantiNoktali()) )
			->kaydet();
			
			$aramaResim = new Resim($yukleme->alHedefDosyaYolu());
			$aramaResim->orantiliKucult(75, 110)
			->ataKlasor($dizinTalil.$dizinArama)
			->ataAd( sprintf("arama__%d__%s%s", Oturum::al("id"), $tarihDamgaDosya, $yukleme->alUzantiNoktali()))
			->kaydet();
			
			//Geçici resimlerin bilgilerini veritabanına kaydet
			//tur_id = Resim, jpeg
			//durum_id = Geçici
			//degerlerPOST = false; değerleri post dan alma
			Mysql::sql($talil)
			->insert("dosyalar")
			->sutunlar("yukleyen_id, tur_id, kok_dizin, dizin, ad, yukseklik, genislik, durum_id, eklenme_tarihi")
			->degerler( array(Oturum::al("id"), 1, $dizinTalil, $dizinOrjinal, 
								$yukleme->alAd(), $yukleme->alYukseklik(), $yukleme->alGenislik(), 1, $tarihDamgaVeritabani) )
			->calistir();
			
			Mysql::sql($talil)
			->insert("dosyalar")
			->sutunlar("yukleyen_id, tur_id, kok_dizin, dizin, ad, yukseklik, genislik, durum_id, eklenme_tarihi")
			->degerler( array(Oturum::al("id"), 1, $dizinTalil, $dizinParmak, 
								$kucukResim->alAd(), $kucukResim->alYukseklik(), $kucukResim->alGenislik(), 1, $tarihDamgaVeritabani) )
			->calistir();
			
			Mysql::sql($talil)
			->insert("dosyalar")
			->sutunlar("yukleyen_id, tur_id, kok_dizin, dizin, ad, yukseklik, genislik, durum_id, eklenme_tarihi")
			->degerler( array(Oturum::al("id"), 1, $dizinTalil, $dizinArama,
								$aramaResim->alAd(), $aramaResim->alYukseklik(), $aramaResim->alGenislik(), 1, $tarihDamgaVeritabani) )
			->calistir();
			
			$cevap->ekleCevap("kucukresim", $dizinParmak.$kucukResim->alAd());
			$cevap->ekleCevap("yukseklik", $kucukResim->alYukseklik());
			$cevap->ekleCevap("genislik", $kucukResim->alGenislik());

		} 
		catch (exVeritabani $hata) 
		{
			$cevap->ekleHata("VERITABANI_HATASI", $hata->__toString());
		}
		catch (exYukleme $hata)
		{
			$cevap->ekleHata("YUKLEME_HATASI", $hata->__toString());
		}
		catch (exResim $hata)
		{
			//Eğer küçük resim oluşturulamadıysa büyük resmi sil
			Dosya::sil($yukleme->alHedefKlasor().$yukleme->alAd());
			
			$cevap->ekleHata("KUCUK_RESIM_OLUSTURMA_HATASI", $hata->__toString());
		}
	}
	
	echo $cevap->alJSON();
?>