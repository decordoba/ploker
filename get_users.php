<?php require_once('Connections/conn_vote.php'); ?>
<?php require_once('basic_functions.php'); ?>
<?php
if (isset($_POST['table_name'])) {
	$table_name = $_POST['table_name'];
	$users = UpdateUsers($conn_vote, $table_name);  // deletes users that expire
	//$users = GetUsers($conn_vote, $table_name);  // users will never expire
	echo json_encode($users);
}
?>