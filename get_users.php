<?php require_once('Connections/conn_vote.php'); ?>
<?php require_once('basic_functions.php'); ?>
<?php
if (isset($_POST['table_name'])) {
	$table_name = GetSQLValueString($_POST['table_name'], "int");
	$users = UpdateUsers($conn_vote, $table_name);
	//$users = GetUsers($conn_vote, $table_name);
	echo json_encode($users);
}
?>