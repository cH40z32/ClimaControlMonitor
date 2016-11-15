<?php
error_reporting(E_ALL);
ini_set("display_errors", true);
require_once ('class.Business.php');

$servername = "db10.sysproserver.de";
$username = "clima";
$password = "YeHiepYerc!osDet";
$database = "ClimaControlMonitor";

$database = new Business($servername, $database, $username, $password);
$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : 'overview';
//$database->clear();
//for($i=0;$i<100;$i++)
//$database->insert(rand(20,30),rand(0,100),25,80,1);
?>
<html>
	<head>
		<link rel="stylesheet" type="text/css" href="css/style.css"/>
		<link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/smoothness/jquery-ui.css">

		<link rel="stylesheet" href="css/bootstrap.min.css">
		<link rel="stylesheet" href="css/bootstrap-theme.min.css">
		<link rel="stylesheet" type="text/css" href="js/clockpicker/standalone.css"/>
		<link rel="stylesheet" type="text/css" href="js/clockpicker/clockpicker.css"/>
		<link rel="stylesheet" type="text/css" href="css/style.css"/>
		<link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/smoothness/jquery-ui.css">

		<link href="https://fonts.googleapis.com/css?family=Share+Tech+Mono" rel="stylesheet">

		<link href="https://fonts.googleapis.com/css?family=Share+Tech+Mono" rel="stylesheet">
	</head>
	<body class="container-fluid">
		<nav class="navbar navbar-default">
			<div class="container-fluid">
				<div class="navbar-header">
					<a class="navbar-brand" href="#">Clima Control</a>
				</div>
				<ul class="nav navbar-nav">
					<li class="active">
						<a href="#Charting">Charting</a>
					</li>
					<li>
						<a href="#Settings">Settings</a>
					</li>
					<li>
						<a href="#Status">Status</a>
					</li>
				</ul>
			</div>
		</nav>
		<ul id="menu">
			<li data-type="edit">
				<div>
					Edit
				</div>
			</li>
			<li data-type="delete">
				<div>
					Delete
				</div>
			</li>
		</ul>
		<div id="edit_menu" title="Change Range">

			<div class="input-group clockpicker" data-placement="left" data-align="top" data-autoclose="true">
				<b>Start:</b>
				<input id="edit_start" type="text" class="form-control" value="09:30">
			</div>
			<div class="input-group clockpicker" data-placement="left" data-align="top" data-autoclose="true">
				<b>End:</b>
				<input  id="edit_end" type="text" class="form-control" value="09:30">
			</div>

		</div>

		<div id="new_menu" title="Create Range">
			<div class="input-group">
				<b>Type:</b>
				<select class="form-control" id="type" size="1">
					<option>Illumination</option>
					<option>Ventilation</option>
					<option>ChannelA</option>
					<option>ChannelB</option>
				</select>
			</div>
			<div class="input-group clockpicker" data-placement="left" data-align="top" data-autoclose="true">
				<b>Start:</b>
				<input id="start" type="text" class="form-control" value="09:30">
			</div>
			<div class="input-group clockpicker" data-placement="left" data-align="top" data-autoclose="true">
				<b>End:</b>
				<input  id="end" type="text" class="form-control" value="15:30">
			</div>
		</div>

		<div id="interval_menu" title="Create Range">
			<div class="input-group">
				<b>Type:</b>
				<select class="form-control" id="interval_type" size="1">
					<option>Illumination</option>
					<option>Ventilation</option>
					<option>ChannelA</option>
					<option>ChannelB</option>
				</select>
			</div>
			<div class="input-group clockpicker" data-placement="left" data-align="top" data-autoclose="true">
				<b>Start:</b>
				<input id="interval_start" type="text" class="form-control" value="09:30">
			</div>

			<div class="input-group">
				<b>Interval:</b>
				<input  id="interval" type="text" class="form-control ui-corner-all ui-widget" value="10">
				<b>min</b>
			</div>
			<div class="input-group">
				<b>Duration:</b>
				<input  id="duration" type="text" class="form-control ui-corner-all ui-widget" value="5">
				<b>min</b>
			</div>

			<div class="input-group clockpicker" data-placement="left" data-align="top" data-autoclose="true">
				<b>End:</b>
				<input  id="interval_end" type="text" class="form-control" value="15:30">
			</div>
		</div>

		<div id="tabs">

			<ul>
				<li>
					<a href="#Timespan">Timespan</a>
				</li>
				<li>
					<a href="#Interval">Interval</a>
				</li>
				<li>
					<a href="#Save">Save</a>
				</li>

			</ul>

			<div id="Timespan">

				<div class="input-group">
					<button class="ui-button ui-corner-all ui-widget" type="button" id="addButton"  >
						Add
					</button>
				</div>
			</div>
			<div id="Interval">

				<div class="input-group">
					<button class="ui-button ui-corner-all ui-widget" type="button" id="addIntervalButton"  >
						Add
					</button>
				</div>
			</div>
			<div id="Save">

				<div class="input-group">
					<button class="ui-button ui-corner-all ui-widget" type="button" id="saveButton"  >
						Save
					</button>
				</div>
			</div>

		</div>

		<div id="dashboard_div row">
			<!--Divs that will hold each control and chart-->
			<div id="filter_div"></div>

			<div id="curve_chart"></div>

			<div id="temperature" class="col-md-1">
				<div id="current_temperature">
					27,5&deg;
				</div>
				<div id="wanted_temperature">
					28,5&deg;
				</div>

			</div>
			<div class="col-md-8">
				df
			</div>
			<div id="humidity" class="col-md-1">
				<div id="current_humidity">
					85,5%
				</div>
				<div id="wanted_humidity">
					90,5%
				</div>
			</div>

		</div>

		<script src="js/jquery/external/jquery/jquery.js"></script>
		<script src="js/jquery/jquery-ui.js"></script>
		<script src="js/bootstrap.min.js"></script>
		<script src="js/clockpicker/clockpicker.js"></script>
		<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
		<script type="text/javascript" src="js/Settings.js"></script>
		<script type="text/javascript" src="js/Charting.js"></script>
		<script type="text/javascript" src="js/Status.js"></script>
		<script type="text/javascript">
			( function($)
				{
					$('body').initCharting();
					$('.navbar-nav a').click(function(element)
					{
						console.log();
						switch($(this).html())
						{

							case'Settings':
								$('body').initSettings();
								break;
							case'Status':
								$('body').initStatus();
								break;
							default:
								$('body').initCharting();

								break;
						}
						$('.navbar-nav li').removeClass('active');
						$(this).parent().addClass('active');
					});
				}(jQuery));
		</script>
	</body>
</html>