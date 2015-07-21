/*
+--------------------------------------------------------------------------
|   PERSCOM v1.0
|   =============================================
|   by 3rd Infantry Division (Erickson)
|   Copyright 2014-2015 Third Infantry Division
|   http://www.3rdinf.us
+--------------------------------------------------------------------------
*/

// When the document is ready
jQuery(document).ready(function() {

	// Populate our datatables
	getServiceRecord(populateServiceRecordDatatable);	

	// Populate the datatable when the dropdown changes indexes
	jQuery("#application").change(function() {
       
       // Call update function
       jQuery('#service_record').dataTable().fnDestroy();
       getServiceRecord(populateServiceRecordDatatable);
    });

    // Set up the enlistment trends chart
    setUpEnlistmentTrendsChart();

    // Set up most active recruiting medium
    setUpRecruitingMediumsChart();
});

function setUpEnlistmentTrendsChart() {

	// Set up our ajax request
	var url = ipb.vars['base_url'] + "app=perscom&module=ajax&section=datatables&record=enlistment";
	new Ajax.Request( url,
	{
		method: 'get',
		evalJSON: 'force',
		parameters: {
			'md5check': ipb.vars['secure_hash']
		},
		onSuccess: function(t)
		{
			if( Object.isUndefined( t.responseJSON ) ) {

				// Show that the ajax request was bad
				alert( "Bad Request" );
			}
			else if ( t.responseJSON['error'] ) {

				// Show the error
				alert( t.responseJSON['error'] );
			}
			else {

				// Set up data
				var data = {
				    labels: ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"],
				    datasets: [
				    	{
				            label: "Wesbite Registrations",
				            fillColor: "rgba(0,122,255,0.2)",
				            strokeColor: "rgba(70,122,255,1)",
				            pointColor: "rgba(0,122,255,1)",
				            pointStrokeColor: "#fff",
				            pointHighlightFill: "#fff",
				            pointHighlightStroke: "rgba(0,122,255,1)",
				            data: [t.responseJSON['members']['January'].length, t.responseJSON['members']['February'].length, t.responseJSON['members']['March'].length, t.responseJSON['members']['April'].length, t.responseJSON['members']['May'].length, t.responseJSON['members']['June'].length, t.responseJSON['members']['July'].length, t.responseJSON['members']['August'].length, t.responseJSON['members']['September'].length, t.responseJSON['members']['October'].length, t.responseJSON['members']['November'].length, t.responseJSON['members']['December'].length]
				        },
				        {
				            label: "Enlistment Applications",
				            fillColor: "rgba(76,217,100,0.2)",
				            strokeColor: "rgba(76,217,100,1)",
				            pointColor: "rgba(76,217,100,1)",
				            pointStrokeColor: "#fff",
				            pointHighlightFill: "#fff",
				            pointHighlightStroke: "rgba(76,217,100,1)",
				            data: [t.responseJSON['enlistment']['January'].length, t.responseJSON['enlistment']['February'].length, t.responseJSON['enlistment']['March'].length, t.responseJSON['enlistment']['April'].length, t.responseJSON['enlistment']['May'].length, t.responseJSON['enlistment']['June'].length, t.responseJSON['enlistment']['July'].length, t.responseJSON['enlistment']['August'].length, t.responseJSON['enlistment']['September'].length, t.responseJSON['enlistment']['October'].length, t.responseJSON['enlistment']['November'].length, t.responseJSON['enlistment']['December'].length]
				        }
				    ]
				};	

				// Get the height and width of the parent div
				var tdHeight = document.getElementById('enlistment_trends_chard_td').clientHeight;
				var tdWidth = document.getElementById('enlistment_trends_chard_td').clientWidth;

				// Get the canvas and its context
				var canvas = document.getElementById("enlistment_trends_chart");
				var ctx = canvas.getContext("2d");

				// Set the attributes
				canvas.setAttribute("height", tdHeight - 80);
				canvas.setAttribute("width", tdWidth - 40);

				// Draw the chart
				var myLineChart = new Chart(ctx).Line(data);

				// Generate our legend
				document.getElementById("enlistment_trends_chart_legend").innerHTML = myLineChart.generateLegend();
			}
		}
	});
}

function setUpRecruitingMediumsChart() {

	// Set up our ajax request
	var url = ipb.vars['base_url'] + "app=perscom&module=ajax&section=datatables&record=recruiting";
	new Ajax.Request( url,
	{
		method: 'get',
		evalJSON: 'force',
		parameters: {
			'md5check': ipb.vars['secure_hash']
		},
		onSuccess: function(t)
		{
			if( Object.isUndefined( t.responseJSON ) ) {

				// Show that the ajax request was bad
				alert( "Bad Request" );
			}
			else if ( t.responseJSON['error'] ) {

				// Show the error
				alert( t.responseJSON['error'] );
			}
			else {

				// Create an array of random colors to choose from
				var colors = ["#4CD964", "#007AFF", "#FF2D55", "#FF3B30", "#C86EDF", "#8E8E93", "#5856D6"];

				// Create our data array
				var data = new Array();

				// Loop through our recruiting mediums
				for (var property in t.responseJSON) {

					// If the recruiting medium object has the property we are looking for
				    if (t.responseJSON.hasOwnProperty(property)) {
				        
				        // Create our object
						var item = {value: t.responseJSON[property], color: colors[Math.floor(Math.random() * colors.length)], label: property};
		
						// Add the item to the array
						data.push(item);
				    }
				}

				// Get the height and width of the parent div
				var tdHeight = document.getElementById('recruiting_mediums_chart_td').clientHeight;
				var tdWidth = document.getElementById('recruiting_mediums_chart_td').clientWidth;

				// Get the canvas and its context
				var canvas = document.getElementById("recruiting_mediums_chart");
				var ctx = canvas.getContext("2d");

				// Set the attributes
				canvas.setAttribute("height", tdHeight - 100);
				canvas.setAttribute("width", tdWidth - 40);

				// Draw the chart
				var myRadarChart = new Chart(ctx).Doughnut(data);

				// Generate our legend
				document.getElementById("recruiting_mediums_chart_legend").innerHTML = myRadarChart.generateLegend();
			}
		}
	});
}

function getServiceRecord(executeFunction) {

	// Create our AJAX request to get the data
	var service_record_link = ipb.vars['base_url'] + "app=perscom&module=ajax&section=datatables&record=service&id=" + jQuery("#application").val();
	new Ajax.Request( service_record_link,
	{
		method: 'get',
		evalJSON: 'force',
		parameters: {
			'md5check': ipb.vars['secure_hash']
		},
		onSuccess: function(t)
		{
			if( Object.isUndefined( t.responseJSON ) ) {

				// Show that the ajax request was bad
				alert( "Bad Request" );
			}
			else if ( t.responseJSON['error'] ) {

				// Show the error
				alert( t.responseJSON['error'] );
			}
			else {	

				// If a function is defined
				if (executeFunction) {

					// Call the function
					executeFunction(t.responseJSON);
				}
			}
		}
	});	
} 

function populateServiceRecordDatatable(data) {
		
	// Regular table
	var columns = [
		{ "title": "Date", "width": "10%", "data": "date"},
		{ "title": "Entry", "width": "90%", "data": "entry", "orderable": false},
	];

	// Set up our datatable
	var table = jQuery('#service_record').dataTable( {
		"data": data,
		"paging": true,
		"ordering": true,
		"order": [[ 0, "desc" ]],
		"info": false,
		"columns": columns,
		"oLanguage": {
			"sZeroRecords": "No Service Record Found"
		}
	});
}