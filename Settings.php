<?php
error_reporting(E_ALL);
ini_set("display_errors", true);
require_once ('class.Database.php');

$servername = "db10.sysproserver.de";
$username = "clima";
$password = "YeHiepYerc!osDet";
$database = "ClimaControlMonitor";

$database = new Database($servername, $database, $username, $password);
$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : 'overview';
//$database->clear();
//for($i=0;$i<100;$i++)
//$database->insert(rand(20,30),rand(0,100),25,80,1);
?>
<html>
	<head>
		<link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap.min.css">
		<link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap-theme.min.css">
		<link rel="stylesheet" type="text/css" href="js/clockpicker/standalone.css"/>
		<link rel="stylesheet" type="text/css" href="js/clockpicker/clockpicker.css"/>
		<link rel="stylesheet" type="text/css" href="css/style.css"/>
		<link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/smoothness/jquery-ui.css">

		<link href="https://fonts.googleapis.com/css?family=Share+Tech+Mono" rel="stylesheet">
	</head>
	<body>
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

		<div id="dashboard_div chartWithOverlay">
			<!--Divs that will hold each control and chart-->
			<div id="curve_chart" ></div>
		</div>
		<script src="js/jquery/external/jquery/jquery.js"></script>
		<script src="js/jquery/jquery-ui.js"></script>
		<script src="js/clockpicker/clockpicker.js"></script>

		<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
		<script type="text/javascript">
			var json = JSON.parse($.ajax(
			{
				url : "AjaxController.php?action=GetSettings",
				dataType : "json",
				async : false
			}).responseText);
			console.log(json);
			var values = new Array();

			for ( i = 0; i < json.length; i++)
			{
				values.push([json[i][0], new Date(json[i][1] * 1000), new Date(json[i][2] * 1000)]);
			}

			//var values = [['Illumination', new Date(0, 0, 0, 12, 0, 0), new Date(0, 0, 0, 13, 30, 0)], ['Illumination', new Date(0, 0, 0, 14, 0, 0), new Date(0, 0, 0, 15, 30, 0)], ['Illumination', new Date(0, 0, 0, 16, 0, 0), new Date(0, 0, 0, 17, 30, 0)], ['Ventilation', new Date(0, 0, 0, 12, 30, 0), new Date(0, 0, 0, 14, 0, 0)], ['Ventilation', new Date(0, 0, 0, 14, 30, 0), new Date(0, 0, 0, 16, 0, 0)], ['Ventilation', new Date(0, 0, 0, 16, 30, 0), new Date(0, 0, 0, 18, 0, 0)], ['ChannelA', new Date(0, 0, 0, 17, 0, 0), new Date(0, 0, 0, 17, 30, 0)], ['ChannelA', new Date(0, 0, 0, 18, 0, 0), new Date(0, 0, 0, 18, 30, 0)], ['ChannelA', new Date(0, 0, 0, 19, 0, 0), new Date(0, 0, 0, 19, 30, 0)], ['ChannelB', new Date(0, 0, 0, 12, 0, 0), new Date(0, 0, 0, 13, 30, 0)], ['ChannelB', new Date(0, 0, 0, 14, 0, 0), new Date(0, 0, 0, 15, 30, 0)], ['ChannelB', new Date(0, 0, 0, 16, 0, 0), new Date(0, 0, 0, 17, 30, 0)]];
			var x,
			    y,
			    selectedRowIndex;

			$('#saveButton').click(function()
			{

				$.ajax(
				{
					type : "POST",
					dataType : "json",
					url : "AjaxController.php?action=SetSettings",
					data :
					{
						data : JSON.stringify(values)
					},
					success : function(data)
					{
						alert('success');
					},
					error : function(e)
					{
						console.log(e);
						alert('no');
					}
				});
			});
			function dateAdd(date, interval, units)
			{
				var ret = new Date(date);
				//don't change original date
				switch(interval.toLowerCase())
				{
					case 'year'   :
						ret.setFullYear(ret.getFullYear() + units);
						break;
					case 'quarter':
						ret.setMonth(ret.getMonth() + 3 * units);
						break;
					case 'month'  :
						ret.setMonth(ret.getMonth() + units);
						break;
					case 'week'   :
						ret.setDate(ret.getDate() + 7 * units);
						break;
					case 'day'    :
						ret.setDate(ret.getDate() + units);
						break;
					case 'hour'   :
						ret.setTime(ret.getTime() + units * 3600000);
						break;
					case 'minute' :
						ret.setTime(ret.getTime() + units * 60000);
						break;
					case 'second' :
						ret.setTime(ret.getTime() + units * 1000);
						break;
					default       :
						ret = undefined;
						break;
				}
				return ret;
			}


		

			$('#addIntervalButton').click(function(event, ui)
			{
				$("#interval_menu").dialog(
				{
					resizable : false,
					height : "auto",
					width : 400,
					modal : true,
					buttons :
					{
						"Create" : function()
						{
							var startDate = ToDate($('#interval_start').val());
							var interval = $('#interval').val();
							var duration = $('#duration').val();

							var endDate = ToDate($('#interval_end').val());

							var iDate = new Date(startDate);

							while (iDate <= endDate)
							{
								var start = iDate;
								var end = dateAdd(start, 'minute', duration)
								iDate = dateAdd(iDate, 'minute', interval);
								values.push([$('#interval_type').val(), start, end]);

							}

							$(this).dialog("close");
							drawChart();
						},
						Cancel : function()
						{
							$(this).dialog("close");
						}
					}
				});

			});

			$('#addButton').click(function(event, ui)
			{
				$("#new_menu").dialog(
				{
					resizable : false,
					height : "auto",
					width : 400,
					modal : true,
					buttons :
					{
						"Create" : function()
						{
							var start = ToDate($('#start').val());
							var end = ToDate($('#end').val());

							values.push([$('#type').val(), start, end]);
							$(this).dialog("close");
							drawChart();
						},
						Cancel : function()
						{
							$(this).dialog("close");
						}
					}
				});

			});

			$('.clockpicker').clockpicker(
			{
				placement : 'top',
				align : 'left',
				donetext : 'Done'
			});
			
			function ToDate(hoursAndMinutes)
			{
				var vals = hoursAndMinutes.split(":");
				return new Date(0, 0, 0, vals[0], vals[1], 0);
			}


			$("#menu").menu(
			{
				select : function(event, ui)
				{

					switch(	$(ui['item'][0]).attr('data-type'))
					{
						case('delete'):
							values.splice(selectedRowIndex, 1);
							break;
						case('edit'):

							$("#edit_menu").dialog(
							{
								resizable : false,
								height : "auto",
								width : 400,
								modal : true,
								buttons :
								{
									"Save" : function()
									{
										values[selectedRowIndex] = [values[selectedRowIndex][0], ToDate($('#edit_start').val()), ToDate($('#edit_end').val())];
										$(this).dialog("close");
										drawChart();
									},
									Cancel : function()
									{
										$(this).dialog("close");
									}
								}
							});

							$('#edit_start').val(values[selectedRowIndex][1].getHours() + ":" + values[selectedRowIndex][1].getMinutes());
							$('#edit_end').val(values[selectedRowIndex][2].getHours() + ":" + values[selectedRowIndex][2].getMinutes());

							break;
					}
					$("#menu").hide();
					drawChart();
				}
			});

			$(document).on("mousemove", function(event)
			{
				x = event.pageX;
				y = event.pageY;
			});

			google.charts.load('current',
			{
				'packages' : ['timeline']
			});
			google.charts.setOnLoadCallback(drawChart);
			
			function drawChart()
			{

				var container = document.getElementById('curve_chart');
				var chart = new google.visualization.Timeline(container);

				var dataTable = new google.visualization.DataTable();

				dataTable.addColumn(
				{

					type : 'string',
					id : 'Channel'
				});

				dataTable.addColumn(
				{
					type : 'date',
					id : 'Start'
				});
				dataTable.addColumn(
				{
					type : 'date',
					id : 'End'
				});

				dataTable.addRows(values);

				var options =
				{
					legend :
					{
						position : 'bottom'
					},
					timeline :
					{
						colorByRowLabel : true
					},
					hAxis :
					{
						minValue : new Date(0, 0, 0, 0, 0, 0),
						maxValue : new Date(0, 0, 0, 23, 59, 59),
						format : 'HH:mm'
					},
					'tooltip' :
					{
						trigger : 'none'
					}

				};

				// The select handler. Call the chart's getSelection() method
				var ignore = false;
				function selectHandler()
				{
					if (!ignore)
					{
						var selectedItem = chart.getSelection()[0];
						if (selectedItem)
						{
							console.log(selectedItem.row);
							$('#menu').css('top', y - 10);
							$('#menu').css('left', x - 10);
							$('#menu').show();
							selectedRowIndex = selectedItem.row;
							// alert('The user selected ' + value);
						}
						ignore = true;
						chart.setSelection([]);
						ignore = false;
					}

				}

				// Listen for the 'select' event, and call my function selectHandler() when
				// the user selects something on the chart.
				google.visualization.events.addListener(chart, 'select', selectHandler);

				chart.draw(dataTable, options);

				$("#tabs").tabs();
			}
		</script>

	</body>
</html>