/*
+--------------------------------------------------------------------------
|   PERSCOM v1.0
|   =============================================
|   by 3rd Infantry Division (Erickson)
|   Copyright 2014-2015 Third Infantry Division
|   http://www.3rdinf.us
+--------------------------------------------------------------------------
*/

// When the document has loaded
jQuery(document).ready(function() {

	// Define our AJAX links in variables that the datatables will use to populate their data from
	var logs_link = ipb.vars['base_url'] + "app=perscom&module=ajax&section=datatables&record=logs";

	// Create our AJAX request to get the data
	new Ajax.Request( logs_link,
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
				alert( "Bad Request" );
			}
			else if ( t.responseJSON['error'] )
			{
				alert( t.responseJSON['error'] );
			}
			else
			{
				jQuery('#logs').dataTable( {
					"data": t.responseJSON,
					"paging": true,
					"ordering": true,
					"order": [[ 0, "desc" ]],
					"info": false,
                                        "iDisplayLength": 50,
					"columns": [
						{ "title": "ID", "width": "5%", "data": "primary_id_field", "className": "center" },
						{ "title": "Date", "width": "8%", "data": "date", "className": "center" },
						{ "title": "Soldier", "width": "8%", "data": "soldier", "className": "center" },
						{ "title": "Log Type", "width": "15%", "data": "type", "className": "center" },
						{ "title": "Description", "width": "40%", "data": "description", "orderable": false, "className": "center" },
						{ "title": "Completed By", "width": "10%", "data": "completed_by", "className": "center" },
						{ "title": "Status", "width": "5%", "data": "status", "className": "center" },
					]
				});					
			}
		}
	});
});