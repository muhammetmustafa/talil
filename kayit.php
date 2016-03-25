<?php 
	session_start();
	
	require_once 'essek/kutuphane.guvenlik.php';
	require_once 'essek/class.Oturum.php';
	require_once 'essek/class.Post.php';
	require_once 'essek/class.Form.php';
	require_once 'essek/class.Cevap.php';
	require_once 'essek/sql/class.Mysql.php';
	
	if (giris_yapilmis())
	{
		header("Location: home.php");
	}
	
	$index = new Cevap();
	$index
	->ataBaslik("Talil")
	->ekleJS("js/jquery.js")
	->ekleCSS( array( "sablon/kaynaklar/css/ilklendirme.css", "sablon/kaynaklar/css/zabita.css",
					  "sablon/kaynaklar/css/ortak.css", "sablon/index/css/index.css" ) )
	->ataGovdeDosyasi("sablon/index/index.phtml");
	
	$form = new Form();
	
	if (!$form->ilkZiyaret(array("k_adi", "email", "sifre", "sifre_tekrar"))) // Sayfaya birşey gönderdiysem
	{
		try
		{
			$talil = new Mysql("talil");
			
			$index->ekleGovde("boslar", $form->kontrolBos(array("sifre" => "Şifre")));
			
			if (Post::al("k_adi") != "")
			{	
				//Kullanıcı adı alınmış mı kontrol edelim
				Mysql::sql($talil)
				->select("id")
				->from("kullanicilar")
				->where(array("kullanici_adi"=>"k_adi"), true)
				->calistir();
					
				//Kullanıcı ad varsa
				if ($talil->etkilenenSatirSayisi > 0)
				{
					$index->ekleGovde("k_adi_hata", "Kullanıcı Adı zaten alınmış");
					$form->hataArtir();
				}
				else
				{
					$index->ekleGovde("k_adi_onay", "Kullanıcı Adı alınabilir");
				}	
			}
			else
			{
				$index->ekleGovde("boslar", array("k_adi" => "Kullanıcı adı boş bırakılamaz"));
				$form->hataArtir();
			}
			
			//Email 'i kontrol edelim.
			if (Post::al("email") != "")
			{
				if (emailGecerli(Post::al("email")))
				{
					Mysql::sql($talil)
					->select("id")
					->from("kullanicilar")
					->where(array("email"=>"email"), true)
					->calistir();
					
					if ($talil->etkilenenSatirSayisi > 0)
					{
						$index->ekleGovde("email_hata", "Email zaten alınmış");
						$form->hataArtir();
					}
					else
					{
						$index->ekleGovde("email_onay", "Email alınabilir");
					}
				}
				else
				{
					$index->ekleGovde("email_hata", "Email geçersiz");
					$form->hataArtir();
				}	
			}
			else
			{
				$index->ekleGovde("boslar", array("email" => "Email boş bırakılamaz"));
				$form->hataArtir();
			}
			
			//buraya kadarki tüm kontrollerden geçtiysem
			if ($form->toplamHataMiktari <= 0) 
			{
				Mysql::sql($talil)
				->insert("kullanicilar")
				->sutunlar("kullanici_adi, yetki, sifre, email")
				->degerler( array("k_adi", (array)"kullanıcı", "sifre", "email"), true	)
				->calistir();
				
				if ($talil->etkilenenSatirSayisi > 0)
				{
					giris_yap('k_adi', true);
					
					Oturum::ata("kullanici",
						array("id" => $talil->sonEklenen,
						"yetki" => "kullanici",
						"kullanici_adi" => Post::al("k_adi"),
						"puan" => 0,
						"seviye" => "Yeni Eleman"
						));
					
					Oturum::ata("klasorTalil", str_replace("\\", "/", __DIR__)."/");
					
					header("Location: home.php");
				}
			}
			
		} 
		catch (exVeritabani $hata) 
		{
			$index->ekleGovde("veritabaniHatasi", $hata->__toString());
		}
	}
		
	echo $index->giydir('essek/sablon/sablon.phtml')->alHTML();
?>