<?php
require_once 'config.php';
if (isset($_SESSION['user'])) {
	header("Location:/index.php");
}
$user     = 'guest';
$password = '123';
$connect  = new PDO("mysql:host=".HOST.";dbname=".DB,$user,$password);
if ($connect) {
	$user     = $_POST['username'];
	$password = $_POST['password'];
	$query    = "SELECT * FROM user WHERE (username = :user) and (password = :password)";
	$stmt     = $connect->prepare($query);
	$stmt->bindParam(':user',$user);
	$stmt->bindParam(':password',$password);
	$stmt->execute();
	
	$row = $stmt->fetch(PDO::FETCH_ASSOC);
	$connect = null;
	if ($row) {
		$_SESSION['user'] = $row['username'];
		header("Location:../index.php");
	} else {
		echo 'No such user or wrong password!';
		echo '<a href="../index.php">Back</a>';
	}
}

?>