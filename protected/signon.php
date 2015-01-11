<?php
require_once 'config.php';
if (isset($_SESSION['user'])) {
	header("Location:/index.php");
}
$root     = 'bbsroot';
$password = '123';
$connect  = new PDO("mysql:host=".HOST.";dbname=".DB,$root,$password);
if ($connect) {
	$user     = $_POST['username'];
	$password = $_POST['password'];
	$query    = "SELECT * FROM user WHERE (username = :user)";
	$stmt     = $connect->prepare($query);
	$stmt->bindParam(':user',$user);
	$stmt->execute();
	$row = $stmt->fetch(PDO::FETCH_ASSOC);
	
	if ($row) {
		echo 'This user have already existed! <br />';
		echo '<a href="../index.php">Back</a>';
		$connect = null;
	} else {
		$query    = "INSERT INTO user(username,password) VALUES (:user,:password)";
		$stmt     = $connect->prepare($query);
		$stmt->bindParam(':user',$user);
		$stmt->bindParam(':password',$password);
		$stmt->execute();

		$_SESSION['user'] = $user;
		$connect = null;
		header("Location:../index.php");
	}
	
	
}
?>