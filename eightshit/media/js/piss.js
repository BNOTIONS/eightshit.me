jQuery(document).ready(function($) {
  $('#twit_auth').click(function(e) {
    e.preventDefault();
    window.windo = window.open('/index.php/twitAuth', 'auth', 'status=0,toolbar=0,location=0,resizable=0,height=500,width=500,scrollbars=0');
    window.dickCallback = function() {
	  if( ! confirm('Opt in to autotweet? (ok = yes, cancel = no)'))
	  {
		$.get('/index.php/dont_say_shit', {}, function(){});
	  }
      alert('awesome great! in the next however long it takes me to draw an avatar you will get it changed');
    };
  });
});
