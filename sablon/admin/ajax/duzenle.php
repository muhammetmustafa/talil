<?php
	session_start();
	
	require_once '../essek/class.Oturum.php';
	require_once '../essek/sql/class.Mysql.php';
	require_once '../essek/class.Cevap.php';
	
	$cevap = new Cevap();
	$talil = new Mysql("talil");
	
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
		try
		{
			$id = Post::al("test_id");
			Mysql::sql($talil)->select("id")->from("testler")->where( array("id" => $id) )->calistir();
			if ($talil->etkilenenSatirSayisi <= 0)
			{
				//yanlış test id
				throw new exCevap("HATA_TEST", "Verilen test id bulunamadı!");
			}
			
			//Testin Alınması
			Mysql::sql($talil)
			->select("test.etiket, test.aciklama, concat(dosya.dizin, dosya.ad) as src, dosya.genislik, dosya.yukseklik, zorluk.id as zorluk")
			->from("testler as test, dosyalar as dosya, zorluk")->where()
			->filtre("test.id", $id)->ve()->filtre("test.zorluk_id", (object)"zorluk.id")->ve()
			->filtre("dosya.test_id", $id)->ve()->filtre("dosya.durum_id", 2)->ve()->filtre("dosya.ad", "parmak%", "LIKE")->calistir();
			if ($talil->etkilenenSatirSayisi <= 0)
			{
				throw new exCevap("HATA_SORGU", $talil->sonSorgu);
			}
			$test = $talil->sonuc()->alMesaj();
			
			//Soruların alınması
			Mysql::sql($talil)
			->select("soru.id as soru_id, soru.sira_no, soru.soru, soru.dogru_sik, zorluk.id as zorluk")->from("sorular as soru, zorluk")->where()
			->filtre("soru.test_id", $id)->ve()->filtre("soru.zorluk_id", (object)"zorluk.id")->orderby("sira_no ASC")->calistir();
			if ($talil->etkilenenSatirSayisi <= 0)
			{
				throw new exCevap("HATA_SORGU", $talil->sonSorgu);
			}
			$sorular = $talil->sonuc(true)->alMesaj();
			
			//Şıkların alınması
			$gcc_sorular = array();
			foreach ($sorular as $soru)
			{
				if (!($soru["dogru_sik"] == "evet" || $soru["dogru_sik"] == "hayir"))
				{
					Mysql::sql($talil)->select("sik, deger")->from("siklar")->where()->filtre("soru_id", (object)$soru["soru_id"])->calistir();
					if ($talil->etkilenenSatirSayisi <= 0)
					{
						throw new exCevap("HATA_SORGU", $talil->sonSorgu);
					}
					
					$siklar = $talil->sonuc(true)->alMesaj();
					
					foreach ($siklar as $sik)
					{
						$soru["siklar"][] = array("sik" => $sik["sik"], "deger" => $sik["deger"]);
					} 
				}
				$gcc_sorular[] = $soru;
			}
			$sorular = $gcc_sorular;
			
			//Zorluk listesinin alınması
			Mysql::sql($talil)->select("id as deger, etiket as icerik")->from("zorluk")->calistir();
			if ($talil->etkilenenSatirSayisi <= 0)
			{
				throw new exCevap("HATA_SORGU", $talil->sonSorgu);
			}
			$zorluk = $talil->sonuc(true)->alMesaj();
			
			//Düzenleme formunun doldurulması
			ob_start();
			include (Oturum::al('klasorTalil') . "suret_yeni/form_test.phtml");
			$html = ob_get_clean();
				
			$cevap->ekleCevap("html", $html);
			$cevap->ekleCevap("sorular", $sorular);
		} 
		catch (exVeritabani $hata) 
		{
			$cevap->ekleHata("HATA_VERITABANI", $hata->__toString());
		}
		catch (exCevap $hata)
		{
			$cevap->ekleHata($hata->alTur(), $hata->alMesaj());
		}
	}
	
	echo $cevap->alJSON();
?>