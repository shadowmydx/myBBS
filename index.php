<?php
require_once 'protected/config.php';
require_once 'protected/GenerUtil.php';

$isUser = false;
if (!isset($_GET['nowPage'])) {
	$_GET['nowPage'] = 0;               // 绗竴娆¤繘鍏ワ紝閭ｄ箞榛樿椤甸潰鏄0椤点�
}
if (isset($_SESSION['user'])) {
	$isUser = true;
} else {
	$isUser = false;
}
?>
<html>
	<head>
		<title>myBBS</title>
		<meta charset="utf-8" />
	</head>
	<body>
		<?php if ($isUser == true):?>
			<a href="logout.php">logout</a> <br />
			<?php showSubject($_GET['nowPage']);?> 
			<form action="protected/publishSubject.php" method="POST">
				title <br />
				<input type="text" name="name" /><br />
				content <br />
				<textarea rows="10" cols="30" name="content" ></textarea>
				<input type="submit" value="submit">
			</form>
		<?php else:?>
			<a href="signon.php">sign on</a>
			<a href="login.php">login</a> <br />
			<?php showSubject($_GET['nowPage']);?>
		<?php endif;?>
		<br />
	</body>
</html>