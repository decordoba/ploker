<?php require_once('Connections/conn_vote.php'); ?>
<?php require_once('basic_functions.php'); ?>
<?php
session_start();

if (!isset($_SESSION['moderator_room'])) {
	// crate table with room number that does not exist yet
	$table_name = createRoom($conn_vote);

	// creating Settings row in table (used by Moderator to communicate with Guests)
	CreateSettings($conn_vote, $table_name);
	
	// create session
	$_SESSION['moderator_room'] = $table_name;
} else {
	$table_name = GetSQLValueString($_SESSION['moderator_room'], "int");
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
	<script>
		console.log('Table name:', "<?php Print($table_name); ?>");
		var connected_users = [];
	
		$(document).ready(function(){
			$('.button').click(function(){
				var action = $(this).val();
				var ajaxurl = 'startstopvote.php';
				data =  {'action': action,
				         'table_name': <?php Print($table_name); ?>};
				$.post(ajaxurl, data, function (response) {
					console.log("Data:", data);
					console.log("Response:", response);
					stateMachine(response);
				});
			});
			
			var checkUsersInterval = window.setInterval(myCallback, 1000);
			
			function stateMachine(state) {
				switch(state) {
					case "0":
						console.log("State 0");
						break;
					case "1":
						console.log("State 1");
						break;
					case "2":
						console.log("State 2");
						break;
				}
			}
			
			function myCallback() {
				var xhttp;
				if (window.XMLHttpRequest) {
					// code for modern browsers
					xhttp = new XMLHttpRequest();
				} else {
					// code for old IE browsers
					xhttp = new ActiveXObject("Microsoft.XMLHTTP");
				}
				xhttp.onreadystatechange = function() {
					if (this.readyState == 4 && this.status == 200) {
						console.log("Response", this.responseText);
						var response = JSON.parse(this.responseText);
						var tmp_connected_users = [];
						for (var i=0; i<response.length; i++) {
							if (response[i]["user_name"] == "Settings") {
								continue;
							}
							tmp_connected_users.push(response[i]["user_name"]);
						}
						connected_users = tmp_connected_users;
					}
				};
				xhttp.open("POST", "get_users.php", true);
				xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
				xhttp.send("table_name=<?php Print($table_name); ?>");
			}
		});
	</script>

	<input type="submit" class="button" name="start" value="start" />
	<input type="submit" class="button" name="stop" value="stop" />
	
	
	
</body>