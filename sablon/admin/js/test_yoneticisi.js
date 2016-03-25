var SoruYoneticisi = function(sorular)
{
	if (sorular != undefined)
	{
		this.sorular = sorular;
	}
	else
	{
		this.sorular = [];
	}
	
	this.goruntulenen_soru = 1; //bu dizi indeksine göre değil de sıra sayısına göredir.
								//dolayısıyla indeks işlemlerinde bir eksiğini almak gerekir.
	
	this.goruntulenen_soruyu_degistir = function(deger)
	{
		this.goruntulenen_soru += deger;
		
		if (this.goruntulenen_soru <= this.sorular.length)
		{
			var kaydet = "<input type=\"button\" class=\"siyah_dugme\" value=\"Güncelle\" id=\"guncelle\"/>";
			var yeni_soru = "<input type=\"button\" class=\"siyah_dugme\" value=\"Yeni Soru\" id=\"yeni_soru\"/>";
			
			$("#kaydet_ilerle").replaceWith(kaydet + yeni_soru);
		}
		else
		{
			var kaydet_ve_ilerle = "<input type=\"button\" class=\"siyah_dugme\" value=\"Kaydet ve İlerle\" id=\"kaydet_ilerle\"/>";
			$("#guncelle").replaceWith(kaydet_ve_ilerle);
			$("#yeni_soru").remove();
		}
	};
	
	this.goruntulenen_soru_format = function()
	{
		return this.sorular.length + " sorudan " + this.goruntulenen_soru + ". soru";
	};
	
	this.soru_ekle = function(soru)
	{
		this.sorular.push(soru);
		this.goruntulenen_soruyu_degistir(1);
	};
	
	this.yeni_soru = function()
	{
		this.goruntulenen_soruyu_degistir(this.sorular.length-this.goruntulenen_soru+1);
	};
	
	this.guncelle = function(soru)
	{
		this.sorular[this.goruntulenen_soru - 1] = soru;
	};
	
	this.sonraki_soru = function()
	{
		if (this.goruntulenen_soru < this.sorular.length) //bu şart sağlandığı sürece sonraki soruya geçebiliriz.
		{
			this.goruntulenen_soruyu_degistir(1);
			return this.sorular[this.goruntulenen_soru-1];
		}
		else
		{
			return null;
		}
	};
	
	this.onceki_soru = function()
	{
		if (this.goruntulenen_soru > 1)
		{
			this.goruntulenen_soruyu_degistir(-1);
			return this.sorular[this.goruntulenen_soru-1];
		}
		else
		{
			return null;
		}
	};
	
	this.en_ilkinci_soru = function()
	{
		this.goruntulenen_soruyu_degistir(1 - this.goruntulenen_soru);
		
		return this.sorular[this.goruntulenen_soru - 1];
	};
	
	this.yeni_sorularla_doldur = function(sorular)
	{
		this.sorular = [];
		this.sorular = sorular;
	};
};

//verilen soru nesnesine göre soru formunu doldurur.
function formuDoldur(soru_no, soru)
{	
	$("#soru_yonetimi .alan_alt_baslik").html(soru_no);
	$("#soru_yonetimi #soru").val(soru.soru);
	$("#soru_yonetimi #zorluk").val(soru.zorluk);
	
	//evet hayır şık türü ise
	if (soru.dogru_sik == "evet" || soru.dogru_sik == "hayir")
	{
		var evet  = "<div>Evet:<input type=\"radio\" name=\"sik\" class=\"_gerekli_\" value=\"evet\" ";
	    var hayir = "<div>Hayır:<input type=\"radio\" name=\"sik\" class=\"_gerekli_\" value=\"hayir\" ";
	    
	    if (soru.dogru_sik == "evet")
    	{
	    	evet += "checked=\"checked\"/></div>";
	    	hayir += "/></div>";
    	}
	    else
	    {
	    	evet += "/></div>";
	    	hayir += "checked=\"checked\"/></div>";
	    }
	    
	    $("#sik_yonetimi #siklar").html(evet+hayir);
	}
	else 
	{
		var siklar = "";
		for (var i = 0; i < soru.siklar.length; i++)
		{
			siklar += sik_olustur(soru.siklar[i].sik);
		}
		$("#sik_yonetimi #siklar").html(siklar);
		
		var i = 0;
		$("#sik_yonetimi #siklar :text").each(function()
		{
			$(this).val(soru.siklar[i++].deger);
		});
	}
	
	$("#siklar :radio[name='sik'][value='"+soru.dogru_sik+"']").attr("checked", "checked");
}

function formuBosalt(soru_no)
{
	$("#soru_yonetimi .alan_alt_baslik").html(soru_no);
	$("#soru_yonetimi #soru").val("");
	
	if ($("#soru_yonetimi :checkbox").is(":checked"))
	{
	}
	else
	{
		$("#soru_yonetimi #zorluk").val(0);
	}
	
	if ($("#sik_yonetimi :checkbox").is(":checked"))
	{
		$("#sik_yonetimi #siklar :text").val("");
		$("#sik_yonetimi #siklar :radio:checked").removeAttr('checked');
	}
	else
	{
		$("#sik_yonetimi #siklar").html("");
	}
}

//soru formunun bilgilerini kontrol ettikten sonra alır ve nesne olarak dönderir.
function formuAl()
{
	var secenekler = {
			bos : {
				mesaj : "Doldurmanız gereken alanlar var",
				sinif : "hata_form"
			},
			radyo : {
				yok : "Şık eklemelisiniz",
				sec : "Doğru şıkkı seçmelisin"
			}
	};
	
	if ($('#sorular').denetle(secenekler) > 0)
	{
		return false;
	}
	
	var soru = jQuery.trim($("#soru_yonetimi #soru").val());
	var zorluk = $("#soru_yonetimi #zorluk").val();
	var siklar = $("#sik_yonetimi #siklar :radio[name='sik']");
	var seciliSik = $("#sik_yonetimi #siklar :radio[name='sik']:checked");
	
	yeni_soru = {
			soru : soru,
			dogru_sik : seciliSik.val(),
			zorluk : zorluk
	};
	
	if (!(seciliSik.val() == 'evet' || seciliSik.val() == 'hayir'))
	{
		var _siklar = [];
		
		siklar.each(function(){
			_siklar.push({sik:$(this).val(), deger:$(this).next().val()});
		});
		
		yeni_soru.siklar = _siklar;
	}
	
	return yeni_soru;
}

function sik_olustur(harf)
{
	var siklar = "";
	
	siklar += "<div>";
	siklar += "<label for=\"sik\">" + harf + "</label>";
	siklar += "<input type=\"radio\" class=\"_gerekli_\" name=\"sik\" value=\"" + harf + "\"/>";
	siklar += "<input type=\"text\" class=\"_gerekli_\"/>";
	siklar += "<input type=\"button\" value=\"X\" class=\"dugme_kck\" id=\"sik_kaldir\" title=\"Şıkkı Kaldır\"/>";
	siklar += "</div>";
	
	return siklar;
}

function sik_ekle()
{
	var harfDizisi = new Array("A", "B", "C", "D", "E", "F", "G");
	var siklar = "";
	
	$("#sik_yonetimi #siklar :radio[value='evet']").parent().remove();
	$("#sik_yonetimi #siklar :radio[value='hayir']").parent().remove();
	
	if ($("#sik_yonetimi #siklar :radio").length == 0)
	{
		//İlk şık eklerken en az 2 şık olmalı
		siklar = sik_olustur(harfDizisi[0]) + sik_olustur(harfDizisi[1]);
	}
	else
	{
		var sonSik = $("#sik_yonetimi #siklar :radio:last").val();
		var harf = "";
		
		if (sonSik != "")
		{
			harf = harfDizisi[harfDizisi.indexOf(sonSik)+1];
			
			if (harf == "G")
			{
				$('#sik_ekle').remove();
			}
		}
		else
		{
			harf = harfDizisi[0];
		}
		
		siklar = sik_olustur(harf);
	}
	
	
	$("#sik_yonetimi #siklar").append(siklar);
	
}

function sik_kaldir()
{
	if ($("#sik_yonetimi #siklar :radio").length <= 2)
	{	
		$('#sorular').hata("En az 2 şıkkın olması şart.");
		return;
	}
	
	$(this).parent().remove();
	
	var harfDizisi = new Array("A", "B", "C", "D", "E", "F", "G");
	var i = 0;
	
	$("#sik_yonetimi #siklar div label").each(function()
	{
		$(this).text(harfDizisi[i++]);
	});
	
	i = 0;
	
	$("#sik_yonetimi #siklar div :radio").each(function()
	{
		$(this).val(harfDizisi[i++]);
	});
}

function evet_hayir()
{
	var siklar  = "<div><label>Evet:<input type=\"radio\" name=\"sik\" class=\"_gerekli_\" value=\"evet\"></label></div>" +
			      "<div><label>Hayır:<input type=\"radio\" name=\"sik\" class=\"_gerekli_\" value=\"hayir\"></label></div>";
	
	$("#sik_yonetimi #siklar").html("").html(siklar);
	
	if ($('#sik_ekle').length == 0)
	{
		$('#evet_hayir').before("<input type=\"button\" value=\"Şık Ekle\" id=\"sik_ekle\"/>");
	}
}

function kaydet_ilerle(event)
{	
	var sorular = event.data;
	var sonuc = formuAl();
	
	if (sonuc)
	{
		sorular.soru_ekle(sonuc);
		formuBosalt(sorular.goruntulenen_soru_format());
		$('#sorular').basari("Soru kaydı başarılı.");
	}
}

function onceki_soru(event)
{
	var sorular = event.data;
	var soru = sorular.onceki_soru();
	
	if (soru == null)
	{
		$('#sorular').hata("Zaten en ilkinci sorudasınız.");
	}
	else
	{
		formuDoldur(sorular.goruntulenen_soru_format(), soru);
	}
}

function sonraki_soru(event)
{
	var sorular = event.data;
	var soru = sorular.sonraki_soru();
	
	if (soru == null)
	{
		$('#sorular').hata("Zaten en sonuncu sorudasınız.");
	}
	else
	{
		formuDoldur(sorular.goruntulenen_soru_format(), soru);
	}
}

function guncelle(event)
{
	var sorular = event.data;
	var sonuc = formuAl();
	
	if (sonuc)
	{
		sorular.guncelle(sonuc);
		
		$('#sorular').basari("Güncelleme Başarılı!");
	}
}

function yeni_soru(event)
{
	var sorular = event.data;
	sorular.yeni_soru();
	formuBosalt(sorular.goruntulenen_soru_format());
}

function resim_ekle()
{
	resimSec({id: "yeniTestResmi", selector: "#yeniTestResmi", name:"yeniTestResmi"});
}