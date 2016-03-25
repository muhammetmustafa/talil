(function($)
{
	var sorular = new SoruYoneticisi();
	
	function aramaFormunuAl()
	{
		var secenekler = {
				bos : {
					mesaj : 'Doldurmadığınız alanlar mevcut',
					sinif : 'hata_form'
				}
		};
		
		if ($('#arama').denetle(secenekler) > 0)
		{
			return false;
		}
		
		var etiket = $("#etiket textarea").val();
		var aciklama = $("#aciklama textarea").val();
		var baslangic = $("#baslangic").val();
		var bitis = $("#bitis").val();
		var zorluk = $("#zorluk select").val();
		
		return {
				etiket: etiket,
				aciklama : aciklama,
				tarih : {
					baslangic : baslangic,
					bitis : bitis
				},
				zorluk : zorluk,
				sayfa : 1
		};
	}
	
	function sonucDoldur(parametreler)
	{
		$.ajax
		({
			url : 'ajax/ara.php',
			type: 'post',
			data: 'parametreler=' + JSON.stringify(parametreler),
			dataType: 'json'	
		})
		.done(function(data, textStatus, jqXHR)
		{
			if (data.hatamiktari == 0)
			{
				$("#icerik #sonuclar").remove();
				
				if (data.cevap.sonucmiktari > 0)
				{
					$("#icerik #arama").append(data.cevap.html);
				}
				else
				{
					$('#arama').hata("Aramanızla eşleşen bir sonuç bulunamadı!");
				}
			}
			else
			{
				$('#arama').hata("Hata Oluştu: " + data.hatalar[0]);
			}
		});
	}
	
	$("#ara").on("click", function()
	{
		var parametreler = aramaFormunuAl();
		
		if (!parametreler)
		{
			return;
		}
		
		sonucDoldur(parametreler);
		
		//Arama formunu gizle
		$("#icerik #arama #bilgiler #baslik #gizle_goster").trigger("click");

	});
	
	$('body').on('click', '#sayfalar #numaralar a', function()
	{
		var parametreler = aramaFormunuAl();
		
		if (!parametreler)
		{
			return;
		}
		
		parametreler.sayfa = $(this).html();
		
		sonucDoldur(parametreler);
	});
	
	$('body').on('click', '#en_ilk', function()
	{
		var parametreler = aramaFormunuAl();
		
		if (!parametreler)
		{
			return;
		}
		
		parametreler.sayfa = 1;
		
		sonucDoldur(parametreler);
	});
	
	$('body').on('click', '#onceki', function()
	{
		var sayfa_no = $('#sayfalar #numaralar #sayfa').html();
		
		if (sayfa_no == 1)
		{
			return;
		}
		
		var parametreler = aramaFormunuAl();
		
		if (!parametreler)
		{
			return;
		}
		
		parametreler.sayfa = parseInt(sayfa_no) - 1;
		
		sonucDoldur(parametreler);
	});
	
	$('body').on('click', '#sonraki', function()
	{
		var sayfa_no = $('#sayfalar #numaralar #sayfa').html();
		
		var parametreler = aramaFormunuAl();
		
		if (!parametreler)
		{
			return;
		}
		
		parametreler.sayfa = parseInt(sayfa_no) + 1;
		
		sonucDoldur(parametreler);
	});
	
	$('body').on('click', '#en_son', function()
	{
		var parametreler = aramaFormunuAl();
		
		if (!parametreler)
		{
			return;
		}
		
		parametreler.sayfa = 'en_son';
		
		sonucDoldur(parametreler);
	});
	
	$('body').on('click', '#duzenle_test', function()
	{
		var test_id = $(this).parent().parent().attr('id');
		
		$('#test_id').remove();
		$('body').append('<input type="hidden" id="test_id" value="'+ test_id +'">');
		
		$.ajax
		({
			url : 'ajax/duzenle.php',
			type: 'post',
			data: 'test_id=' + test_id,
			dataType: 'json'	
		})
		.done(function(data, textStatus, jqXHR)
		{
			if (data.hatamiktari == 0)
			{
				$("#icerik #duzenle").remove();
				$("#icerik #arama").after(data.cevap.html);
				
				sorular.yeni_sorularla_doldur(data.cevap.sorular);
				
				formuDoldur(sorular.goruntulenen_soru_format(), sorular.en_ilkinci_soru());
				
				$('#icerik #sonuclar #gizle_goster').trigger('click');	
			}
			else
			{
				$('#sonuclar').hata("Hata Oluştu: " + data.hatalar[0]);
			}
		});
	});
	
	$('body').on('click', '#testi_sil', function()
	{
		var test_id = $(this).parent().parent().attr('id');
		
		$.ajax
		({
			url : 'ajax/sil.php',
			type: 'post',
			data: 'test_id=' + test_id,
			dataType: 'json'	
		})
		.done(function(data, textStatus, jqXHR)
		{
			if (data.hatamiktari == 0)
			{
				var sayfa_no = $('#sayfalar #numaralar #sayfa').html();
				
				var parametreler = aramaFormunuAl();
				
				if (!parametreler)
				{
					return;
				}
				
				parametreler.sayfa = sayfa_no;
				
				sonucDoldur(parametreler);	
			}
			else
			{
				$('#sonuclar').hata("Hata Oluştu: " + data.hatalar[0]);
			}
		});
	});
	
	$('body').on('click', '#bilgiler', function()
	{
		
	});
	
	$('body').on('click', '#sik_ekle', sik_ekle);
	$('body').on('click', '#sik_kaldir', sik_kaldir);
	$('body').on('click', '#evet_hayir', evet_hayir);
	$('body').on("click", '#kaydet_ilerle', sorular, kaydet_ilerle);
	$('body').on('click', '#sorular #onceki_soru', sorular, onceki_soru);
	$('body').on('click', '#sorular #sonraki_soru', sorular, sonraki_soru);
	$('body').on('click', '#sorular #guncelle', sorular, guncelle);
	$('body').on('click', '#sorular #yeni_soru', sorular, yeni_soru);
	$('body').on('click', '#resim_ekle', resim_ekle);
	
	$('body').on("change", "#yeniTestResmi", function(event){
		
		event.stopPropagation();
		event.preventDefault();
		
		var veri = new FormData();
		
		veri.append("yeniTestResmi", event.target.files[0]);
		veri.append('test_id', $('#test_id').val());
		
		$.ajax({
			type: 'POST',
			url: 'ajax/resimdegistir.php',
			data: veri,
			cache: false,
			contentType: false,
			processData: false,
			dataType: "json"})
		.done(function(data, textStatus, jqXHR)
		{	
			if (data.hatamiktari == 0)
			{
				$("#resim img")
				.attr({
						src:data.cevap.kucukresim,
						height:data.cevap.yukseklik,
						width:data.cevap.genislik
					});
			}
			else
			{
				uyariHataVar(data.hatalar);
			}
			
			$("#yeniTestResmi").remove();
		});
	});
	
	
	$('body').on("click", "#duzenle #kaydet", function(event)
	{	
		var secenekler = {
				bos : {
						mesaj : "Doldurmanız gereken alanlar var",
						sinif : "hata_form"
				},
				ozel : [
				        {
				        	sart: sorular.sorular.length <= 0, 
				        	mesaj : 'Soru oluşturmamışsınız'
				        }
				       ]
		};
		
		if ($('#test_bilgileri').denetle(secenekler) > 0)
		{
			return;
		}
		
		var resim_linki = $("#duzenle #test_bilgileri #resim img").attr("src");
		var etiket = $("#duzenle #test_bilgileri #etiket textarea").val();
		var aciklama = $("#duzenle #test_bilgileri #aciklama textarea").val();
		var zorluk = $("#duzenle #test_bilgileri #zorluk select").val();
		
		var test = {
				id: $('#test_id').val(),
				resim: resim_linki,
				etiket: etiket,
				aciklama: aciklama,
				zorluk: zorluk,
				sorular: sorular.sorular
		};
		
		$.ajax
		({
			type: 'POST',
			url: 'ajax/guncelle.php',
			dataType: 'json',
			data: 'test=' + JSON.stringify(test)
		})
		.done(function(data, textStatus, jqXHR)
		{
			if (data.hatamiktari == 0)
			{
				if (data.cevap != undefined && data.cevap.resim != undefined && data.cevap.resim != "")
				{
					$('#duzenle #resim img')
					.attr({
							src:data.cevap.resim,
							height:data.cevap.yukseklik,
							width:data.cevap.genislik
						});
				}
				
				//Düzenleme formunu kapat.
				$('#duzenle #vazgec').trigger('click');
				
				//Testin yeni değerlerini sonuçlar formunda göster.
				$.ajax({
					type: 'POST',
					url : 'ajax/test_bilgileri.php',
					dataType: 'json',
					data: 'test_id=' + $('#test_id').val()
				})
				.done(function(data, textStatus, jqXHR)
				{
					if (data.hatamiktari == 0)
					{
						if (data.cevap.html != "")
						{
							$('tr#' + $('#test_id').val()).html(data.cevap.html);
						}
						else
						{
							$('#sonuclar').hata('Hata oluştu!');
						}
					}
					else
					{
						$('#sonuclar').hata(data.hatalar);
					}
				});
			}
			else
			{
				if (data.hatalar.HATA_TEST.length)
				{
					$('#duzenle').hata(data.hatalar.HATA_TEST[0]);
				}
			}
		});
		
		$("#yeniTestResmi").remove();
	});
	
	$('body').on("click", "#duzenle #vazgec", function(event)
	{
		$('#icerik #duzenle').remove();
		
		$('#icerik #sonuclar #gizle_goster').trigger('click');
	});
})(jQuery);