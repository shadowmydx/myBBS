<?php
require_once 'protected/config.php';
if (isset($_SESSION['user'])) {
	header("Location:/index.php");
}
?>
<html>
	<head>
		<title>login</title>
	</head>
	<body>
		<p>plz enter ur username and password.</p>
		<form action="protected/signon.php" method="post">
		username : 
		<input type="text" name="username" />
		<br />
		password : 
		<input type="password" name="password" />
		<input type="submit" value="Submit" />
		</form>
	</body>
</html>