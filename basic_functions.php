<?php
if (!function_exists("GetSQLValueString")) {
    function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") 
    {
		$theValue = get_magic_quotes_gpc() ? stripslashes($theValue) : $theValue;
		$theValue = function_exists("mysql_real_escape_string") ? mysql_real_escape_string($theValue) : mysql_escape_string($theValue);
		switch ($theType) {
			case "text":
			case "string":
				$theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
				break;    
			case "long":
			case "int":
				$theValue = ($theValue != "") ? intval($theValue) : "NULL";
				break;
			case "double":
				$theValue = ($theValue != "") ? "'" . doubleval($theValue) . "'" : "NULL";
				break;
			case "date":
				$theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
				break;
			case "defined":
				$theValue = ($theValue != "") ? $theDefinedValue : $theNotDefinedValue;
				break;
		}
		return $theValue;
	}
}

if (!function_exists("GetGlobalRound")) {
	function GetGlobalRound($conn_vote, $table_name) 
	{
		$query_get_round = "SELECT `round` FROM `{$table_name}` WHERE `user_name` = 'Settings'";
		$round = mysql_query($query_get_round, $conn_vote) or die(mysql_error());
		return array_pop(mysql_fetch_assoc($round));
	}
}
if (!function_exists("GetGlobalState")) {
	function GetGlobalState($conn_vote, $table_name) 
	{
		$query_get_vote = "SELECT `vote` FROM `{$table_name}` WHERE `user_name` = 'Settings'";
		$vote = mysql_query($query_get_vote, $conn_vote) or die(mysql_error());
		return array_pop(mysql_fetch_assoc($vote));
	}
}
if (!function_exists("IncrementRound")) {
	function IncrementRound($conn_vote, $table_name) 
	{
		$num_round = GetGlobalRound($conn_vote, $table_name) + 1;
		$query_set_round = "UPDATE `{$table_name}` SET `round` = {$num_round} WHERE `user_name` = 'Settings'";
		return mysql_query($query_set_round, $conn_vote) or die(mysql_error());
	}
}
if (!function_exists("StartStopVoting")) {
	function StartStopVoting($value, $conn_vote, $table_name)
	{
		$query_start_voting = "UPDATE `{$table_name}` SET `vote` = {$value} WHERE `user_name` = 'Settings'";
		return mysql_query($query_start_voting, $conn_vote) or die(mysql_error());
	}
}
if (!function_exists("createTableUsers")) {
	function createTableUsers($conn_vote, $table_name)
	{
		$query_create_table = "CREATE TABLE `{$table_name}` (
							   `user_name` varchar(200) default  NULL,
							   `join_date` date NOT NULL
							   )";
		return mysql_query($query_create_table, $conn_vote) or die(mysql_error());
	}
}
if (!function_exists("createTable")) {
	function createTable($conn_vote, $table_name)
	{
		$query_create_table = "CREATE TABLE `{$table_name}` (
							   `user_name` varchar(200) default  NULL,
							   `round` int(9) NOT NULL,
							   `vote` int(3) NOT NULL
							   )";
		return mysql_query($query_create_table, $conn_vote) or die(mysql_error());
	}
}

if (!function_exists("createRoom")) {
	function createRoom($conn_vote)
	{
		$max = 100000;
		$max = 100;
		$table_name = rand(0, $max - 1);
		for($i = 0; $i < $max; $i++)
		{
			if(mysql_num_rows(mysql_query("SHOW TABLES LIKE '{$table_name}'")) == 0) 
			{
				createTable($conn_vote, $table_name);
				createTableUsers($conn_vote, "{$table_name}_users");
				return $table_name;
			}
			$table_name = ($table_name + 1) % $max;
		}
		echo "No rooms available. Contact your administrator.";
		return -1;
	}
}
if (!function_exists("UpdateVote")) {
	function UpdateVote($user_name, $round, $vote, $conn_vote, $table_name) 
	{
		$query_update = sprintf("UPDATE `{$table_name}` SET `vote` = %s WHERE `user_name` = %s && `round` = %s",
								GetSQLValueString($vote, "int"),
								GetSQLValueString($user_name, "text"),
								GetSQLValueString($round, "int"));
		return mysql_query($query_update, $conn_vote) or die(mysql_error());
	}
}
if (!function_exists("WriteRow")) {
	function WriteRow($user_name, $round, $vote, $conn_vote, $table_name)
	{
		$query_insert = sprintf("INSERT INTO `{$table_name}` (user_name, round, vote) VALUES (%s, %s, %s)",
								GetSQLValueString($user_name, "text"),
								GetSQLValueString($round, "int"),
								GetSQLValueString($vote, "int"));
		return mysql_query($query_insert, $conn_vote) or die(mysql_error());
	}
}
if (!function_exists("WriteRowUsers")) {
	function WriteRowUsers($user_name, $conn_vote, $table_name)
	{
		$query_insert = sprintf("INSERT INTO `{$table_name}_users` (user_name, join_date) VALUES (%s, NOW())",
								GetSQLValueString($user_name, "text"));
		return mysql_query($query_insert, $conn_vote) or die(mysql_error());
	}
}
if (!function_exists("ReadRowsFromUserName")) {
	function ReadRowsFromUserName($user_name, $conn_vote, $table_name)
	{
		$query_select = sprintf("SELECT * FROM `{$table_name}` WHERE `user_name` = %s",
								GetSQLValueString($user_name, "text"));
		$result = mysql_query($query_select, $conn_vote) or die(mysql_error());
		while($row = mysql_fetch_assoc($result)){
			$array[] = $row;
		}
		return $array;
	}
}
if (!function_exists("ReadRowsFromRound")) {
	function ReadRowsFromRound($round, $conn_vote, $table_name)
	{
		$query_select = sprintf("SELECT * FROM `{$table_name}` WHERE `round` = %s",
							    GetSQLValueString($round, "int"));
		$result = mysql_query($query_select, $conn_vote) or die(mysql_error());
		while($row = mysql_fetch_assoc($result)){
			$array[] = $row;
		}
		return $array;
	}
}
if (!function_exists("ReadRowsFromVote")) {
	function ReadRowsFromVote($vote, $conn_vote, $table_name)
	{
		$query_select = sprintf("SELECT * FROM `{$table_name}` WHERE `vote` = %s",
							    GetSQLValueString($vote, "int"));
		$result = mysql_query($query_select, $conn_vote) or die(mysql_error());
		while($row = mysql_fetch_assoc($result)){
			$array[] = $row;
		}
		return $array;
	}
}
if (!function_exists("GetUsers")) {
	function GetUsers($conn_vote, $table_name)
	{
		$query_select = "SELECT `user_name` FROM `{$table_name}_users`";
		$result = mysql_query($query_select, $conn_vote) or die(mysql_error());
		while($row = mysql_fetch_assoc($result)){
			$array[] = $row;
		}
		return $array;
	}
}
if (!function_exists("TableExists")) {
	function TableExists($conn_vote, $table_name)
	{
		return mysql_num_rows(mysql_query("SHOW TABLES LIKE '{$table_name}'")) > 0;
	}
}
if (!function_exists("UserExists")) {
	function UserExists($user_name, $conn_vote, $table_name)
	{
		  $query_select = sprintf("SELECT `user_name` FROM `{$table_name}_users` WHERE `user_name` = %s",
						          GetSQLValueString($user_name, "text"));
		  $result = mysql_query($query_select, $conn_vote) or die(mysql_error());
		  return mysql_num_rows($result) > 0;
	}
}
if (!function_exists("RowFromUserNameAndRoundExists")) {
	function RowFromUserNameAndRoundExists($user_name, $round, $conn_vote, $table_name)
	{
		  $query_select = sprintf("SELECT * FROM `{$table_name}` WHERE `user_name` = %s && `round` = %s",
								  GetSQLValueString($user_name, "text"),
								  GetSQLValueString($round, "int"));
		  $result = mysql_query($query_select, $conn_vote) or die(mysql_error());
		  return mysql_num_rows($result) > 0;
	}
}
if (!function_exists("CreateSettings")) {
	function CreateSettings($conn_vote, $table_name)
	{
		WriteRowUsers("Settings", $conn_vote, $table_name);
		return WriteRow("Settings", 0, 0, $conn_vote, $table_name);
	}
}
?>