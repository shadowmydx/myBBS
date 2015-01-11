<?php

require_once 'config.php';
require_once 'GenerUtil.php';

if (!isset($_SESSION['user'])) {
	header("Location:../index.php");
}
$sub_id  = $_POST['sub_id'];
$content = $_POST['content'];
$username = $_SESSION['user'];
publishIssue($sub_id,$username,$content);
header("Location:../issue.php?sub_id=".$sub_id);
?>