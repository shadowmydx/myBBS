<?php
require_once 'protected/config.php';
require_once 'protected/GenerUtil.php';
if (!isset($_GET['nowIssuePage'])) {
	$_GET['nowIssuePage'] = 0;               
}
?>
<html>
	<head>
		<meta charset='utf-8' />
		<title>
			<?php showSubTitle($_GET['sub_id']);?>
		</title>
	</head>
	<body>
		<?php showIssue($_GET['sub_id'],$_GET['nowIssuePage']);?> <br />
		<?php if (isset($_SESSION['user'])):?>
			<form action="protected/publishIssue.php" method="POST">
				content <br />
				<textarea rows="10" cols="30" name="content" ></textarea>
				<input type="submit" value="submit">
				<?php echo '<input type="hidden" name="sub_id" value="'. $_GET['sub_id'].'" >';?>
			</form>
		<?php endif?>
	</body>
</html>

