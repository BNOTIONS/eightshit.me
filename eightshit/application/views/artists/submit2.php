<script>
	window.authenticated = <?php echo json_encode($authenticated) ?>;
</script>

<img src="/media/img/be_artist.png" />

<br />
<h3><a href="http://eightshit.me"><- back to home</a></h3>
<br />

<h1 class="intro">Here is how you, <em>YES YOU</em>, can create an EightShit avatar for some lucky person.</h1>
<ul class="instructions">
	<?php if( ! $authenticated): ?>
	<li class="auth_msg">Click "Autheticate" below so we can let the world know what EightShits you've created.</li>
	<?php endif; ?>
	<li>MS Paint Recommended</li>
	<li>128x128 Image</li>
	<li>Put a word in it (legible)</li>
	<li>Bright colours recommended.</li>
	<li>Submit as a PNG</li>
	<li>Repeat</li>
</ul>

<h2>The community thanks you!</h2>

<?php if(isset($error)): ?>
<p><?php echo $error ?></p>
<?php endif; ?>

<?php if(isset($message)): ?>
<p><?php echo $message ?></p>
<?php endif; ?>

<div class="submit_form" <?php if(!$authenticated) { ?>style="display:none"<?php } ?>>
	<form method="POST" enctype="multipart/form-data">
		<input type="file" name="picture" /><br /><br /><br /><br />
		<input type="image" class="submit" src="/media/img/SUBMIT.png" />
	</form>
</div>

<div class="auth_button" <?php if($authenticated) { ?>style="display:none"<?php } ?>>
	<a href="#"><img src="/media/img/auth_btn.png" /></a>
</div>