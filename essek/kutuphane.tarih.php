<?php
	/**
	*	Tarih ve zaman için genel fonksiyonlar. 
	*	
	*	Muhammet Mustafa Çaliskan
	*	2014 
	*/
	

	/**
	*	Belli formatta tarih dönderir.
	*	
	*	Örn: 21/05/1990 07:02:04
	*/
	function veritabaniSimdi()
	{
		$tarih = new DateTime();
		
		return $tarih->format("d/m/Y H:i:s");
	}
	
	/**
	*	Sunucuya eklenen dosya için tarih ve saatin birleşimini döndürür.
	*
	*/
	function dosyaSimdi()
	{
		$tarih = new DateTime();
		
		return $tarih->format("dmYHis");
	}
?>