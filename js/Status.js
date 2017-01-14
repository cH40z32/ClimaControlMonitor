( function($)
	{

		$.fn.initStatus = function()
		{
			var lastTimestamp = "";
			drawTable();
			function drawTable()
			{

				var response = $.ajax(
				{
					url : "AjaxController.php?action=GetLastMeasure" + lastTimestamp,
					dataType : "json",
					async : true
				}).done(function(json)
				{ 
					if (json != "")
					{

						$('#current_temperature').html(json.Temperature + '&deg;');
						$('#wanted_temperature').html(json.WantedTemperature + '&deg;');
						$('#current_humidity').html(json.Humidity + '%');
						$('#wanted_humidity').html(json.WantedHumidity + '%');
						lastTimestamp = "&LastTimestamp=" + json.Timestamp;

					}
				}).always(function()
				{
					drawTable();
				});

			}

			return this;
		};

	}(jQuery));
