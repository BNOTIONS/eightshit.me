window.dickCallback = function(){};

jQuery(document).ready(function($) {
	var windo;
	
	$('.auth_button a').click(function(e) {
		e.preventDefault();
		window.dickCallback = function(e) {
			if(windo) windo.close();
			$('.auth_msg').remove();
			$('.auth_button').remove();
			$('.submit_form').show();
		};
		windo = window.open('/index.php/twitAuth?artist=yea', 'auth', 'status=0,toolbar=0,location=0,resizable=0,height=500,width=500,scrollbars=0');
	});
	
});