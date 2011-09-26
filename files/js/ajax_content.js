$(document).ready(function(){
	/* This code is executed after the DOM has been completely loaded */

	/* Caching the tabs into a variable for better performance: */
	var the_tabs = $('#main_menu li a');
	
	the_tabs.click(function(e){
		/* "this" points to the clicked tab hyperlink: */
		var element = $(this);
		
		/* If it is currently active, return false and exit: */
		//if(element.find('#menu_active').length) return false;
		//$('#menu_active a').css('color','#ccc')
		$('#menu_active').attr("id",'');
		Cufon.replace('.call, #menu a, #slogan2, h2 span, h3, .list3', { fontFamily: 'Didact Gothic', hover:true });
		element.parent().attr("id",'menu_active');
		/* Checking whether the AJAX fetched page has been cached: */
		$('#contentHolder').slideUp('fast');
		if(!element.data('cache'))
		{		
			/* If no cache is present, show the gif preloader and run an AJAX request: */
			$('#contentHolder').html('<img src="images/ajax_preloader.gif" width="64" height="64" class="preloader" />');
			$.get(element.attr('href'),function(msg){
				$('#contentHolder').html(msg);
				
				/* After page was received, add it to the cache for the current hyperlink: */
				element.data('cache',msg);
			});
		}
		else	{
			
			 $('#contentHolder').html(element.data('cache'));
		}
		$('#contentHolder').slideDown('slow');
		e.preventDefault();
	})
});
