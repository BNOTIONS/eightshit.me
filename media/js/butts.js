jQuery(document).ready(function($) {
  $('#twit_auth').click(function(e) {
    e.preventDefault();
    window.windo = window.open('/index.php/twitAuth?artist=yea', 'auth', 'status=0,toolbar=0,location=0,resizable=0,height=500,width=500,scrollbars=0');
    window.dickCallback = function() {
      window.windo.close();
	  window.location.href = window.location.href;
    };
  });
});