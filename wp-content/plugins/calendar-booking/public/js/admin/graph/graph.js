jQuery( document ).ready( function( $ ) {

	const { __ } = wp.i18n;

	var data = {
		'action':'cbsb_get_latest_bookings'
	};

	$.post( ajaxurl, data, function( response ) {

		if (response.code == 200 && response.status == 'success') {

			// console.log(response);

			$('.yesterday').html(__('Yesterday', 'calendar-booking') + ' <strong class="number">' + response.yesterday + '</strong>');
			$('.today').html(__('Today', 'calendar-booking') + ' <strong class="number">' + response.today + '</strong>');

			// Data for graph on dashboard
			var dataPointsArray = [];
			var date = new Date();
			var hasBookings = false;
			$.each(response.graph, function (day, count) {

				if (count > 0) {
					hasBookings = true;
				}

				$dataPoint = { x: new Date(day), y: count}
				dataPointsArray.push($dataPoint);
			});

			if (hasBookings) {
				$('#loading-bookings').hide();
				$('#has-bookings').show();

				var options = { 
					data: [
						{
							type: "line",
							markerType: "circle",
							markerColor: "#ffffff",
							markerBorderColor: "#1480e6",
							markerBorderThickness: 2,
							lineColor: "#b9bfc5",
							xValueType: "dateTime",
							dataPoints: dataPointsArray
						}
					],
					axisX:{
						labelFontColor: "#5b636a",
						gridThickness: 1,
						gridColor: "#d1d5da",
						tickColor: "#ffffff",
						lineColor: "#ecf0f5",
						labelFormatter: function (e) {
							return CanvasJS.formatDate( e.value, "DD-MMM");
						},
					},
					axisY:{
						labelFontColor: "#ffffff",
						gridThickness: 0,
						lineColor: "#ffffff",
						tickLength: 0,
					},
					toolTip:{   
						content:  "<div class='\"'tooltip_wrapper'\"'><div class='\"'tooltip_header'\"'>{x}</div><div class='\"'tooltip_body'\"'>{y} " + __('Bookings', 'calendar-booking') + "</div></div>",
						cornerRadius: 6
					},
					animationEnabled: true,
					animationDuration: 1000,
				};

				$("#chartContainer").CanvasJSChart(options);

			} else {
				$('#loading-bookings').hide();
				$('#no-bookings').show();
			}
		}

	});
});