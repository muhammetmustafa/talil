(function($)
{	
	var sorular = new SoruYoneticisi();
	
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
					
		$.ajax({	
			type: 'POST',
			url: '../ajax/resimyukle.php',
			data: veri,
			cache: false,
			contentType: false,
			processData: false,
			dataType: "json"})
		.done(function(data, textStatus, jqXHR)
		{	
			if (data.hatamiktari == 0)
			{
				$("#resim img").attr({src:data.cevap.kucukresim});
			}
			else
			{
				$('#test_bilgileri').hata(data.hatalar, 'hataBaslik');
			}
			
			$("#yeniTestResmi").remove();
		});
	});
	
	
	$('body').on("click", "#test_olustur #kaydet", function(event)
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
		
		var test = {
				resim : $("#resim img").attr("src"),
				etiket : $("#etiket textarea").val(),
				aciklama : $("#aciklama textarea").val(),
				zorluk : $("#zorluk select").val(),
				sorular : sorular.sorular 
		};
		
		$.ajax
		({
			type: 'POST',
			url: '../ajax/olustur.php',
			dataType: 'json',
			data: 'olustur=test' + '&test=' + JSON.stringify(test)
		})
		.done(function(data, textStatus, jqXHR)
		{
			if (data.hatamiktari == 0)
			{
				$('#test_bilgileri').basari("Test Kaydedildi");
			}
			else
			{
				if (data.hatalar.HATA_TEST.length)
				{
					//TODO:
				}
			}
		});
		
		$("#yeniTestResmi").remove();
	});
})(jQuery);