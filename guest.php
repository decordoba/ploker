<?php require_once('Connections/conn_vote.php'); ?>
<?php require_once('basic_functions.php'); ?>
<?php
session_start();

if (!isset($_SESSION['room']) || !isset($_SESSION['user_name'])) {
	header('Location: choose_name.php');
}
$table_name = $_SESSION['room'];
$user_name = $_SESSION['user_name'];

if (isset($_POST["submit_vote"])) {
    // read current round id
    $round = GetGlobalRound($conn_vote, $table_name);
    if (RowFromUserNameAndRoundExists($user_name, $round, $conn_vote, $table_name))
    {
		UpdateVote($user_name, $round, $_POST['vote'], $conn_vote, $table_name);
	}
	else
	{
		WriteRow($user_name, $round, $_POST['vote'], $conn_vote, $table_name);
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Online Planning Poker</title>
<link href="style.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
<script src="js/jquery-latest.min.js" type="text/javascript"></script>
<script src="js/d3.v3.min.js" type="text/javascript"></script>
<link rel="icon" type="image/ico" href="favicon.ico">
</head>

<body>
	<script>
		console.log('Table name:', "<?php Print($table_name); ?>");
		console.log('User name:', "<?php Print($user_name); ?>");

		$(document).ready(function(){
			var current_state = "";
			var checkUsersInterval = window.setInterval(myCallback, 1000);
			
			function stateMachine(state) {
				switch(state) {
					case "0":
						console.log("State 0 - Wait");
						break;
					case "1":
						console.log("State 1 - Vote");
						break;
					case "2":
						console.log("State 2 - See Statistics");
						var ajaxurl = 'get_results.php';
						data =  {'table_name': <?php Print($table_name); ?>};
						$.post(ajaxurl, data, function (responseText) {
							console.log("Data:", data);
							console.log("Response:", responseText);
							plotStatistics(JSON.parse(responseText));
						});
						break;
				}
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
				for (var i=0; i<keys.length; i++) {
					votes_grouped.push(votes_survey[keys[i]].length);
					votes_keys_grouped.push({label: keys[i], value: votes_survey[keys[i]].length});
				}
				plotBarGraph(votes_keys_grouped, keys, votes_survey);
			}
			
			function plotBarGraph(data, keys, data_users) {
				console.log(data);
				var div = d3.select("body").select("div.bar_graph").attr("class", "toolTip");

				var width = parseInt(d3.select("div.bar_graph_container").style('width'), 10),
					barHeight = width / 10.0,
					barPadding = barHeight / 8.0,
					height = (barHeight + barPadding) * data.length,
					margin = 0,
					valueMargin = 4,
					axisMargin = 4,
					data, bar, svg, scale, xAxis, labelWidth = 0;

			max = d3.max(data, function(d) { return d.value; });

			svg = d3.select('body')
					.select("svg.bar_graph")
					.attr("width", width)
					.attr("height", height);

			bar = svg.selectAll("g");
			if (bar.length != keys.length) {
				bar.remove()
				   .data(data)
				   .enter()
				   .append("g");
			}

			bar.attr("class", "bar")
					.attr("cx", 0)
					.attr("transform", function(d, i) {
						return "translate(" + margin + "," + (i * (barHeight + barPadding) + barPadding) + ")";
					});

			// bar.append("text")
					// .attr("class", "label")
					// .attr("y", barHeight / 2)
					// .attr("dy", ".35em") //vertical align middle
					// .text(function(d){
						// return d.label;
					// }).each(function() {
				// labelWidth = Math.ceil(Math.max(labelWidth, this.getBBox().width));
			// });

			scale = d3.scale.linear()
					.domain([0, max])
					.range([0, width - margin*2 - labelWidth]);

			xAxis = d3.svg.axis()
					.scale(scale)
					.tickSize(-height + 2*margin + axisMargin)
					.orient("bottom");

		bar.append("rect")
				.attr("transform", "translate("+labelWidth+", 0)")
				.attr("height", barHeight)
				.attr("width", function(d){
					return scale(d.value);
				});

    // bar.append("text")
            // .attr("class", "value")
            // .attr("y", barHeight / 2)
            // .attr("dx", -valueMargin + labelWidth) //margin right
            // .attr("dy", ".35em") //vertical align middle
            // .attr("text-anchor", "end")
            // .text(function(d){
                // return (d.value+"%");
            // })
            // .attr("x", function(d){
                // var width = this.getBBox().width;
                // return Math.max(width + valueMargin, scale(d.value));
            // });

    // bar
            // .on("mousemove", function(d){
                // div.style("left", d3.event.pageX+10+"px");
                // div.style("top", d3.event.pageY-25+"px");
                // div.style("display", "inline-block");
                // div.html((d.label)+"<br>"+(d.value)+"%");
            // });
    // bar
            // .on("mouseout", function(d){
                // div.style("display", "none");
            // });

    svg.insert("g",":first-child")
            .attr("class", "axisHorizontal")
            .attr("transform", "translate(" + (margin + labelWidth) + ","+ (height - axisMargin - margin)+")")
            .call(xAxis);

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
						if (current_state != this.responseText) {
							current_state = this.responseText;
							console.log("Response", this.responseText);
							selectDivVisibility(this.responseText);
							stateMachine(this.responseText);
						}
					}
				};
				xhttp.open("POST", "get_voting_state.php", true);
				xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
				xhttp.send("table_name=<?php Print($table_name); ?>");
			}
			
			function selectDivVisibility(state) {
				switch(state) {
						case "1":
							document.getElementById("div_state_0").style.display = "none";
							document.getElementById("div_state_1").style.display = "block";
							document.getElementById("div_state_2").style.display = "none";
							break;
						case "2":
							document.getElementById("div_state_0").style.display = "none";
							document.getElementById("div_state_1").style.display = "none";
							document.getElementById("div_state_2").style.display = "block";
							break;
						case "0":
						default:
							document.getElementById("div_state_0").style.display = "block";
							document.getElementById("div_state_1").style.display = "none";
							document.getElementById("div_state_2").style.display = "none";
				}
			}
		});
	</script>
	<div id="div_state_0" style="display: none">
		<h1>Please wait until the moderator starts the voting session...</h1>
	</div>
	<div id="div_state_1" style="display: none">
		<form action="<?php echo $editFormAction; ?>" id="form1" name="form1" method="POST">
			<div  style="margin-top: 30px;" align=center><img src="./logo.png" width="264" height="82" alt="" /></div>

			<div style="margin-top: 140px; margin-left: 25px" align="center" class="card-selector">
				<input type="radio" name="vote" value="0" id="Poll_1" />
				<label class="drinkcard-cc one" for="Poll_1"></label>

				<input type="radio" name="vote" value="1" id="Poll_2" />
				<label class="drinkcard-cc two" for="Poll_2"></label>
				
				<input type="radio" name="vote" value="2" id="Poll_3" />
				<label class="drinkcard-cc three" for="Poll_3"></label>
				
				<input type="radio" name="vote" value="3" id="Poll_4" />
				<label class="drinkcard-cc five" for="Poll_4"></label>
				
				<input type="radio" name="vote" value="4" id="Poll_5" />
				<label class="drinkcard-cc eight" for="Poll_5"></label>			
				
				<input type="radio" name="vote" value="5" id="Poll_6" />
				<label class="drinkcard-cc thirteen" for="Poll_6"></label>
				
				<input type="radio" name="vote" value="6" id="Poll_7" />
				<label class="drinkcard-cc twenty" for="Poll_7"></label>			
				
				<input type="radio" name="vote" value="7" id="Poll_cof" />
				<label class="drinkcard-cc coffee" for="Poll_cof"></label>

				<input type="radio" name="vote" value="8" id="Poll_inf" />
				<label class="drinkcard-cc inf" for="Poll_inf"></label>
			</div>
			<div style="margin-top: 170px;" align=center><input type="submit" name="submit_vote" value="SUBMIT" class="myButton"></div>
		</form>
	</div>
	<div id="div_state_2" class="bar_graph_container col-md-6 col-md-offset-3 col-sm-8 col-sm-offset-2" style="display: none">
		You should see statistics here
		<div class="bar_graph"></div>
		<svg class="bar_graph"></svg>
	</div>
</body>