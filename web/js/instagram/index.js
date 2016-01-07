$(function()
{
	$('.wrapper').fadeIn(1000);
	
	$(document).on('focus', '.input-search', function()
	{
		if($.trim($(this).val()) == 'Instagram search') $(this).val('').css('color', '#444');
		else                                            $(this).css('color', '#444');
	});
	
	$(document).on('blur', '.input-search', function()
	{
		if($.trim($(this).val()) == '') $(this).val('Instagram search').css('color', '#DDD');
	});
	
	$(document).on('keyup', '.input-search', function(event)
	{
		if(event.keyCode == 13)
		{
			$.post('?ctl=instagram&atn=filter', {input: $.trim($(this).val().toLowerCase())}, function(response)
			{
				if(response == '') return;

				search(encodeURIComponent(response));
			});
		}
	});
		
	$(document).on('click', '.scroll-top', function()
	{
		$('body, html').animate({scrollTop: 0}, 1000);
	});
	
	$(window).scroll(function() 
	{
		if($(window).scrollTop() + $(window).height() > $(document).height() - 300) 
		{
			var url = $('.button-next-url').attr('url');
			
			if(url == '' || url == undefined) return;
			
			getByUrl(url);
		}
		
		if($(window).scrollTop() == 0) $('.scroll-top').hide();
		else                           $('.scroll-top').show();
	});
});

function search(input)
{
	if($('.div-search-input').css('top') != '0px') $('.div-search-input').animate({top: '-=300'}, 1000);
	
	$('.loading-holder').html('<img src="web/img/instagram/loading.gif" />');

	$('.ajax.div-search-result').fadeOut(0, function()
	{
		$.getJSON('https://api.instagram.com/v1/tags/'+input+'/media/recent?client_id=107e977e54fe43bdb552c427b216f0f7&callback=?', function(tags)
		{
			$.post('?ctl=instagram&atn=tags', {tags: tags}, function(response)
			{
				$.getJSON('https://api.instagram.com/v1/tags/'+input+'/?client_id=107e977e54fe43bdb552c427b216f0f7&callback=?', function(count)
				{
					if(count.data.media_count && count.data.media_count > 16) 
					{
						var media = (count.data.media_count > 1) ? 'images' : 'image';
						
						$('.media-count').html('~'+numberDivider(count.data.media_count)+' '+media);
					}
					else $('.media-count').html('');
				});
				
				$('.div-search-input').promise().done(function()
				{
					$('.loading-holder').html('');
					$('.border').show();
					
					$('.ajax.div-search-result').html(response).fadeIn(800, function()
					{
						var url = $('.button-next-url').attr('url');
						
						if(url == '' || url == undefined) return;
						
						getByUrl(url);
					});
				});
			});
		});
	});
}

function getByUrl(url)
{
	$('.button-next-url').remove();
	
	$.getJSON(url+'&callback=?', function(tags)
	{
		$.post('?ctl=instagram&atn=tags', {tags: tags}, function(response)
		{
			$('.ajax.div-search-result').append(response);
		});
	});
}

function numberDivider(number) 
{  
	number = number.toString(); 
	
	if(number.length > 3) 
	{ 
		var mod    = number.length % 3; 
		var output = (mod > 0 ? (number.substring(0,mod)) : ''); 

		for(i=0 ; i < Math.floor(number.length / 3); i++) 
		{ 
			if((mod == 0) && (i == 0)) output += number.substring(mod+ 3 * i, mod + 3 * i + 3); 
			else                       output += '.' + number.substring(mod + 3 * i, mod + 3 * i + 3);
		}
		
		return output;
	} 
	else return number; 
}