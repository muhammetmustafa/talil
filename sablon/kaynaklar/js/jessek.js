function jEssek(degerler)
{
	/*
		===============================================
	*	Bir HTML etiket Prototipi
	*	Kapalı:	<e o.i o.c o.n o.v o.t e/>
	*	Açık:	<e o.i o.c o.n o.v o.t e>i</e>
	*	===============================================
	*	e = etiket = HTML etiketi. a, br, img gibi
	*	o = ozellikler = HTML ozellikleri. class, id
	*		Bu değer için bazı alt değerlerin kısaltmaları:
	*		o.i = o.id 
	*	    o.c = o.class
	*		o.n = o.name 
	*		o.v = o.value
	*		o.t = o.type
	*		o.st = o.style
	*	i = icerik = HTML içeriği. Eğer etiket açık etiketse.
	*	t = tur = HTML etiketinin turu. kapali (input gibi) veya acik etiket (div gibi).
	*	
	*	
	*/
	if (degerler == null)
	{
		this.degerler = {};
	}
	else
	{
		this.degerler = degerler;
	}
	
	var sozlukOzellikler = {
		i : "id",
		c : "class",
		n : "name",
		v : "value",
		t : "type",
		s : "src",
		st: "style",
		w : "width",
		h : "height",
		cspn: "colspan",
	};
	
	var listeKapalilar = Array("input", "br", "img");
	
	this.esInput = function(tur)
	{
		this.degerler.e = "input";
		this.degerler.o.t = tur;
		
		return this.esHtml();
	};
	
	/*
	*	Bu fonksiyon hem select html elemanını hem de içindeki optionları oluşturur.
	*	select etiketinin kendi özellikleri global değişkenlerden alınır.
	*
	*	optDegerler için esOption() fonksiyonuna bak.
	*/
	this.esSelect = function(optDegerler)
	{
		this.degerler.e = "select";
		
		this.degerler.i = this.esOption(optDegerler);
		
		return this.esHtml();
	};
	
	/*
	*	Bu fonksiyon select html etiketinin içindeki optionları oluşturur. Ama select elemanın içine almaz.
	*
	*	m: açılır listelerde ilk gösterimde yer alacak mesaj. (Seçiniz..., Seçilmedi.. v.b.). Atanırsa bu eleman seçili olur.
	*	md: mesajın değeri. Atanmayabilir.
	*	s: seçili elemanın değeri. Select'teki optionlar için value değerlerinden bir tanesi
	*	e: elemanlar. bunun yapısı şöyledir:
	*		Array(object{deger, icerik});
	*/
	this.esOption = function(optDegerler)
	{
		var opsiyonHtml = "";
		var opsiyon = "";
		
		if (optDegerler.m != null) //Gösterilecek mesaj varsa
		{
			opsiyonHtml += "<option";
			
			if (optDegerler.md != null) //Gösterilen mesaja değer atanmak isteniyorsa
			{
				opsiyonHtml += " value=\"" + optDegerler.md + "\" selected>";
			}
			else
			{
				opsiyonHtml += " selected>";
			}
			
			opsiyonHtml += optDegerler.m + "</option>";
		}
		
		for (var i = 0; i < optDegerler.e.length; i++)
		{
			opsiyon = optDegerler.e[i];
			
			opsiyonHtml += "<option value=\"" + opsiyon.deger + "\"";
			
			if (opsiyon.deger == optDegerler.s)
			{
				opsiyonHtml += " selected>";
			}
			else
			{
				opsiyonHtml += ">";
			}
			
			opsiyonHtml += opsiyon.icerik + "</option>";
		}
		
		return opsiyonHtml;
	};
	
	/*
	*	Tablo oluşturur.
	*	
		th: Başlık varsa bu değer atanır.
	*	
	*	e: Eklenecek elemanlar. Yapısı şöyledir.
	*		e: Array(
					{d:Array({d:hucre1, o:{}}, 
						  	 {d:hucre2, o:{}}, 
						     {d:hucre3, o:{}}), o:{}}
				  )
	*		
	*/
	this.esTablo = function(tblDegerler)
	{
		var satirIcerik = "";
		var hucre = "";
		
		this.degerler.e = "table";
		this.degerler.i = "";
		
		if (tblDegerler.th != null)
		{
			this.degerler.i += "<th>" + tblDegerler.th + "</th>";
		}
		
		for (var ix = 0; ix < tblDegerler.e.length; ix++)
		{
			satirIcerik = "";
			
			for (var jx = 0; jx < tblDegerler.e[ix].d.length; jx++)
			{
				hucre = tblDegerler.e[ix].d[jx];;
				
				satirIcerik += (new jEssek({e: "td", i:hucre.d, o:hucre.o})).esHtml();
			}
			
			this.degerler.i += (new jEssek({e: "tr", i:satirIcerik, o:tblDegerler.e[ix].o})).esHtml();
		}
		
		return this.esHtml();
	};
	
	this.esHtml = function()
	{
		var html = "<" + this.degerler.e;
		var ozellikDegeri = "";
	
		if (this.degerler.o != null)
		{
			for (ozellik in this.degerler.o)
			{
				if (sozlukOzellikler[ozellik] != null)
				{
					ozellikDegeri = this.degerler.o[ozellik];
					
					html += " " + sozlukOzellikler[ozellik] + "=\"" + ozellikDegeri + "\"";
				}
			}
		}
		
		//Eğer etiketimiz yukarıdaki kapalılar listesindeyse etiketin türünü
		//kapalı olarak değiştir. Eğer listede bulamadıysan açık olarak değiştir.
		if (this.degerler.t == null) 
		{
			if (listeKapalilar.indexOf(this.degerler.e) >= 0)
			{
				this.degerler.t = "kapali";
			}
			else
			{
				this.degerler.t = "acik";
			}
		}
		
		if (this.degerler.t == "acik") //eğer etiket açıksa
		{
			html += ">";
			
			if (this.degerler.i != null)
			{
				html += this.degerler.i;
			}
			
			html += "</" + this.degerler.e + ">";
		}
		else
		{
			html += "/>";
		}
		
		return html;
	};
}

jEssek.idDegis = function(essek, aranan, yeni)
{
	essek.o.i = essek.o.i.replace(aranan, yeni);
};
//Bu sınıf degerler Array olduğu zaman kullanılacaktır.
//Zaten essekler isimlendirmesindende anlaşılabilir.
//jEssek sınıfı kullanılır.
function jEssekler(degerler)
{
	this.degerler = degerler;
	
	this.esInput = function(tur)
	{
		for (var i = 0; i < this.degerler.length; i++)
		{
			this.degerler[i].e = "input";
			this.degerler[i].o.t = tur;
		}
		
		return this.esHtml();
	};
	
	this.esHtml = function()
	{
		var topluHtml = "";
		
		if (this.degerler.length)
		{
			for (var i = 0; i < this.degerler.length; i++)
			{
				topluHtml += new jEssek(this.degerler[i]).esHtml();
			}
		}
		
		return topluHtml;
	};
}
