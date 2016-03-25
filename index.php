<?php
	session_start();
	
	require_once 'essek/class.Oturum.php';
	require_once 'essek/class.Post.php';
	require_once 'essek/class.Form.php';
	require_once 'essek/class.Cevap.php';
	require_once 'essek/sql/class.Mysql.php';
	
	if (giris_yapilmis())
	{
		if (Oturum::al("yetki") == "admin")
		{
			header("Location: admin.php");
		}
		else
		{
			header("Location: home.php");
		}
	}
	
	$index = new Cevap();
	$index
	->ataBaslik("Talil")
	->ekleJS("js/jquery.js")
	->ekleCSS( array( "sablon/kaynaklar/css/ilklendirme.css", "sablon/kaynaklar/css/zabita.css",
					  "sablon/kaynaklar/css/ortak.css", "sablon/index/css/index.css" ) )
	->ataGovdeDosyasi("sablon/index/index.phtml");
	
	$form = new Form();
	
	//eğer sayfaya ilk defa girilmişse
	if (!$form->ilkZiyaret(array("k_adi_email", "grs_sifre"))) 
	{
		$index->ekleGovde("boslar", $form->kontrolBos(array("k_adi_email" => "Kullanıcı Adı/Email", "grs_sifre" => "Şifre")));
		
		if ($form->toplamHataMiktari <= 0) //tüm login bilgileri girilmişse
		{	
			try 
			{
				$talil = new Mysql("talil");
				
				Mysql::sql($talil)
				->select("k.id AS kullanici_id, yetki, kullanici_adi, puan, s.seviye AS k_seviye")
				->from("kullanicilar AS k, seviyeler AS s")
				->where()
				->filtre("kullanici_adi", "k_adi_email", null, true)->ve()
				->filtre("sifre", "grs_sifre", null, true)->ve()
				->filtreString("s.id = k.seviye")
				->calistir();
				
				if ($talil->etkilenenSatirSayisi > 0) //Bilgiler doğrulandıysa
				{
					giris_yap('k_adi_email', true);
					
					$sonuc = $talil->sonuc()->alMesaj();
					
					Oturum::ata("kullanici", 
						array("id" => $sonuc["kullanici_id"], 
							   "yetki" => $sonuc["yetki"],
							   "kullanici_adi" => $sonuc["kullanici_adi"],
							   "puan" => $sonuc["puan"],
							   "seviye" => $sonuc["k_seviye"]
							  ));
				
					Oturum::ata("klasorTalil", str_replace("\\", "/", __DIR__)."/");
					
					if ($sonuc["yetki"] == "admin")
					{
						header("Location: admin.php");
					}
					else if ($sonuc["yetki"] == "kullanıcı")
					{
						header("Location: home.php");
					}
					else
					{
						header("Location: hata.php");
					}
				}
				else
				{
					$index->ekleGovde("uyari", "Öyle bir kullanıcı yok veya Kullanıcı Adı/Şifre kombinasyonu hatalı!");
				}
			} 
			catch (exVeritabani $hata) 
			{
				$index->ekleGovde("uyari", $hata->__toString());
			}
			
		}
	}

	echo $index->giydir('essek/sablon/sablon.phtml')->alHTML();
?>