<?php require_once('Connections/conn_vote.php'); ?>
<?php require_once('basic_functions.php'); ?>
<?php
if (isset($_POST['table_name'])) {
	$table_name = $_POST['table_name'];
	$round = GetGlobalRound($conn_vote, $table_name);
	$results = ReadRowsFromRound($round, $conn_vote, $table_name);
	echo json_encode($results);
}
?>
