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
	getAwardRecord(populateAwardRecordDatatable);
	getCombatRecord(populateCombatRecordDatatable);

	// Check if we have an edit element to observe
	if (document.getElementById('edit_soldier')) {
		
		// Create the function to display the award modal
		$('edit_soldier').observe( 'click', function(e) {
			
			// Show our modal view
			_var = new ipb.Popup( 'booboo', { type: 'pane',
							  initial: $('view_edit_soldier').innerHTML,
							  hideAtStart: false,
							  w: '600px',
							  h: 600 } );
				    
			Event.stop(e);	
			
			return false;
		});
	}	
});

// Validate edit soldier file
function validateForm() {

	// Validate date regex
	var regex = /^(0[1-9]|1[012])[- \/.](0[1-9]|[12][0-9]|3[01])[- \/.](19|20)\d\d$/; 

    var enlistment = document.forms["edit_soldier"]["enlistment"].value;
    if (enlistment==null || enlistment=="") {
        alert("Please enter in an enlistment date for the record.");
        return false;
    }
    if (!regex.test(enlistment)) {
        alert("Please enter a valid enlistment date. (MM-DD-YYYY)");
		return false;
    }
    var promotion = document.forms["edit_soldier"]["promotion"].value;
    if (promotion==null || promotion=="") {
        alert("Please enter in a promotion date for the record.");
        return false;
    }
    if (!regex.test(promotion)) {
        alert("Please enter a valid promotion date. (MM-DD-YYYY)");
		return false;
    }
    var induction = document.forms["edit_soldier"]["induction"].value;
    if (induction==null || induction=="") {
        alert("Please enter in an induction date for the record.");
        return false;
    }
    if (!regex.test(induction)) {
        alert("Please enter a valid induction date. (MM-DD-YYYY)");
		return false;
    }
    var c = confirm ('Have you checked to make sure all the information is correct?');
    if (c == false) {
		return false;
    }
}

// Confirm deletion of soldier
function deleteSoldierConfirmation() {
	
	// Confirm
	var c = confirm('Are you sure you want to delete this soldier?');
	if (c == false) {
		return false;
	}
}

// Create function to get HTTP parameters
function getParameterByName(name) {
    name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
    var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
        results = regex.exec(location.search);
    return results == null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
}

function getServiceRecord(executeFunction) {

	// Create our AJAX request to get the data
	var service_record_link = ipb.vars['base_url'] + "app=perscom&module=ajax&section=datatables&record=service&id=" + getParameterByName('id');
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

function getAwardRecord(executeFunction) {

	// Create our AJAX request to get the data
	var award_record_link = ipb.vars['base_url'] + "app=perscom&module=ajax&section=datatables&record=award&id=" + getParameterByName('id');
	new Ajax.Request( award_record_link,
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

function getCombatRecord(executeFunction) {

	// Create our AJAX request to get the data
	var combat_record_link = ipb.vars['base_url'] + "app=perscom&module=ajax&section=datatables&record=combat&id=" + getParameterByName('id');
	new Ajax.Request( combat_record_link,
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

	// If the user can edit
	if (ipb.vars['allowEdit']) {
		
		// Add delete column
		var columns = [
			{ "title": "Date", "className": "center", "width": "10%", "data": "date"},
			{ "title": "Entry", "width": "60%", "data": "entry", "orderable": false},
			{ "title": "Citation / OPORD", "className": "citation", "width": "20%", "data": "citation", "orderable": false},
			{ "title": "Delete", "className": "center delete", "width": "10%", "orderable": false}
		];
	}
	else {
		
		// Regular table
		var columns = [
			{ "title": "Date", "width": "10%", "data": "date"},
			{ "title": "Entry", "width": "70%", "data": "entry", "orderable": false},
			{ "title": "Citation / OPORD", "className": "citation", "width": "20%", "data": "citation", "orderable": false}
		];
	}

	// Set up our datatable
	var table = jQuery('#service_record').dataTable( {
		"data": data,
		"paging": true,
		"ordering": true,
		"order": [[ 0, "desc" ]],
		"info": false,
		"columns": columns,
		"oLanguage": {
			"sZeroRecords": "No Service Record Entries Found"
		},
		"columnDefs": [ {
			"targets": -1,
			"data": null,
			"defaultContent": "<u><a href='#'>Delete</a></u>"
		} ],
		"fnRowCallback": function( nRow, aData, iDisplayIndex, iDisplayIndexFull) {
			jQuery(nRow).attr('id', aData['primary_id_field']);
			return nRow;
		}
	});

	// Add a listener to respond to the deletion click
	jQuery('#service_record').on('click','.delete', function (e) {
		
		// Present confirmation
		if (confirm('Are you sure you want to delete this record?')) {
			
			// Create our AJAX request to get the data
			var delete_link = ipb.vars['base_url'] + "app=perscom&module=ajax&section=datatables";
			new Ajax.Request( delete_link,
			{
				method: 'post',
				evalJSON: 'force',
				parameters: {
					'md5check': ipb.vars['secure_hash'],
					'id': jQuery(this).closest('tr').attr('id'),
					'do': 'delete',
					'type': 'Record',
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
			
			// Delete the row
			var row = jQuery(this).closest("tr").get(0);
			var pos = table.fnGetPosition(row); 
			table.fnDeleteRow(pos);
		}
		
		// Return
		return false;
	});
	
	// Add a listener to respond to the citation click
	jQuery('#service_record').on('click','.citation', function (e) {
	
		// Get element
		var element = e.target;
		
		// Get count
		var count = element.getAttribute('count');
		
		// If this is a promotion citation
		if (element.getAttribute('class') == 'promotion') {
			
			// Set properties
			var promotion_image = 'img_promotion_image_';
			var promotion_rank = 'p_promotion_rank_';
 			
 			// Set the award name
			document.getElementById(promotion_image.concat(count)).setAttribute('src', element.getAttribute('image'));
			document.getElementById(promotion_rank.concat(count)).innerHTML=element.getAttribute('rank');

			// Set modal view id
			var modal = 'view_promotion_citation_';
			
			// Show our modal view
			_var = new ipb.Popup( 'booboo', { type: 'pane',
					  		  initial: $(modal.concat(count)).innerHTML,
					 		  hideAtStart: false,
							  w: '600px'} );
		}
		else if (element.getAttribute('class') == 'demotion') {
			
			// Get element
			var element = e.target;
			
			// Get count
			var count = element.getAttribute('count');
			
			// Set properties
			var demotion_image = 'img_demotion_image_';
			var demotion_rank = 'p_demotion_rank_';
			
			// Set the award name
			document.getElementById(demotion_image.concat(count)).setAttribute('src', element.getAttribute('image'));
			document.getElementById(demotion_rank.concat(count)).innerHTML=element.getAttribute('rank');
			
			// Set modal view id
			var modal = 'view_demotion_citation_';
			
			// Show our modal view
			_var = new ipb.Popup( 'booboo', { type: 'pane',
							  initial: $(modal.concat(count)).innerHTML,
							  hideAtStart: false,
							  w: '600px'} );
		}
		else if (element.getAttribute('class') == 'assignment') {
			
			// Get element
			var element = e.target;
			
			// Get count
			var count = element.getAttribute('count');
			
			// Set properties
			var assignment_unit = 'p_assignment_unit_';
			var assignment_position = 'p_assignment_position_';
			
			// Set the assignment properties
			document.getElementById(assignment_unit.concat(count)).innerHTML=element.getAttribute('unit');
			document.getElementById(assignment_position.concat(count)).innerHTML=element.getAttribute('position');
			
			// Set modal view id
			var modal = 'view_assignment_opord_';
			
			// Show our modal view
			_var = new ipb.Popup( 'booboo', { type: 'pane',
							  initial: $(modal.concat(count)).innerHTML,
							  hideAtStart: false,
							  w: '600px'} );
		}
		
		// Stop the event and return
		Event.stop(e);
		return false;
	});
}

function populateAwardRecordDatatable(data) {
	
	// If the user can edit
	if (ipb.vars['allowEdit']) {
		
		// Add delete column
		var columns = [
			{ "title": "Date", "className": "center", "width": "10%", "data": "date"},
			{ "title": "Entry", "width": "60%", "data": "entry", "orderable": false},
			{ "title": "Citation / OPORD", "className": "citation", "width": "20%", "data": "citation", "orderable": false},
			{ "title": "Delete", "className": "center delete", "width": "10%", "orderable": false}
		];
	}
	else {
		
		// Regular table
		var columns = [
			{ "title": "Date", "width": "10%", "data": "date"},
			{ "title": "Entry", "width": "70%", "data": "entry", "orderable": false},
			{ "title": "Citation / OPORD", "className": "citation", "width": "20%", "data": "citation", "orderable": false}
		];
	}

	// Set up our datatable				
	var table = jQuery('#award_record').dataTable( {
		"data": data,
		"paging": true,
		"ordering": true,
		"order": [[ 0, "desc" ]],
		"info": false,
		"columns": columns,
		"oLanguage": {
			"sZeroRecords": "No Award Record Entries Found"
		},
		"columnDefs": [ {
			"targets": -1,
			"data": null,
			"defaultContent": "<u><a href='#'>Delete</a></u>"
		} ],
		"fnRowCallback": function( nRow, aData, iDisplayIndex, iDisplayIndexFull) {
			jQuery(nRow).attr('id', aData['primary_id_field']);
			return nRow;
		}
	});
	
	// Add a listener to respond to the deletion click
	jQuery('#award_record').on('click','.delete', function (e) {
		
		// Present confirmation
		if (confirm('Are you sure you want to delete this record?')) {
			
			// Create our AJAX request to get the data
			var delete_link = ipb.vars['base_url'] + "app=perscom&module=ajax&section=datatables";
			new Ajax.Request( delete_link,
			{
				method: 'post',
				evalJSON: 'force',
				parameters: {
					'md5check': ipb.vars['secure_hash'],
					'id': jQuery(this).closest('tr').attr('id'),
					'do': 'delete',
					'type': 'Record',
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
			
			// Delete the row
			var row = jQuery(this).closest("tr").get(0);
			var pos = table.fnGetPosition(row); 
			table.fnDeleteRow(pos);
		}
		
		// Return
		return false;
	});
	
	// Add a listener to respond to the citation click
	jQuery('#award_record').on('click','.citation', function (e) {	
		
		// Get element
		var element = e.target;
		
		// Get count
		var count = element.getAttribute('count');
		
		// Set properties
		var award_name = 'p_award_name_';
		var award_image = 'img_award_image_';
		
		// Set the award name
		document.getElementById(award_name.concat(count)).innerHTML=element.getAttribute('award');
		document.getElementById(award_image.concat(count)).setAttribute('src', element.getAttribute('image'));
		
		// Set modal view id
		var modal = 'view_award_citation_';
		
		// Show our modal view
		_var = new ipb.Popup( 'booboo', { type: 'pane',
						  initial: $(modal.concat(count)).innerHTML,
						  hideAtStart: false,
						  w: '600px'} );
		
		// Stop the event and return    
		Event.stop(e);	
   		return false;
	});
}

function populateCombatRecordDatatable(data) {
	
	// If the user can edit
	if (ipb.vars['allowEdit']) {
		
		// Add delete column
		var columns = [
			{ "title": "Date", "width": "10%", "data": "date"},
			{ "title": "Entry", "width": "80%", "data": "entry", "orderable": false},
			{ "title": "Delete", "className": "center delete", "width": "10%", "orderable": false}
		];
	}
	else {
		
		// Regular table
		var columns = [
			{ "title": "Date", "width": "10%", "data": "date"},
			{ "title": "Entry", "width": "90%", "data": "entry", "orderable": false}
		];
	}
	
	// Set up our datatable
	var table = jQuery('#combat_record').dataTable( {
		"data": data,
		"paging": true,
		"ordering": true,
		"order": [[ 0, "desc" ]],
		"info": false,
		"columns": columns,
		"oLanguage": {
			"sZeroRecords": "No Combat Record Entries Found"
		},
		"columnDefs": [ {
			"targets": -1,
			"data": null,
			"defaultContent": "<u><a href='#'>Delete</a></u>"
		} ],
		"fnRowCallback": function( nRow, aData, iDisplayIndex, iDisplayIndexFull) {
			jQuery(nRow).attr('id', aData['primary_id_field']);
			return nRow;
		}
	});
	
	// Add a listener to respond to the deletion click
	jQuery('#combat_record').on('click','.delete', function (e) {
		
		// Present confirmation
		if (confirm('Are you sure you want to delete this record?')) {
			
			// Create our AJAX request to get the data
			var delete_link = ipb.vars['base_url'] + "app=perscom&module=ajax&section=datatables";
			new Ajax.Request( delete_link,
			{
				method: 'post',
				evalJSON: 'force',
				parameters: {
					'md5check': ipb.vars['secure_hash'],
					'id': jQuery(this).closest('tr').attr('id'),
					'do': 'delete',
					'type': 'Record',
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
			
			// Delete the row
			var row = jQuery(this).closest("tr").get(0);
			var pos = table.fnGetPosition(row); 
			table.fnDeleteRow(pos);
		}
		
		// Return
		return false;
	});
}