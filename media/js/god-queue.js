jQuery(document).ready(function($){
	$('.accept, .deny').click(function(e) {
		e.preventDefault();

		var id = $(this).attr('img_id');

		$.getJSON('http://eightshit.me/index.php/god/'+$(this).attr('class')+'/'+id, {'username': $('#recipient_'+id).val()}, function() {
			console.log('neato');
			$(this).parents('tr').remove();
		});
	});
});