<html>
	<head>
		<title>BE CAREFUL</title
	</head>
	<body>
		<?php if(isset($error)): ?>
		<h1><?php echo $error ?></h1>
		<?php endif; ?>
		<form method="POST">
			USERNAME: <input name="username"><br />
			PASWORDH: <input type="password" name="password" /><br />
			<input type="submit" />
		</form>
	</body>
</html>