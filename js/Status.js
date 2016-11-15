( function($)
	{

		$.fn.initStatus = function()
		{
			$('#tabs').hide();
			$('#filter_div').hide();
			$('#curve_chart').hide();

			setInterval(drawTable, 3000);

			function drawTable()
			{

				var json = JSON.parse($.ajax(
				{
					url : "AjaxController.php?action=GetLastMeasure",
					dataType : "json",
					async : false
				}).responseText);

				$('#current_temperature').html(json.Temperature + '&deg;');
				$('#wanted_temperature').html(json.WantedTemperature + '&deg;');
				$('#current_humidity').html(json.Humidity + '%');
				$('#wanted_humidity').html(json.WantedHumidity + '%');

			}

			return this;
		};

	}(jQuery));
