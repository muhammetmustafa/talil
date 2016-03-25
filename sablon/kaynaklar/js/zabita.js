(function($){
	$.fn.denetle = function(ayarlar)
	{
		var ilkler = {
			bos : {
				sinif : '',
				mesaj : ''
			},
			radyo : {
				bos : '',
				sec : ''
			},
			ozel : []
		};
		var secenekler = $.extend(ilkler, ayarlar);
		var _hata_sayi = 0;
		
		this.each(function()
		{
			var eleman = $(this);
			
			//Uyarıları basmadan önce hepsini tamamen temizleyelim. 
			//Hiç verilecek uyarı olmazsa öyle kalır.
			eleman.find('._uyari_').text('');
			
			if (secenekler.ozel.length > 0)
			{
				for (var _i = 0; _i < secenekler.ozel.length; _i++)
				{
					if (secenekler.ozel[_i].sart)
					{
						eleman.find('._uyari_').append(secenekler.ozel[_i].mesaj + "<br/>");
					}
				}
			}
			
			if (secenekler.radyo.bos != "" && eleman.find('._gerekli_').filter(':radio').length == 0)
			{
				_hata_sayi++;
				
				eleman.find('._uyari_').append(secenekler.radyo.yok + "<br/>");
			}
			
			if (secenekler.radyo.sec != "" && eleman.find('._gerekli_').filter(':radio:checked').length == 0)
			{
				_hata_sayi++;
				
				eleman.find('._uyari_').append(secenekler.radyo.sec + "<br/>");
			}
			
			eleman.find('._gerekli_').filter('select').each(function()
			{
				var gerekli = $(this);
				
				if (gerekli.val() == 0) 
				{
					gerekli.addClass(secenekler.bos.sinif);
					
					_hata_sayi++;
				}
				else
				{
					gerekli.removeClass(secenekler.bos.sinif);
				}
			});
			
			eleman.find('._gerekli_').filter('textarea, :text').each(function()
			{
				var gerekli = $(this);
				
				if (gerekli.val() == "") 
				{
					gerekli.addClass(secenekler.bos.sinif);
					
					_hata_sayi++;
				}
				else
				{
					gerekli.removeClass(secenekler.bos.sinif);
				}
			});
			
			eleman.find('._gerekli_').filter('img').each(function()
			{
				var gerekli = $(this);
				
				if (gerekli.attr("src") == "") 
				{
					gerekli.addClass(secenekler.bos.sinif);
					
					_hata_sayi++;
				}
				else
				{
					gerekli.removeClass(secenekler.bos.sinif);
				}
			});
			
			if (_hata_sayi > 0)
			{
				eleman.find('._uyari_').prepend(secenekler.bos.mesaj + "<br/>").addClass('_zabita_hata_').removeClass('_zabita_basari_');
			}
		});
		
		return _hata_sayi;
	};
	
	$.fn.hata = function(mesaj)
	{
		return this.each(function()
		{
			var eleman = $(this);
			
			var uyari = eleman.find('._uyari_');
			
			/*if (uyari.html().indexOf(mesaj) < 0)
			{
				uyari.append(mesaj + "<br/>").addClass("_zabita_hata_").removeClass("_zabita_basari_");
			}*/
			
			uyari.html(mesaj + "<br/>").addClass("_zabita_hata_").removeClass("_zabita_basari_");
		});
	};
	
	$.fn.basari = function(mesaj)
	{
		return this.each(function()
		{
			var eleman = $(this);
			
			var uyari = eleman.find('._uyari_');
			
			/*if (uyari.html().indexOf(mesaj) < 0)
			{
				uyari.append(mesaj + "<br/>").addClass("_zabita_basari_").removeClass("_zabita_hata_");
			}*/
			
			uyari.html(mesaj + "<br/>").addClass("_zabita_basari_").removeClass("_zabita_hata_");
		});
	};
})(jQuery);