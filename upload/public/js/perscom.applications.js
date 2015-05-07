/*
+--------------------------------------------------------------------------
|   PERSCOM v1.0
|   =============================================
|   by 3rd Infantry Division (Erickson)
|   Copyright 2014-2015 Third Infantry Division
|   http://www.3rdinf.us
+--------------------------------------------------------------------------
*/

// Get global variable
var table;

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
});

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
			if( Object.isUndefined( t.responseJSON ) )
			{
				// Show that the ajax request was bad
				alert( "Bad Request" );
			}
			else if ( t.responseJSON['error'] )
			{
				// Show the error
				alert( t.responseJSON['error'] );
			}
			else
			{	
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