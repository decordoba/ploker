<?php require_once('Connections/conn_vote.php'); ?>
<?php require_once('basic_functions.php'); ?>
<?php
if (isset($_POST['action']) && isset($_POST['table_name'])) {
	$table_name = GetSQLValueString($_POST['table_name'], "int");
    switch ($_POST['action']) {
        case 'start':
        case 'START POLL':
			IncrementRound($conn_vote, $table_name);
            StartStopVoting(1, $conn_vote, $table_name);
			echo 1;
            break;
        case 'stop':
        case 'SHOW RESULTS':
            StartStopVoting(2, $conn_vote, $table_name);
            echo 2;
			break;
    }
}
?>