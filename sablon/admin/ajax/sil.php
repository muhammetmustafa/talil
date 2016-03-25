<?php
	session_start();
	
	require_once '../essek/class.Oturum.php';
	require_once '../essek/sql/class.Mysql.php';
	require_once '../essek/class.Dosya.php';
	require_once '../essek/class.Mesaj.php';
	require_once '../essek/class.Cevap.php';
	
	$cevap = new Cevap();
	$talil = new Mysql("talil");
	
	if ( !isset($_POST['test_id']) && (giris_yapilmamis()) && (Oturum::al('yetki') != "admin") )
	{
		$cevap->ekleHata("AJAX_ISTEK_HATASI", "Yetkisiz kullanıcı ve giriş yapılmamış");
	}
	else
	{
		try
		{
			$test_id = Post::al('test_id');
			
			//önce silinecek testin resimlerini bulalım.
			Mysql::sql($talil)
			->select("concat(kok_dizin, dizin, ad) as yol")->from("dosyalar")->where()->filtre("test_id", $test_id)->ve()->filtre("durum_id", 2)->calistir();
			if ($talil->etkilenenSatirSayisi > 0)
			{
				$sonuc = $talil->sonuc(true)->alMesaj();
				
				$talil->transaBasla();
				
				//Daha sonra testi sil
				Mysql::sql($talil)->delete()->from("testler")->where()->filtre("id", $test_id)->calistir();
				if ($talil->etkilenenSatirSayisi > 0)
				{
					foreach($sonuc as $resim)
					{
						$mesaj = Dosya::sil($resim["yol"]);
					}
					
					$talil->komit();
				}
				else
				{
					$talil->gerial();
					$cevap->ekleHata('HATA_TEST', 'Testler silinemedi.');
				}
			}
			else
			{
				$cevap->ekleHata('HATA_TEST', 'Yanlış test id.');
			}
		} 
		catch (exDosya $hata) 
		{
			$talil->gerial();
			
			$cevap->ekleHata("DOSYA_HATASI", $hata->__toString());
		}
		catch (exVeritabani $hata)
		{
			$cevap->ekleHata("VERITABANI_HATASI", $hata->__toString());
		}
				
	}
	
	echo $cevap->alJSON();
?>