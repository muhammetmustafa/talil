<?php 
	session_start();
	
	require_once 'essek/class.Oturum.php';
	require_once 'essek/sql/class.Mysql.php';
	require_once 'essek/class.Dosya.php';
	
	
	if (giris_yapilmis())
	{	
		cikis_yap();
		
		try
		{
			$talil = new Mysql("talil");
			
			//önce silinecek testin resimlerini bulalım.
			Mysql::sql($talil)
			->select("concat(kok_dizin, dizin, ad) as yol")
			->from("dosyalar")
			->where()
			->filtre("yukleyen_id", Oturum::al('id'))->ve()
			->filtre("durum_id", 5)
			->calistir();
			
			if ($talil->etkilenenSatirSayisi > 0)
			{
				$sonuc = $talil->sonuc(true)->alMesaj();
				
				try
				{
					foreach($sonuc as $resim)
					{
						Dosya::sil($resim["yol"]);
					}
				}
				catch (exDosya $hata)
				{
					
				}
				
				Mysql::sql($talil)
				->update("dosyalar")->set( array("durum_id"=>3, "dizin"=>"null") )->where()
				->filtre("yukleyen_id", Oturum::al('id'))->ve()->filtre("durum_id", 5)->calistir();
			}
		} 
		catch (exVeritabani $hata)
		{
			
		}
				
	}
	
	header("Location: index.php");
?>