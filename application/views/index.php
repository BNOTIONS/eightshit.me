<script src="http://connect.facebook.net/en_US/all.js#xfbml=1"></script>
<div class="floaty_corner">
	<a href="/index.php/artists/draw">
		<img src="/media/img/DRAW.png" />
	</a>
</div>

<div class="floaty_other_corner">
	<fb:like href="http://eightshit.me" layout="button_count" show_faces="true" width="120" font="trebuchet ms"></fb:like>
</div>

<img src="/media/img/title.png" />
<br />
<a href="http://pro.ps/VoqGf">
	<img src="http://www.apple.com/itunes/affiliates/resources/iTunes-Download-English_110x40.png" />
</a>
<br />
<a href="#" id="twit_auth"><img src="/media/img/auth_btn.png" /></a>
<br />
<img src="/media/img/body.png" />
<br />

<!-- <div class="counters">
	<div class="box total"><?php echo $total ?></div>
	<div class="box waiting"><?php echo $waiting ?></div>
	<div class="box waittime"><?php echo $waittime ?></div>
</div> -->

<h1 class="gallery"> FRENZ (<?php echo $total ?>)</h1>
<?php echo $pagi ?>
<ul class='list'>
<?php $count = 0; ?>
<?php foreach($guys as $guy): ?>
<?php 
if ($count%35 == 0){
?>
<!-- <div class='fun'>

</div> -->
<?php } ?>

<li class='avatar'><a href="http://www.twitter.com/<?php echo $guy['screenname'] ?>" target="_blank"><img src='/avatars/<?php echo $guy['image'] ?>' width='128' height='128' /></a> <a href="http://www.twitter.com/<?php echo $guy['screenname'] ?>" target="_blank">@<?php echo $guy['screenname'] ?></a>
<div class='like'>

<!-- <fb:like href="http://eightshit.me/index.php/profile/<?php echo $guy['screenname'] ?>" layout="button_count" show_faces="true" width="120" font="trebuchet ms"></fb:like> -->

</div><!-- LIKE -->

</li>

<?php 
$count++;
endforeach; ?>
</ul>
<?php echo $pagi ?>
<div id="fb-root"></div>
<script>/*
  window.fbAsyncInit = function() {
    FB.init({appId: 'your app id', status: true, cookie: true,
             xfbml: true});
  };
  (function() {
    var e = document.createElement('script'); e.async = true;
    e.src = document.location.protocol +
      '//connect.facebook.net/en_US/all.js';
    document.getElementById('fb-root').appendChild(e);
  }());
  */
</script>

