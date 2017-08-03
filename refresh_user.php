<?php require_once('Connections/conn_vote.php'); ?>
<?php require_once('basic_functions.php'); ?>
<?php
if (isset($_POST['table_name'])) {
	$table_name = $_POST['table_name'];
	$user_name = $_POST['user_name'];
	return UpdateLastConnection($user_name, $conn_vote, $table_name);
}
?>