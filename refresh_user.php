<?php require_once('Connections/conn_vote.php'); ?>
<?php require_once('basic_functions.php'); ?>
<?php
if (isset($_POST['table_name'])) {
	$table_name = GetSQLValueString($_POST['table_name'], "int");
	$user_name = GetSQLValueString($_POST['user_name'], "string");
	UpdateLastConnection($user_name, $conn_vote, $table_name);
}
?>