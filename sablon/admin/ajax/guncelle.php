<?php
	session_start();
	
	require_once '../essek/class.Oturum.php';
	require_once '../essek/sql/class.Mysql.php';
	require_once '../essek/class.Cevap.php';
	require_once '../essek/kutuphane.tarih.php';
	
	$cevap = new Cevap();
	$talil = new Mysql("talil");
	
	if (
		!isset($_POST['test']) && 
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
			$test = json_decode(Post::al("test"));
			if ($test == null)
			{
				throw new exCevap("AJAX_ISTEK_HATASI", "Hatalı JSON isteği");
			}
			
			Mysql::sql($talil)->select("id")->from("testler")->where( array("id" => $test->id) )->calistir();
			if ($talil->etkilenenSatirSayisi <= 0)
			{
				//yanlış test id
				throw new exCevap("HATA_TEST", "Verilen test id bulunamadı!");
			}
			
			$talil->transaBasla();
			
			//TODO: Maalesef güncellemenin gerçekleşip gerçekleşemediğini kesin olarak öğrenemiyoruz. 
			//onun için transaction işlemi sağlam gerçekleştirilemeyebilir.
			//Testi güncelle
			Mysql::sql($talil)
			->update("testler")->set( array( 'etiket' => $test->etiket, 'aciklama' => $test->aciklama, 'zorluk_id' => $test->zorluk ) )
			->where()->filtre("id", $test->id)->calistir();
			
			//Soruları ve şıklarını sil (on delete:cascade) sil
			Mysql::sql($talil)
			->delete()->from('sorular')->where( array('test_id' =>  $test->id) )->calistir();
			if ($talil->etkilenenSatirSayisi <= 0)
			{
				$talil->gerial();
				
				throw new exCevap("HATA_TEST", "Soru/şık silme başarısız.");
			}
			
			//Yeni soruları ve şıklarını ekle
			$soru_no = 1;
			$simdi = veritabaniSimdi();
			foreach ($test->sorular as $soru)
			{
				Mysql::sql($talil)
				->insert("sorular")->sutunlar("test_id, sira_no, soru, olusturulma_tarihi, dogru_sik, zorluk_id")
				->degerler( array($test->id, $soru_no++, $soru->soru, $simdi, $soru->dogru_sik, $soru->zorluk) )->calistir();
				if ($talil->etkilenenSatirSayisi <= 0)
				{
					$talil->gerial();
					throw new exCevap("HATA_TEST", "Soru eklenemedi");
				}
				
				$soru_id = $talil->sonEklenen;	
				if (isset($soru->siklar))
				{
					foreach ($soru->siklar as $sik)
					{
						if (!isset($sik->deger))
						{
							$sik->deger = "";
						}
							
						Mysql::sql($talil)
						->insert("siklar")->sutunlar("soru_id, sik, deger")->degerler( array($soru_id, $sik->sik, $sik->deger))->calistir();
						if ($talil->etkilenenSatirSayisi <= 0)
						{
							$talil->gerial();
							throw new exCevap("HATA_TEST", "Şık eklenemedi");
						}
					}
				}
			}
			
			//Test için yeni resim eklenmiş mi bulalım. Eklenmişse gerekli prosedürleri gerçekleştirelim.
			Mysql::sql($talil)
			->select("id")->from("dosyalar")->where()
			->filtre("test_id", $test->id)->ve()->filtre("concat(dizin,ad)", $test->resim)->ve()->filtre("durum_id", 5)->calistir();
			//Güncellenecek resim var ise
			if ($talil->etkilenenSatirSayisi > 0)
			{
				//Testin eski resimlerini bul
				Mysql::sql($talil)
				->select("kok_dizin, dizin, ad")->from("dosyalar")->where()->filtre("test_id", $test->id)->ve()->filtre("durum_id", 2)->calistir();
				if ($talil->etkilenenSatirSayisi <= 0)
				{	
					throw new exCevap('HATA_TEST', 'Testin eski resimlerini bulma başarısız');
				}
				
				$eski_resimler = $talil->sonuc(true)->alMesaj(); //Dönüş türü Mesaj
		
				foreach ($eski_resimler as $eski_resim)
				{
					$dosya = $eski_resim["kok_dizin"].$eski_resim["dizin"].$eski_resim["ad"];
					
					//Bütün eski resimleri sunucudan sil ve veritabanındaki durumlarını silinmiş olarak ata,
					//yalnız veritabanından kaldırma.
					Dosya::sil($dosya);
					Mysql::sql($talil)
					->update("dosyalar")->set( array("durum_id"=>3, "dizin"=>"null") )->where()
					->filtre("test_id", $test->id)->ve()->filtre("durum_id", 2)->ve()->filtre("ad", $eski_resim["ad"])->calistir();
				}
				
				//Testin yeni resimlerini bul
				Mysql::sql($talil)
				->select("kok_dizin, dizin, ad")->from("dosyalar")->where()->filtre("test_id", $test->id)->ve()->filtre("durum_id", 5)->calistir();
				if ($talil->etkilenenSatirSayisi <= 0)
				{
					throw new exCevap('HATA_TEST', 'Testin yeni resimlerini bulma başarısız.');
				}
				
				$yeni_resimler = $talil->sonuc(true)->alMesaj(); //Dönüş türü Mesaj
				foreach ($yeni_resimler as $yeni_resim)
				{
					$kaynak = $yeni_resim["kok_dizin"].$yeni_resim["dizin"].$yeni_resim["ad"];
					$hedef = str_replace("yuklemeler_gecici", "yuklemeler", $kaynak);
						
					Dosya::tasi($kaynak, $hedef);	
					Mysql::sql($talil)
					->update("dosyalar")->set( array("durum_id"=>2, "dizin"=>str_replace("yuklemeler_gecici", "yuklemeler", $yeni_resim["dizin"])) )
					->where()->filtre("test_id", $test->id)->ve()->filtre("durum_id", 5)->ve()->filtre("ad", $yeni_resim["ad"])->calistir();
				}
				
				//Güncellenen resmin yeni uri'sini bulalım. Cevap olarak gönderilecek.
				Mysql::sql($talil)
				->select("concat(dizin,ad) as resim, yukseklik, genislik")->from("dosyalar")
				->where()->filtre("test_id", $test->id)->ve()->filtre("durum_id", 2)->ve()->filtre("ad","parmak%","LIKE")->calistir();
				if ($talil->etkilenenSatirSayisi <= 0)
				{
					throw new exCevap('HATA_TEST', 'Yeni resimleri bulma başarısız.');
				}
				
				$sonuc = $talil->sonuc()->alMesaj();
					
				$cevap->ekleCevap("resim", $sonuc["resim"]);
				$cevap->ekleCevap("yukseklik", $sonuc["yukseklik"]);
				$cevap->ekleCevap("genislik", $sonuc["genislik"]);
				
			}//Resim güncelleme if'i
			
			$talil->komit();
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