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
			console.log(json);
			var values = new Array();

			for ( i = 0; i < json.TimeSpans.length; i++)
			{
				values.push(new Array(json.TimeSpans[i][0], new Date(json.TimeSpans[i][1] * 1000), new Date(json.TimeSpans[i][2] * 1000)));
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

			return this;
		};

	}(jQuery));

