( function($)
	{

		$.fn.initSettings = function()
		{
			$('#tabs').show();
			$('#filter_div').hide();
			$('#curve_chart').show();
			var json = JSON.parse($.ajax(
			{
				url : "AjaxController.php?action=GetSettings",
				dataType : "json",
				async : false
			}).responseText);

			var values = new Array();

			for ( i = 0; i < json.TimeSpans.length; i++)
			{
				values.push(new Array(json.TimeSpans[i][0], new Date(json.TimeSpans[i][1] * 1000), new Date(json.TimeSpans[i][2] * 1000)));
			}

			var wantedTemperature = new Array();

			for ( i = 0; i < json.WantedTemperature.length; i++)
			{
				wantedTemperature.push(new Array(new Date(json.WantedTemperature[i][0] * 1000), Number(json.WantedTemperature[i][1])));
			}
						console.log("received wantedTemperature:");
			console.log(json.WantedTemperature);
			
			
			console.log("generated wantedTemperature:");
			console.log(wantedTemperature);
			
			
			var wantedHumidity = new Array();

			for ( i = 0; i < json.WantedHumidity.length; i++)
			{
				wantedHumidity.push(new Array(new Date(json.WantedHumidity[i][0] * 1000), Number(json.WantedHumidity[i][1])));
			}

			var x,
			    y,
			    selectedRowIndex;

			$('#saveTemperatureButton').off('click').on('click', function()
			{
				console.log("save wantedTemperature:");
				console.log(wantedTemperature);
				$.ajax(
				{
					type : "POST",
					dataType : "json",
					url : "AjaxController.php?action=SetWantedTemperature",
					data :
					{
						data : JSON.stringify(wantedTemperature)
					},
					success : function(data)
					{
						alert('success');
					},
					error : function(e)
					{

					}
				});
			});

			$('#saveHumidityButton').off('click').on('click', function()
			{

				$.ajax(
				{
					type : "POST",
					dataType : "json",
					url : "AjaxController.php?action=SetWantedHumidity",
					data :
					{
						data : JSON.stringify(wantedHumidity)
					},
					success : function(data)
					{
						alert('success');
					},
					error : function(e)
					{

					}
				});
			});
			$('#saveButton').off('click').on('click', function()
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


			$('#clearHumidityButton').off('click').on('click', function(event, ui)
			{
				$("#clear_menu").dialog(
				{
					resizable : false,
					height : "auto",
					width : 400,
					modal : true,
					buttons :
					{
						"Clear" : function()
						{
							wantedHumidity = [];
							drawHumidityChart();
							$(this).dialog("close");
						},
						Cancel : function()
						{
							$(this).dialog("close");
						}
					}
				});

			});

			$('#clearTemperatureButton').off('click').on('click', function(event, ui)
			{
				$("#clear_menu").dialog(
				{
					resizable : false,
					height : "auto",
					width : 400,
					modal : true,
					buttons :
					{
						"Clear" : function()
						{
							wantedTemperature = [];
							drawTemperatureChart();
							$(this).dialog("close");
						},
						Cancel : function()
						{
							$(this).dialog("close");
						}
					}
				});

			});

			$('#addHumidityButton').off('click').on('click', function(event, ui)
			{
				$("#add_humidity_menu").dialog(
				{
					resizable : false,
					height : "auto",
					width : 400,
					modal : true,
					buttons :
					{
						"Add" : function()
						{
							var time = ToDate($('#humidity_time').val());
							var value = $('#humidity_value').val();
							wantedHumidity.push([time, value]);
							drawHumidityChart();
							$(this).dialog("close");
						},
						Cancel : function()
						{
							$(this).dialog("close");
						}
					}
				});

			});

			$('#addTemperatureButton').off('click').on('click', function(event, ui)
			{
				$("#add_temperature_menu").dialog(
				{
					resizable : false,
					height : "auto",
					width : 400,
					modal : true,
					buttons :
					{
						"Add" : function()
						{
							var time = ToDate($('#temperature_time').val());
							var value = $('#temperature_value').val();
							wantedTemperature.push([time, value]);
							drawTemperatureChart();
							$(this).dialog("close");
						},
						Cancel : function()
						{
							$(this).dialog("close");
						}
					}
				});
			});

			$('#addIntervalButton').off('click').on('click', function(event, ui)
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
								var end = dateAdd(start, 'minute', duration);
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

			$('#addButton').off('click').on('click', function(event, ui)
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
			google.charts.setOnLoadCallback(drawTemperatureChart);
			google.charts.setOnLoadCallback(drawHumidityChart);

			function drawTemperatureChart()
			{
				var data = prepareTimes(['Time', 'Temperature'], wantedTemperature);
				var options =
				{
					vAxis :
					{
						minValue : 0,
						maxValue : 50
					},
					hAxis :
					{
						minValue : new Date(0, 0, 0, 0, 0, 0),
						maxValue : new Date(0, 0, 0, 23, 59, 59),
						format : 'HH:mm'
					},
					'chartArea' :
					{
						'width' : '92%',
						'height' : '80%'
					},
					legend : 'none',
					isStacked : true,
					'tooltip' :
					{
						trigger : 'none'
					}
				};

				var chart = new google.visualization.AreaChart(document.getElementById('temperature_chart'));

				chart.draw(data, options);
			}

			function prepareTimes(firstline, array)
			{
				var copy = array.slice(0);
				var ordered = copy.sort(function(a, b)
				{
					return a[0] > b[0];
				});
				if (copy.length == 0)
					ordered.push([new Date(0, 0, 0, 12, 0, 0), 0]);
				var last = ordered[ordered.length - 1];
				var result = new Array();

				result.push(firstline);
				if (ordered.length > 0)
				{

					result.push([new Date(0, 0, 0, 0, 0, 0), Number(last[1])]);

					for ( i = 0; i < ordered.length; i++)
					{
						if (i == 0)
						{
							result.push([ordered[i][0], Number(last[1])]);
						}

						else
						{
							result.push([ordered[i][0], Number(ordered[i-1][1])]);
						}

						result.push(ordered[i]);

					}

					result.push([new Date(0, 0, 0, 23, 59, 59), Number(last[1])]);

				}
				return google.visualization.arrayToDataTable(result);
			}

			function drawHumidityChart()
			{

				var data = prepareTimes(['Time', 'Humidity'], wantedHumidity);

				var options =
				{
					vAxis :
					{
						minValue : 0,
						maxValue : 100
					},
					hAxis :
					{
						minValue : new Date(0, 0, 0, 0, 0, 0),
						maxValue : new Date(0, 0, 0, 23, 59, 59),
						format : 'HH:mm'
					},
					'chartArea' :
					{
						'width' : '92%',
						'height' : '80%'
					},
					legend : 'none',
					isStacked : true,
					'tooltip' :
					{
						trigger : 'none'
					}
				};

				var chart = new google.visualization.SteppedAreaChart(document.getElementById('humidity_chart'));

				chart.draw(data, options);
			}

			function drawChart()
			{

				var container = document.getElementById('channel_chart');
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

			}

			return this;
		};

	}(jQuery));
