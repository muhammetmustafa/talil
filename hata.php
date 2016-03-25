<?php 
	session_start();
	
	require_once 'essek/class.Oturum.php';
	require_once 'essek/class.Cevap.php';
	
	if (giris_yapilmis())
	{
		header("Location: home.php");
	}
	
	$index = 
	(new Cevap())
	->ataBaslik("Talil - Hata")
	->ekleCSS(array("sablon/kaynaklar/css/ilklendirme.css"))
	->ataGovdeDosyasi('sablon/hata.phtml');
		
	echo $index->giydir('essek/sablon/sablon.phtml')->alHTML();
?>