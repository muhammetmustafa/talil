var kronometre = {saniye: 0, dakika: 0, saat: 0};
var kronometre_konum = $('#bilgiler span:last');
var kronometre_baslat = false;

function kronometre_ilerlet()
{
	kronometre.saniye++;
	
	if (kronometre.saniye == 60)
	{
		kronometre.saniye = 0;
		kronometre.dakika++;
	}
	
	if (kronometre.dakika == 60)
	{
		kronometre.dakika = 0;
		kronometre.saat++;
	}
}

function kronometre_guncelle()
{
	if (!kronometre_baslat)
		return;
	
	kronometre_ilerlet();
	kronometre_konum.html('<b>Geçen Zaman: </b>' + kronometre.saat + ':' + kronometre.dakika + ':' + kronometre.saniye);
	
	setTimeout(kronometre_guncelle, 1000);
}

$('body').on('click', 'input#testi_baslat', function()
{
	$.ajax({
		url: 'sablon/anasayfa/ajax/soru.php',
		type: 'post',
		dataType: 'json',
		data: 'test_id=' + document.location.href.match(/[^0-9]+(\d+)/)[1] + '&sira_no=' + $('#soru_konum b').html()
	})
	.done(function(data, jqXhr, textStatus){
		
		console.log(data.cevap.soru);
		
		kronometre_baslat = true;
		kronometre_guncelle();
	});
});