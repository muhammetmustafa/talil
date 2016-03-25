<?php
	session_start();
	
	require_once '../essek/class.Oturum.php';
	require_once '../essek/kutuphane.guvenlik.php';
	require_once '../essek/class.Post.php';
	require_once '../essek/kutuphane.tarih.php';
	require_once '../essek/class.Dosya.php';
	require_once '../essek/class.Cevap.php';
	require_once '../essek/class.Mesaj.php';
	require_once '../essek/sql/class.Mysql.php';
	
	$cevap = new Cevap();
	$talil = new Mysql("talil");
	
	if (
		!isset($_POST['olustur']) && 
		giris_yapilmamis() &&
		Oturum::al("yetki") != "admin"
		)
	{
		$cevap->ekleHata("AJAX_ISTEK_HATASI", "Yetkisiz kullanıcı ve giriş yapılmamış");
	}
	else
	{
		switch ($_POST['olustur'])
		{
			case 'test':  
			{
				try
				{
					$test = json_decode(Post::al("test"));
					$id =  Oturum::al("id");
					$simdi = veritabaniSimdi();
					
					if ($test == null)
					{
						throw new exCevap("AJAX_ISTEK_HATASI", "Hatalı JSON isteği");
					}
					
					//Eklenmesi istenen testin etiketini kontrol edelim. tabloda varsa hata olur.
					Mysql::sql($talil)
					->select("id")
					->from("testler")
					->where(array("etiket" => $test->etiket))
					->calistir();
					
					if ($talil->etkilenenSatirSayisi > 0)
					{
						throw new exCevap("HATA_TEST", "Etiket mevcut");
					}
					
					$talil->transaBasla();
					
					//Test, Soruları ve şıklarını ekleme
					Mysql::sql($talil)
					->insert("testler")
					->sutunlar("olusturan_id, etiket, aciklama, zorluk_id, olusturulma_tarihi")
					->degerler( array($id, $test->etiket, $test->aciklama, $test->zorluk, $simdi ))
					->calistir();
					
					//Test eklenemediyse
					if ($talil->etkilenenSatirSayisi <= 0)
					{
						throw new exCevap("HATA_TEST", "Test ekleme başarısız.");
					}
					
					$test_id = $talil->sonEklenen;
						
					$soru_no = 1;
		
					foreach ($test->sorular as $soru)
					{
						Mysql::sql($talil)
						->insert("sorular")
						->sutunlar("test_id, sira_no, soru, olusturulma_tarihi, dogru_sik, zorluk_id")
						->degerler( array($test_id, $soru_no++, $soru->soru, $simdi, $soru->dogru_sik, $soru->zorluk) )
						->calistir();
							
						$soru_id = $talil->sonEklenen;
							
						if ($talil->etkilenenSatirSayisi <= 0)
						{
							throw new exCevap("HATA_TEST", "Soru eklenemedi");
						}
					
						if (isset($soru->siklar))
						{
							foreach ($soru->siklar as $sik)
							{
								if (!isset($sik->deger))
								{
									$sik->deger = "";
								}
					
								Mysql::sql($talil)
								->insert("siklar")
								->sutunlar("soru_id, sik, deger")
								->degerler( array($soru_id, $sik->sik, $sik->deger))
								->calistir();
									
								if ($talil->etkilenenSatirSayisi <= 0)
								{
									throw new exCevap("HATA_TEST", "Şık eklenemedi");
								}
							}
						}
					}
					
					//Eklenecek testin resimlerini bul.
					$dizinTalil = Oturum::al("klasorTalil");
					
					$dizinGeciciOrjinal = "img/yuklemeler_gecici/orjinal/";
					$dizinGeciciParmak = "img/yuklemeler_gecici/parmak/";
					$dizinGeciciArama = "img/yuklemeler_gecici/arama/";
					
					$dizinKaliciOrjinal = "img/yuklemeler/orjinal/";
					$dizinKaliciParmak = "img/yuklemeler/parmak/";
					$dizinKaliciArama = "img/yuklemeler/arama/";
					
					Mysql::sql($talil)
					->select("concat(kok_dizin,dizin,ad) as yol, ad")
					->from("dosyalar")
					->where()
					->filtre( "concat(dizin,ad)", $test->resim )
					->calistir();
					
					if ($talil->etkilenenSatirSayisi <= 0)
					{
						throw new exCevap("HATA_TEST", "Test ile eşleşen resim bulunamadı");
					}
					
					$sonuc = $talil->sonuc(); //Dönüş türü Mesaj
					
					if ($sonuc->alHataMiktari() > 0) //Mesajda hiç hata yoksa
					{
						$cevap->ekleHata("HATA", $sonuc->alHatalar());//Bunu exception yapabiliriz.
					}
					
					$sonuc = $sonuc->alMesaj(); //Mesajı al. Ki bu durumda sorgu sonuc dizisi
					
					$adParmak = $sonuc["ad"];
					
					//Parmak resmin eski konumuyla yeni konumunu bulalım.
					$kaynakParmak = $sonuc["yol"];
					$hedefParmak = str_replace("yuklemeler_gecici", "yuklemeler", $kaynakParmak);
					
					$adOrjinal = str_replace("parmak__", "", $adParmak);
					$kaynakOrjinal = $dizinTalil . $dizinGeciciOrjinal . $adOrjinal;
					$hedefOrjinal = $dizinTalil . $dizinKaliciOrjinal . $adOrjinal;
					
					$adArama = "arama__$adOrjinal";
					$kaynakArama = $dizinTalil . $dizinGeciciArama . $adArama;
					$hedefArama = $dizinTalil . $dizinKaliciArama . $adArama;
					
					//Buraya kadar resimlerin hem eski konumlarını
					//hem de yeni konumlarını elde ettik. Şimdi taşımaya çalışalım.
					Dosya::tasi($kaynakParmak, $hedefParmak);
					Dosya::tasi($kaynakOrjinal, $hedefOrjinal);
					Dosya::tasi($kaynakArama, $hedefArama);
					
					//Eğer buraya kadar aykırı durum oluşmadıysa dosyalar taşınmış demektir.
					//Şimdi bu dosyaların veritabanındaki durumlarını ve dizinlerini güncelleyelim.
					
					Mysql::sql($talil)
					->update("dosyalar")
					->set( array("test_id"=>$test_id, "durum_id"=>2, "dizin"=>$dizinKaliciOrjinal) )
					->where()
					->filtre("ad", $adOrjinal)
					->calistir();
											
					Mysql::sql($talil)
					->update("dosyalar")
					->set( array("test_id"=>$test_id, "durum_id"=>2, "dizin"=>$dizinKaliciParmak) )
					->where()
					->filtre("ad", $adParmak)
					->calistir();

					Mysql::sql($talil)
					->update("dosyalar")
					->set( array("test_id"=>$test_id, "durum_id"=>2, "dizin"=>$dizinKaliciArama) )
					->where()
					->filtre("ad", $adArama)
					->calistir();
					
					$talil->komit();
				}
				catch(exDosya $hata)
				{
					$talil->gerial();
					
					$cevap->ekleHata("DOSYA_EXCEPTION", $hata->__toString());
				}
				catch(exVeritabani $hata)
				{
					$talil->gerial();
					
					$cevap->ekleHata("VERITABANI_EXCEPTION", $hata->__toString());
				}
				catch(exCevap $hata)
				{
					$talil->gerial();
					
					$cevap->ekleHata($hata->alTur(), $hata->alMesaj());
				}
				catch(Exception $hata)
				{
					$talil->gerial();
					
					$cevap->ekleHata("HATA", $hata->__toString());
				}
				break;
			}
		}//switch
	}//else
	
	echo $cevap->alJSON();
?>