google.charts.load('current',
{
	'packages' : ['corechart', 'controls']
});

google.charts.setOnLoadCallback(logic);
function logic()
{

	var json = JSON.parse($.ajax(
	{
		url : "AjaxController.php?action=GetRange",
		dataType : "json",
		async : false
	}).responseText);

	//setInterval(drawChart, 1500);

	var options =
	{
		//	title : 'Clima Control Monitor',
		curveType : 'function',
		legend :
		{
			position : 'bottom'
		},
		series :
		{
			0 :
			{
				targetAxisIndex : 0,
				lineWidth : 3
			},
			1 :
			{
				targetAxisIndex : 0,
				lineWidth : 2,
				lineDashStyle : [4, 1]
			},
			2 :
			{
				targetAxisIndex : 1,
				lineWidth : 3
			},
			3 :
			{
				targetAxisIndex : 1,
				lineWidth : 2,
				lineDashStyle : [4, 1]
			},
			4 :
			{
				targetAxisIndex : 2,
				lineWidth : 3
			},
			5 :
			{
				targetAxisIndex : 2,
				lineWidth : 3
			},
			6 :
			{
				targetAxisIndex : 2,
				lineWidth : 3
			},
			7 :
			{
				targetAxisIndex : 2,
				lineWidth : 3
			}
		},
		vAxes :
		{
			// Adds titles to each axis.
			0 :
			{
				title : 'Degrees °'
			},
			1 :
			{
				title : 'Humidity %'
			},
			2:
			{
				textStyle: {color: 'transparent'},
				minValue:-100,
				maxValue:0
			}
		},
		hAxis :
		{
			format : 'M d h:m'
		},
		colors : ['#f00f4e', '#d40e46', '#0c47be', '#0b3c9f', 'green', 'blue', 'red', 'yellow']
	};

	function getData(from, to)
	{
		return new google.visualization.DataTable($.ajax(
		{
			url : "AjaxController.php?action=GetLineChartData&from=" + from + "&to=" + to,
			dataType : "json",
			async : false
		}).responseText);
	}

	function setLabels(dataset)
	{

		var temperature = dataset['c'][1]['v'];
		var wantedtemperature = dataset['c'][2]['v'];
		var humidity = dataset['c'][3]['v'];
		var wantedhumidity = dataset['c'][4]['v'];

		$('#current_temperature').html(temperature + '&deg;');
		$('#wanted_temperature').html(wantedtemperature + '&deg;');
		$('#current_humidity').html(humidity + '%');
		$('#wanted_humidity').html(wantedhumidity + '%');

	}

	function drawVisualization(from, to)
	{
		var from = json.min;
		var to = json.max;
		control1 = drawLineChart(from, to);

	}

	function drawLineChart(from, to)
	{
		var data = getData(from, to);
		var dashboard = new google.visualization.Dashboard(document.getElementById('dashboard_div'));

		var slider = new google.visualization.ControlWrapper(
		{
			'controlType' : 'ChartRangeFilter',
			'containerId' : 'filter_div',
			'options' :
			{
				'filterColumnLabel' : 'Time'
			}
		});
		// Create a pie chart, passing some options
		var lineChart = new google.visualization.ChartWrapper(
		{
			'chartType' : 'LineChart',
			'containerId' : 'curve_chart',
			'options' : options
		});
		dashboard.bind(slider, lineChart);
		dashboard.draw(data);
		return slider;
	}

	


	google.charts.setOnLoadCallback(drawVisualization);

}