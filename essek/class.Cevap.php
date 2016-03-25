<?php
	/*
	*	Bu sınıf isteklere gönderilen cevapların yönetiminde kullanılacaktır.
	*	
	*	@copyright (c) 2014 Muhammet Mustafa Çalışkan
	*	
	*/
	
	require_once 'class.Dosya.php';
	require_once 'class.HataYoneticisi.php';
	
	class Cevap
	{	
		/**
		 *	Cevabın başlığı. 
		 *
		 *	@var string
		 */
		private $baslik = "";
		
		/**
		 *	Cevaba eklenecek javascript dosyalarının dizisi
		 *
		 *	@var array
		 */
		private $diziJS = array();
		
		/**
		 *	Cevaba eklenecek stil dosyalarının dizisi
		 *
		 *	@var array
		 */
		private $diziCSS = array();
		
		/**
		 *	Cevaba eklenecek inline javascript.
		 *
		 *	@var string
		 */
		private $scriptJS = "";
		
		/**
		 *	Cevaba eklenecek inline stil
		 *
		 *	@var string
		 */
		private $stilCSS = "";
		
		/**
		 *	Cevap gövdesinin taranacağı dosya. (bir nevi şablon (template) dosyası) 
		 *  HTML cevapları için <body></body> etiketleri arasındaki kısmı temsil eder.
		 *
		 *	@var string
		 */
		private $govdeDosyasi = "";
		
		/**
		 *  Cevap gövdesi taranırken (ob_start() ve ob_get_clean() ile) dosyadaki değişkenleri temsil eder.
		 * 
		 *  @var array
		 */
		private $govdeDegiskeni = array();
		
		/**
		 * 	Gövdenin (şablon) içine eklenecek suretin dosyası. (bir nevi görünüm (view) dosyası)
		 *
		 * @var string
		 */
		private $suretDosyasi = "";
		
		/**
		 *  Suret taranırken (ob_start() ve ob_get_clean() ile) dosyadaki değişkenleri temsil eder.
		 *
		 *  @var array
		 */
		private $suretDegiskeni = array();
		
		/**
		*	Dönecek cevaplar. 
		*
		*	@var mixed. (String veya Array)
		*/
		private $cevaplar = array();
		
		/**
		*	Oluşan hatalar
		*
		*	Tür:		Hata (class.Hata.php dosyasında)
		*	İlkdeğer:	Boş Hata Nesnesi
		*/
		private $hatalar;
	
		public function __construct()
		{
			$this->hatalar = new HataYoneticisi();
		}
		
		public function giydir($html, $uygulama_adi = null)
		{
			if (!file_exists($html))
			{
				throw new exDosyaMevcutDegil($html);
			}
			
			if ($this->suretDosyasi != "" && !file_exists($this->suretDosyasi))
			{
				throw new exDosyaMevcutDegil($this->suretDosyasi);
			}
			
			if (!file_exists($this->govdeDosyasi))
			{
				throw new exDosyaMevcutDegil($this->govdeDosyasi);
			}
			
			$baslik = $this->baslik;
			$diziJS = $this->diziJS;
			$diziCSS = $this->diziCSS;
			$scriptJS = $this->scriptJS;
			$stilCSS = $this->stilCSS;
			$govde = $this->govdeGiydir();
		
			ob_start();
			include_once ($html);
			$this->ekleCevap("html", ob_get_clean());
		
			return $this;
		}
		
		private function suretGiydir()
		{		
			$suret = $this->suretDegiskeni;
			
			ob_start();
			include ($this->suretDosyasi);
			return ob_get_clean();
		}
		
		private function govdeGiydir()
		{
			$govde = $this->govdeDegiskeni;
			$suret = $this->suretGiydir();
		
			ob_start();
			include_once ($this->govdeDosyasi);
			return ob_get_clean();
		}
		
		public function ekleHata($hataTuru, $hataMesaji, $ekBilgiler = null, $uyari = false)
		{
			$this->hatalar->ekleHata($hataTuru, $hataMesaji, $ekBilgiler, $uyari);
		}
		
		public function ekleCevap($cevap, $deger)
		{
			$this->cevaplar = array_merge_recursive($this->cevaplar, array($cevap => $deger));
		}
		
		public function ekleJS($diziJS)
		{
			if (!is_array($diziJS))
			{
				$diziJS = (array)$diziJS;
			}
				
			$this->diziJS = array_merge_recursive($this->diziJS, $diziJS);
				
			return $this;
		}
		
		public function ekleCSS($diziCSS)
		{
			if (!is_array($diziCSS))
			{
				$diziCSS = (array)$diziCSS;
			}
				
			$this->diziCSS = array_merge_recursive($this->diziCSS, $diziCSS);
				
			return $this;
		}
		
		public function ekleGovde($anahtar, $deger)
		{
			$this->govdeDegiskeni = array_merge_recursive($this->govdeDegiskeni, array($anahtar => $deger));
			
			return $this;
		}
		
		public function ekleSuret($anahtar, $deger)
		{
			$this->suretDegiskeni = array_merge_recursive($this->suretDegiskeni, array($anahtar => $deger));
			
			return $this;
		}
		
		public function alJSON()
		{	
			header("Content-type: application/json");
			
			return json_encode($this->alDizi());
		}
		
		public function alHTML()
		{
			header("Content-type: text/html");
			
			return $this->alCevap("html");
		}
		
		public function alDizi()
		{
			$cevap = array();
			
			if (count($this->cevaplar) > 0)
			{
				$cevap["cevap"] = $this->cevaplar;
			}
			
			if ($this->hatalar->alHataMiktari() > 0)
			{
				$cevap["hatalar"] = $this->hatalar->alHatalar();
			}
			
			$cevap["hatamiktari"] = $this->hatalar->alHataMiktari();
			
			return $cevap;
		}
		
		public function alCevap($anahtar = null)
		{
			if ($anahtar != null)
			{
				if (!is_string($anahtar))
				{
					return null;
				}
				if (key_exists($anahtar, $this->cevaplar))
				{
					return $this->cevaplar[$anahtar];
				}
				else
				{
					return null;
				}
			}
			else
			{
				if (count($this->cevaplar) >= 1)
				{
					return $this->cevaplar;
				}
				else
				{
					return null;
				}
			}
		}
		
		//Dönüş değeri: Hata sınıfı (class.Hata.php sınıfında)
		public function alHatalar($hataTuru = null)
		{
			return $this->hatalar->alHatalar($hataTuru);
		}
		
		public function alHataMiktari()
		{
			return $this->hatalar->alHataMiktari();
		}
		
		public function alBaslik()
		{
			return $this->baslik;
		}

		public function ataBaslik($baslik)
		{
			$this->baslik = $baslik;
			
			return $this;
		}

		public function ataScriptJS($scriptJS)
		{
			$this->scriptJS = $scriptJS;
			
			return $this;
		}

		public function ataStilCSS($stilCSS)
		{
			$this->stilCSS = $stilCSS;
			
			return $this;
		}

		public function ataGovdeDosyasi($govdeDosyasi)
		{
			$this->govdeDosyasi = $govdeDosyasi;
			
			return $this;
		}
		
		public function ataSuretDosyasi($suretDosyasi)
		{
			$this->suretDosyasi = $suretDosyasi;
			
			return $this;
		}
	}
	
	class exCevap extends Exception
	{
		private $tur;
		private $mesaj;
		
		public function __construct($tur, $mesaj)
		{
			$this->tur = $tur;
			$this->mesaj = $mesaj;
		}
		
		public function alTur()
		{
			return $this->tur;
		}
		
		public function alMesaj()
		{
			return $this->mesaj;
		}
	}
?>