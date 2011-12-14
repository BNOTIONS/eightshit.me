<h1>BE THE BEST ARTIST! please,</h1>
<ul>
  <li>Use some bright colours</li>
  <li>Write a word also</li>
</ul>
<br />
<h3><a href="http://eightshit.me"><- back to home</a></h3>
<br />

<?php if($userid !== FALSE): ?>
<script src="/media/js/swfobject.js"></script>
<script>
	// For version detection, set to min. required Flash Player version, or 0 (or 0.0.0), for no version detection.
	var swfVersionStr = "10.1.0";
	// To use express install, set to playerProductInstall.swf, otherwise the empty string.
	var xiSwfUrlStr = "/media/swf/playerProductInstall.swf";
	var flashvars = {
		userInfo: "<?php echo $userid ?>"
	};
	var params = {
		allowScriptAccess: "always",
		flashvars: "userInfo=<?php echo $userid ?>",
		base: "http://eightshit.me"
	};
	params.quality = "high";
	params.bgcolor = "#FF00FF";
	params.allowscriptaccess = "sameDomain";
	params.allowfullscreen = "true";

	var attributes = {};
	attributes.id = "Eightshitpaint";
	attributes.name = "Eightshitpaint";
	attributes.align = "middle";

	swfobject.embedSWF(
		"/media/swf/Eightshitpaint.swf", "flashContent",
		"520", "575",
		swfVersionStr, xiSwfUrlStr,
		flashvars, params, attributes);

	// JavaScript enabled so display the flashContent div in case it is not replaced with a swf object.
	swfobject.createCSS("#flashContent", "display:block;text-align:left;");

</script>
<div id="flashContent">
	<p>
		To view this page ensure that Adobe Flash Player version
		10.1.0 or greater is installed.
	</p>
	<script type="text/javascript">
		var pageHost = ((document.location.protocol == "https:") ? "https://" : "http://");
		document.write("<a href='http://www.adobe.com/go/getflashplayer'><img src='"
						+ pageHost + "www.adobe.com/images/shared/download_buttons/get_flash_player.gif' alt='Get Adobe Flash player' /></a>" );
	</script>
</div>
 <noscript>
	<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" width="520" height="575" id="Eightshitpaint">
		<param name="movie" value="/media/swf/Eightshitpaint.swf" />
		<param name="quality" value="high" />
		<param name="bgcolor" value="#FF00FF" />
		<param name="allowScriptAccess" value="sameDomain" />
		<param name="allowFullScreen" value="true" />
		<!--[if !IE]>-->
		<object type="application/x-shockwave-flash" data="/media/swf/Eightshitpaint.swf" width="520" height="575">
			<param name="quality" value="high" />
			<param name="bgcolor" value="#FF00FF" />
			<param name="allowScriptAccess" value="sameDomain" />
			<param name="allowFullScreen" value="true" />
			<param name="base" value="http://eightshit.me" />
			<param name="flashvars" value="userInfo=<?php echo $userid ?>" />
		<!--<![endif]-->
		<!--[if gte IE 6]>-->
			<p>
				Either scripts and active content are not permitted to run or Adobe Flash Player version
				10.1.0 or greater is not installed.
			</p>
		<!--<![endif]-->
			<a href="http://www.adobe.com/go/getflashplayer">
				<img src="http://www.adobe.com/images/shared/download_buttons/get_flash_player.gif" alt="Get Adobe Flash Player" />
			</a>
		<!--[if !IE]>-->
		</object>
		<!--<![endif]-->
	</object>
</noscript>     
<br />
<?php else: ?>

<p>You gotta make sure you are signed in first!!!</p>

<a href="#" id="twit_auth">
	<img src="/media/img/auth_btn.png" alt="wow" />
</a>
<br />
<?php endif ?>

<a href="/index.php/artists/submit">Or UPLOAD a pic</a>
