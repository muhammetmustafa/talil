<?php 
	session_start();
	
	require_once 'essek/class.Oturum.php';
	require_once 'essek/class.Cevap.php';
	
	if (giris_yapilmis())
	{
		if (Oturum::al("kullanici")["yetki"] != "admin")
		{
			header("Location: index.php");
		}
		
		$admin = new Cevap();
		$admin
		->ataBaslik("Talil - Yönetim")
		->ekleJS( array(
						'js/jquery.js',
						'js/zabita.js',
						'sablon/admin/js/ortak.js'
						))
		->ekleCSS('sablon/admin/css/admin.css')
		->ekleGovde('kullanici_adi', Oturum::al("kullanici")["kullanici_adi"] )
		->ekleGovde('yetki', Oturum::al("kullanici")["yetki"])
		->ataGovdeDosyasi('sablon/admin/karsilama.phtml');
		
		if (isset($_GET['olustur']))
		{
			switch ($_GET['olustur'])
			{
				case 'resimtesti':
					$admin
					->ataGovdeDosyasi('sablon/admin/test_olustur.phtml')
					->ekleJS('sablon/admin/js/test_yoneticisi.js')
					->ekleJS('sablon/admin/js/test_olustur.js');
					break;
				
			}
		}
		else if (isset($_GET['yonetim']))
		{
			switch ($_GET['yonetim'])
			{
				case 'resimtesti':
					$admin
					->ataGovdeDosyasi('sablon/admin/test_yonetimi.phtml')
					->ekleJS('sablon/admin/js/test_yoneticisi.js')
					->ekleJS('sablon/admin/js/test_yonetimi.js');
					break;
			
			}
		}
				
		echo $admin->giydir('essek/sablon/sablon.phtml')->alHTML();
	}
	else
	{
		header("Location: index.php");
	}
	
?>
