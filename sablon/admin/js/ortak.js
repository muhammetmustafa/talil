function resimSec(resim)
{
	$(resim.selector).remove();
	inputFile = $(document.createElement('input'))
	.attr("type", "file")
	.attr("name", resim.name)
	.attr("id", resim.id)
	.attr("style", "display:none;")
	.attr("accept", "image/jpeg")
	.appendTo('#icerik')
	.trigger('click');
}

$('body').on('click', "#gizle_goster", function(event)
{
	event.preventDefault();
	
	var tiklananLink = this;
	var gizlenecekDiv = $(this).parent().siblings().last();

	gizlenecekDiv.slideToggle('fast', function()
	{
		if ($(this).css('display') == 'none')
			$(tiklananLink).html('&#9633'); 
		else
			$(tiklananLink).html('-');
	});
});

