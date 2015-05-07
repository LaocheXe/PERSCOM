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

	// Get the requests
	getRequests(populateRequestsDatatable);

	// Add a listener to responser to muster click
	$('muster').observe( 'click', function(e) {
	_var = new ipb.Popup( 'booboo', { type: 'pane',
					initial: $('view_muster').innerHTML,
					hideAtStart: false,
					w: '600px' } );
		Event.stop(e);
		return false;
	});

	// Add a listener to respond to the par click
	jQuery('a.par').click(function(e) {

		// Get the elementS
		var element = e.target;

		// Get the ID
		var id = element.id;

		// Set modal view id
		var modal = 'view_par_';
		
		// Present the modal view
		_var = new ipb.Popup( 'booboo', { type: 'pane',
						initial: $(modal.concat(id)).innerHTML,
						hideAtStart: false,
						w: '600px' });
		Event.stop(e);
		return false;
	});

	// Add a listener to respond to the tpr click
	jQuery('a.tpr').click(function(e) {

		// Get the element
		var element = e.target;

		// Get the ID
		var id = element.id;

		// Set modal view id
		var modal = 'view_tpr_';

		// Present the modal view
		_var = new ipb.Popup( 'booboo', { type: 'balloon',
						stem: true,
						initial: $(modal.concat(id)).innerHTML,
						hideAtStart: false,
						attach: { target: $(id), position: 'auto', 'event': 'click' },
						w: '600px' });
		Event.stop(e);
		return false;
	});
});

// Get the requests
function getRequests(executeFunction) {

	// Create our AJAX request to get the data
	var requests_link = ipb.vars['base_url'] + "app=perscom&module=ajax&section=datatables&record=requests";
	new Ajax.Request( requests_link,
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

// Set up requests datatable
function populateRequestsDatatable(data) {

	// Regular table
	var columns = [
		{ "title": "ID", "className": "center", "width": "5%", "data": "primary_id_field"},
		{ "title": "Date", "className": "center", "width": "10%", "data": "date"},
		{ "title": "Soldier", "className": "center", "width": "10%", "data": "members_display_name"},
		{ "title": "Request Type", "className": "center", "width": "15%", "data": "type"},
		{ "title": "Request", "className": "center request", "width": "45%", "data": "description_link", "orderable": false},
		{ "title": "Status", "className": "center", "width": "10%", "orderable": false},
		{ "title": "Save", "className": "center", "width": "5%", "orderable": false},
	];

	// Set up our datatable
	var table = jQuery('#requests').dataTable( {
		"data": data,
		"paging": true,
		"ordering": true,
		"order": [[ 0, "desc" ]],
		"info": false,
		"columns": columns,
		"oLanguage": {
			"sZeroRecords": "No Requests Submitted"
		},
		"columnDefs": [ {
			"targets": -2,
			"data": 'primary_id_field',
			"render": function( data, type, full, meta) {
				return '<select id="option_' + data + '" class="option"><option value="approve">Approve</option><option value="deny">Deny</option><option value="drop">Drop</option></select>';
			}
		},
		{
			"targets": -1,
			"data": 'primary_id_field',
			"render": function( data, type, full, meta) {
				return '<button id="button_' + data + '" primary_id_field="' + data + '" class="save">Save</button>';
			}
		},
		{
			"targets": -3,
			"data": 'description_link',
			"render": function( data, type, full, meta) {
				return '<div style="width: 725px; text-overflow: ellipsis; white-space: nowrap; overflow: hidden;">' + data + '</div>';
			}
		} ],
	});

	// Add a listener to respond to the deletion click
	jQuery('#requests').on('click','.request', function (e) {

		// Get the element
		var element = e.target;

		// Get the ID
		var id = element.id;

		// Set modal view id
		var modal = 'view_';

		// Present the modal view
		_var = new ipb.Popup( 'booboo', { type: 'pane',
						initial: $(modal.concat(id)).innerHTML,
						hideAtStart: false,
						w: '600px' });
		Event.stop(e);
		
		// Return
		return false;
	});

	// Add a listener to respond to the deletion click
	jQuery('#requests').on('click','.save', function (e) {
		
		// Present confirmation
		if (confirm('Are you sure you want to perform this action?')) {

			// Get our status option
			var option = document.getElementById("option_" + e.target.getAttribute('primary_id_field'));
			var option_value = option.options[option.selectedIndex].value;

			// Get our request parameters
			var request = document.getElementById("request_" + e.target.getAttribute('primary_id_field'));

			// Create our AJAX request to get the data
			var link = ipb.vars['base_url'] + "app=perscom&module=ajax&section=datatables";
			new Ajax.Request( link,
			{
				method: 'post',
				evalJSON: 'force',
				parameters: {
					'md5check': ipb.vars['secure_hash'],
					'do': option_value,
					'id': e.target.getAttribute('primary_id_field'),
					'relational': request.getAttribute('relational'),
					'type': request.getAttribute('type')
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
				}
			});
			
			// Reload the window
			location.reload();
		}
		
		// Return
		return false;
	});
}

// Confirm TPR deletion
function confirmTPR(){
	
	// Show confirm message
	var confirmed = confirm("Are you sure you want to delete this Temporary Pass Request?");
	return confirmed;
}
 
// Confirm LOA deleteion
function confirmLOA(){
	
	// Show confirm message
	var confirmed = confirm("Are you sure you want to delete this Leave of Absence?");
	return confirmed;
}