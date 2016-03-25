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
		Oturum::al("yetki") != "kullanıcı"
		)
	{
		$cevap->ekleHata("AJAX_ISTEK_HATASI", "Yetkisiz kullanıcı ve giriş yapılmamış"); 
	}
	else
	{
		$id = Post::al("test_id");
		$soru_sira_no = Post::al('sira_no');
		
		try
		{
			//Sorunun alınması
			Mysql::sql($talil)
			->select("id, sira_no, soru, dogru_sik")
			->from("sorular")
			->where()
			->filtre("test_id", $id)->ve()
			->filtre("sira_no", $soru_sira_no)
			->calistir();
			if ($talil->etkilenenSatirSayisi <= 0)
			{
				throw new exCevap("HATA_SORGU", $talil->sonSorgu);
			}
			$soru = $talil->sonuc()->alMesaj();
			
			//Şıkların alınması
			if (!($soru["dogru_sik"] == "evet" || $soru["dogru_sik"] == "hayir"))
			{
				Mysql::sql($talil)
				->select("sik, deger")
				->from("siklar")
				->where()
				->filtreString("soru_id = ".$soru["id"])
				->calistir();
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
			
			$cevap->ekleCevap("soru", $soru);
		}
		catch (exCevap $hata)
		{
			$cevap->ekleHata($hata->alTur(), $hata->alMesaj());
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