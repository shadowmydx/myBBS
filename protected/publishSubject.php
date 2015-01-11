<?php

require_once 'config.php';
require_once 'GenerUtil.php';

if (!isset($_SESSION['user'])) {
	header("Location:../index.php");
}
publishSubject($_POST['name'], $_POST['content']);
header("Location:../index.php");
?>