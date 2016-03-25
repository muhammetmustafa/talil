<?php
	session_start();
	
	require_once $_SESSION['klasorTalil'] . 'essek/class.Oturum.php';
	require_once Oturum::al('klasorTalil') . 'essek/class.Post.php';
	require_once Oturum::al('klasorTalil') . 'essek/sql/class.Mysql.php';
	require_once Oturum::al('klasorTalil') . 'essek/class.Cevap.php';
	
	$talil = new Mysql("talil");
	$cevap = new Cevap();
	
	if (
		!isset($_POST['test_id']) && 
		giris_yapilmamis() &&
		Oturum::al("yetki") != "admin"
		)
	{
		$cevap->ekleHata("AJAX_ISTEK_HATASI", "Yetkisiz kullanıcı ve giriş yapılmamış"); 
	}
	else
	{
		$id = Post::al("test_id");
		
		try
		{
			Mysql::sql($talil)
			->select("t.etiket, t.aciklama, t.olusturulma_tarihi as tarih, ".
					 "z.etiket AS zetiket, k.kullanici_adi AS olusturan, ".
					 "d.dizin AS resim_dizini, d.ad AS resim_adi, genislik, yukseklik")
			->from("testler AS t, zorluk AS z, kullanicilar As k, dosyalar as d")
			->where()
			->filtre("t.id", $id)->ve()
			->filtre("t.zorluk_id", (object)"z.id")->ve()
			->filtre("t.olusturan_id", (object)"k.id")->ve()
			->filtre("d.test_id", $id)->ve()
			->filtre("d.dizin", "%arama%", "LIKE")->ve()
			->filtre("d.durum_id", 2)
			->calistir();
			
			if ($talil->etkilenenSatirSayisi > 0)
			{
				$sonuc = $talil->sonuc()->alMesaj();
				
				ob_start();
				include (Oturum::al('klasorTalil') . "suret_yeni/test_bilgileri.phtml");
				$html = ob_get_clean();
				
				$cevap->ekleCevap("html", $html);
			}
			else
			{
				$cevap->ekleCevap("html", "");
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