<?php require_once('Connections/conn_vote.php'); ?>
<?php require_once('basic_functions.php'); ?>
<?php
session_start();

$room = "";
if (isset($_GET['room'])) {
	$room = GetSQLValueString($_GET['room'], "int");
} else {
	if (isset($_SESSION['room'])) {
		$room = GetSQLValueString($_SESSION['room'], "int");
	}
}

$user_name = "";
if (isset($_GET['user_name'])) {
	$user_name = GetSQLValueString($_GET['user_name'], "text");
} else {
	if (isset($_SESSION['user_name'])) {
		$user_name = GetSQLValueString($_SESSION['user_name'], "text");
	}
}

if (isset($_POST['join_room'])) {
	if (strlen(htmlspecialchars($_POST['room'])) <= 0 || strlen(htmlspecialchars($_POST['user_name'])) <= 0)
	{
		if (strlen(htmlspecialchars($_POST['room'])) > 0) {
			$_SESSION['room'] = $room;
		}
		if (strlen(htmlspecialchars($_POST['user_name'])) > 0) {
			$_SESSION['user_name'] = $user_name;
		}
	}
	else
	{
		$room = GetSQLValueString($_POST['room'], "int");
		$user_name = htmlspecialchars($_POST['user_name']);
		$_SESSION['room'] = $room;
		$_SESSION['user_name'] = $user_name;
		if (TableExists($conn_vote, $room) && !UserExists($user_name, $conn_vote, $room)) {
			WriteRowUsers($user_name, $conn_vote, $room);
			header('Location: guest.php');
		}
	}
}
else
{
	session_unset();
}

?>

<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Poll</title>
<link href="style.css" rel="stylesheet" type="text/css" />
<script src="js/jquery-latest.min.js" type="text/javascript"></script>
<link rel="icon" type="image/ico" href="favicon.ico">
</head>

<body>
	<form method="POST">
		<div>
			<label>Name</label>
			<input type="text" name="user_name" placeholder="John Smith" value=<?php Print($user_name); ?>>
		</div>
		<div>
			<label>Room</label>
			<input type="number" name="room" placeholder="00000" value=<?php Print($room); ?>>
		</div>
		<input type="submit" value="Join Room" name="join_room">
	</form>
</body>