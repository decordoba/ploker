<?php require_once('Connections/conn_vote.php'); ?>
<?php require_once('basic_functions.php'); ?>
<?php
if (isset($_POST["vote"])) {
    // read current round id
	$table_name = $_POST['table_name'];
	$user_name = $_POST['user_name'];
	$vote = $_POST['vote'];
    $round = GetGlobalRound($conn_vote, $table_name);
    if (RowFromUserNameAndRoundExists($user_name, $round, $conn_vote, $table_name))
    {
		UpdateVote($user_name, $round, $vote, $conn_vote, $table_name);
	}
	else
	{
		WriteRow($user_name, $round, $vote, $conn_vote, $table_name);
    }
	echo "Done";
}
?>