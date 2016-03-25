<?php
	session_start();
	
	require_once $_SESSION['klasorTalil'] . 'essek/class.Oturum.php';
	require_once Oturum::al('klasorTalil') . 'essek/class.Post.php';
	require_once Oturum::al('klasorTalil') . 'essek/sql/class.Mysql.php';
	require_once Oturum::al('klasorTalil') . 'essek/class.Dosya.php';
	require_once Oturum::al('klasorTalil') . 'essek/class.Mesaj.php';
	require_once Oturum::al('klasorTalil') . 'essek/class.Cevap.php';
	
	define('MAKS_SAYFA', 5);
	define('MAKS_SONUC', 5);
	
	$talil = new Mysql("talil");
	$cevap = new Cevap();
	
	if (
		!isset($_POST['parametreler']) && 
		giris_yapilmamis() &&
		Oturum::al("yetki") != "admin"
		)
	{
		$cevap->ekleHata("AJAX_ISTEK_HATASI", "Yetkisiz kullanıcı ve giriş yapılmamış"); 
	}
	else
	{
		$test = json_decode(Post::al("parametreler"));
		
		if (!isset($test->sayfa))
		{
			$test->sayfa = 1;
		}
		
		try
		{
			Mysql::sql($talil)
			->select("count(t.id) as sonuc_miktari")
			->from("testler AS t, zorluk AS z, kullanicilar As k, dosyalar as d")
			->where()
			->filtre("t.etiket", "%".$test->etiket."%", "LIKE")->ve()
			->filtre("t.zorluk_id", (object)"z.id")->ve()
			->filtre("t.olusturan_id", (object)"k.id")->ve()
			->filtre("d.test_id", (object)"t.id")->ve()
			->filtre("d.dizin", "%arama%", "LIKE")->ve()
			->filtre("d.durum_id", 2)
			->calistir();
			
			if ($talil->etkilenenSatirSayisi > 0)
			{
				$sonuc_miktari = $talil->sonuc()->alMesaj()["sonuc_miktari"];
				
				if ($test->sayfa == 'en_son')
				{
					$test->sayfa = ceil($sonuc_miktari / MAKS_SONUC);
				}
			}
			
			Mysql::sql($talil)
			->select("t.id AS testID, t.etiket, t.aciklama, t.olusturulma_tarihi as tarih, ".
					 "z.id AS zno, z.etiket AS zetiket, k.kullanici_adi AS olusturan, ".
					 "d.dizin AS resim_dizini, d.ad AS resim_adi, genislik, yukseklik")
			->from("testler AS t, zorluk AS z, kullanicilar As k, dosyalar as d")
			->where()
			->filtre("t.etiket", "%".$test->etiket."%", "LIKE")->ve()
			->filtre("t.zorluk_id", (object)"z.id")->ve()
			->filtre("t.olusturan_id", (object)"k.id")->ve()
			->filtre("d.test_id", (object)"t.id")->ve()
			->filtre("d.dizin", "%arama%", "LIKE")->ve()
			->filtre("d.durum_id", 2)
			->orderby("testID")
			->limit(($test->sayfa - 1) * MAKS_SONUC, MAKS_SONUC)
			->calistir();
			
			if ($talil->etkilenenSatirSayisi > 0)
			{
				$sonuc = $talil->sonuc(true);
				
				if ($sonuc->alHataMiktari() <= 0)
				{
					$sonuc = $sonuc->alMesaj();
					
					ob_start();
					include (Oturum::al('klasorTalil') . "suret_yeni/form_sonuc.phtml");
					$html = ob_get_clean();
					
					$cevap->ekleCevap("html", $html);
					$cevap->ekleCevap("sonucmiktari", $talil->etkilenenSatirSayisi);
				}
				else
				{
					$cevap->ekleHata("HATA_SONUC", "Sonuç almada hata oluştu");
				}
			}
			else
			{
				$cevap->ekleCevap("sonucmiktari", 0);
			}
		}
		catch (exDosya $hata)
		{
			$cevap->ekleHata("DOSYA_HATASI", $hata->__toString());
		}
		catch (exVeritabani $hata)
		{
			$cevap->ekleHata("VERITABANI_HATASI", $hata->__toString());
		}
			
	}
	
	echo $cevap->alJSON();
?>