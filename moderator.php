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
<style> .canvasjs-chart-credit { visibility: hidden; } </style>
<title>Ploker</title>
<link href="style.css" rel="stylesheet" type="text/css" />
<script src="js/jquery-latest.min.js" type="text/javascript"></script>
<link rel="icon" type="image/ico" href="favicon.ico">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
<script type="text/javascript" src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/clipboard@1/dist/clipboard.min.js"></script>
</head>

<body>
	<script>
		console.log('Table name:', "<?php Print($table_name); ?>");
		var connected_users = [];
		var users_with_answer = [];
		var current_state = "";
		var choices = ["ONE (1)", "TWO (2)", "THREE (3)", "FIVE (5)", "EIGHT (8)", "THIRTEEN (13)", "TWENTY (20)", "COFFEE", "ENDLESS"];
	
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
			var checkAnswersInterval = window.setInterval(answersCallback, 1000);
			
			function stateMachine(state) {
				switch(state) {
					case "0":
						console.log("State 0");
						break;
					case "1":
						console.log("State 1");
						document.getElementById("barchart").style.display = "none";
						document.getElementById("button_vote").style.display = "none";
						document.getElementById("button_statistics").style.display = "block";
						if (state != current_state) {
							users_with_answer = [];
						}
						break;
					case "2":
						console.log("State 2");
						document.getElementById("barchart").style.display = "block";
						document.getElementById("button_vote").style.display = "block";
						document.getElementById("button_statistics").style.display = "none";
						if (state != current_state) {
							graphCallback();
						}
						break;
				}
				current_state = state;
			}
			
			function answersCallback() {
				var ajaxurl = 'get_results.php';
				data =  {'table_name': <?php Print($table_name); ?>};
				$.post(ajaxurl, data, function (responseText) {
					console.log("Data:", data);
					console.log("Response:", responseText);
					response = JSON.parse(responseText);
					var tmp_users_with_answer = [];
					for (var i=0; i<response.length; i++) {
						if (response[i]["user_name"] == "Settings") {
							continue;
						}
						tmp_users_with_answer.push(response[i]["user_name"]);
					}
					users_with_answer = tmp_users_with_answer;
				});
			}
			
			function graphCallback() {
				var ajaxurl = 'get_results.php';
				data =  {'table_name': <?php Print($table_name); ?>};
				$.post(ajaxurl, data, function (responseText) {
					console.log("Data:", data);
					console.log("Response:", responseText);
					plotStatistics(JSON.parse(responseText));
				});
			}
			
			function plotStatistics(response) {
				if (response.length <= 1) {
					return;
				}
				var votes_survey = {};
				for (var i=0; i<response.length; i++) {
					if (response[i]["user_name"] == "Settings") {
						continue;
					}
					if (votes_survey[response[i]["vote"]] == undefined) {
						votes_survey[response[i]["vote"]] = [response[i]["user_name"]];
					}
					else
					{
						votes_survey[response[i]["vote"]].push(response[i]["user_name"]);
					}
				}
				
				var keys = Object.keys(votes_survey);
				var votes_grouped = [];
				var votes_keys_grouped = [];
				var votes_keys_grouped2 = [];
				for (var i=0; i<keys.length; i++) {
					votes_grouped.push(votes_survey[keys[i]].length);
					//votes_keys_grouped.push({label: keys[i], value: votes_survey[keys[i]].length});
					votes_keys_grouped2.push({label: choices[keys[i]], y: votes_survey[keys[i]].length, users: "- " + votes_survey[keys[i]].join("<br>- ")});
				}
				// plotBarGraph(votes_keys_grouped, keys, votes_survey);
				plotBarChart(votes_keys_grouped2, keys, votes_survey);
			}
			
			function plotBarChart(data, keys, data_users) {
				document.getElementById("chartContainer").style.height = 120 * data.length + "px";
			
				var chart = new CanvasJS.Chart("chartContainer", {
								title: { text:"Results" },
								animationEnabled: true,
								backgroundColor: "rgba(0,0,0,0)",
								dataPointWidth: 75,
								axisX: {
									interval: 1,
									gridThickness: 0,
									labelFontSize: 20,
									labelFontStyle: "normal",
									labelFontWeight: "normal",
									labelFontFamily: "Lucida Sans Unicode" },
								axisY2: {
									interlacedColor: "rgba(1,77,101,.2)",
									gridColor: "rgba(1,77,101,.1)",
									labelFontSize: 20,
									labelFontStyle: "normal",
									labelFontWeight: "normal",
									labelFontFamily: "Lucida Sans Unicode",
									labelImgHeight: 75,
									labelImgWidth: 100,
								},
								data: [ {
									type: "bar",
									toolTipContent: "<h4><b>Votes: {y}</b></h4> <h5>{users}</h5>",
									name: "votes",
									axisYType: "secondary",
									// color: "#014D65",
									dataPoints: data
								} ]
							});

				chart.render();
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
							var user = response[i]["user_name"];
							if (users_with_answer.indexOf(user) != -1) {
								user = "<button class='btn btn-success' type='button'>" + user + "</button>";
							}
							else
							{
								user = "<button class='btn btn-default' type='button'>" + user + "</button>";
							}
							tmp_connected_users.push(user);
						}
						connected_users = tmp_connected_users;
						if (connected_users.length > 0) {
							document.getElementById("participantlist").innerHTML = connected_users.join("");
							console.log(connected_users.join(""));
						}
						else {
							document.getElementById("participantlist").innerHTML = "<button class='btn btn-info' type='button'>" + "Waiting for guests..." + "</button>";
						}
					}
				};
				xhttp.open("POST", "get_users.php", true);
				xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
				xhttp.send("table_name=<?php Print($table_name); ?>");
			}
			
			new Clipboard('.btnclip');
		});
	</script>

	<div  class="brightness" style="margin-top: 30px;" align=center><a href="./index.html"><img src="./logo.png" width="264" height="82" alt="" /></a></div>

	<div style="margin-top: 80px;" class="col-md-4 col-md-offset-4 col-sm-6 col-sm-offset-3" align="center">
		<div class="btn-group btn-group-justified" role="group" aria-label="...">
			<div class="btn-group" role="group">
				<button type="button" class="btn btn-primary">Room Number:</button>
			</div>
			<div class="btn-group" role="group">
				<button type="button" class="btn btn-default"><b><?php echo $table_name; ?></b></button>
			</div>
			<div class="btn-group" role="group">
				<button class="btn btnclip btn-warning" data-clipboard-text="http://<?php echo "10.11.169.188"; //GetClientIP(); ?>/ploker/choose_name.php?room=<?php Print($table_name); ?>">
					Link to clipboard
				</button>
		    </div>
		</div>
		<label id="copyTarget" name="copyTarget" type="text" />
		<div  class="textButton" style="margin-top: 60px; color: #1c9ad6">
			Participant list:
		</div>
		</label>
		<br>
		<!--<textarea style="overflow:auto;resize:none" id="participantlist" name="participantlist" width="500px" cols="auto"></textarea>-->
		<div class="btn-group-vertical" role="group" style="margin-top: 10px;" id="participantlist">
		</div>
		<br>
		<br>
		<div align=center id="button_vote" style="display: block">
			<input class="button Startbutton" type="submit" name="start" value="START POLL" />
		</div>
		<div align=center id="button_statistics" style="display: none">
			<input class="button Startbutton" type="submit" name="stop" value="SHOW RESULTS" />
		</div>
	</div>
	<div align=center id="barchart" style="display: none">
		<div id="chartContainer" style="height: 300px; width: 100%;"></div>
	</div>
</body>