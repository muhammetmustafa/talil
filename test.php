<?php
	session_start();
	
	require_once 'essek/class.Oturum.php';
	require_once 'essek/class.Post.php';
	require_once 'essek/class.Form.php';
	require_once 'essek/class.Cevap.php';
	require_once 'essek/sql/class.Mysql.php';
	
	if (giris_yapilmamis())
	{
		header("Location: index.php");
	}
	
	$index = new Cevap();
	$index
	->ataBaslik("Talil")
	->ekleJS("js/jquery.js")
	->ekleJS("sablon/anasayfa/js/test.js")
	->ekleCSS( array( "sablon/kaynaklar/css/ilklendirme.css", 
					  "sablon/kaynaklar/css/zabita.css",
					  "sablon/kaynaklar/css/ortak.css", 
					  "sablon/anasayfa/css/anasayfa.css" ) )
	->ataGovdeDosyasi("sablon/anasayfa/test.phtml");
	
	echo $index->giydir('essek/sablon/sablon.phtml')->alHTML();
?>