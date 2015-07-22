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
		
	// Observe the edit combat unit href
	$('edit_cunit').observe( 'click', function(e) {

		// Get the currently selected combat unit option value
		var select = document.getElementById("combat_unit");
		var option = select.options[select.selectedIndex].value;

		// Check to make sure there is a value to edit
		if (option && option != '') {

			// Set the form action
			document.getElementById('combat_unit_form').setAttribute('action', ipb.vars['base_url'] + 'app=perscom&module=basecamp&section=settings&action=edit&type=combat_unit&id=' + option);

			// Get the combat unit information
			getCombatUnitInformation();
		};
	});

	// Observe the edit admin unit href
	$('edit_aunit').observe( 'click', function(e) {

		// Get the currently selected combat unit option value
		var select = document.getElementById("admin_unit");
		var option = select.options[select.selectedIndex].value;

		// Check to make sure there is a value to edit
		if (option && option != '') {

			// Set the form action
			document.getElementById('admin_unit_form').setAttribute('action', ipb.vars['base_url'] + 'app=perscom&module=basecamp&section=settings&action=edit&type=admin_unit&id=' + option);

			// Get the admin unit information
			getAdminUnitInformation();
		};
	});


	// Observe the edit weapon href
	$('edit_weapon').observe( 'click', function(e) {

		// Get the currently selected combat unit option value
		var select = document.getElementById("weapon");
		var option = select.options[select.selectedIndex].value;

		// Check to make sure there is a value to edit
		if (option && option != '') {

			// Set the form action
			document.getElementById('weapon_form').setAttribute('action', ipb.vars['base_url'] + 'app=perscom&module=basecamp&section=settings&action=edit&type=weapon&id=' + option);

			// Get the weapon information
			getWeaponInformation();
		};
	});


	// Observe the add combat unit href
	$('add_cunit').observe( 'click', function(e) {

		// Set the form action
		document.getElementById('combat_unit_form').setAttribute('action', ipb.vars['base_url'] + 'app=perscom&module=basecamp&section=settings&action=add&type=combat_unit');

		// Clear all the  previous element values
		document.getElementById('cunit_name').setAttribute('value', '');
		document.getElementById('position').setAttribute('value', '');
		document.getElementById('nickname').setAttribute('value', '');
		document.getElementById('order').setAttribute('value', '');
		document.getElementById('cunit_usergroup').setAttribute('value', '');
		
		// Show our modal view
		_var = new ipb.Popup( 'combat_unit', { type: 'pane',
						  	initial: $('view_cunit').innerHTML,
						  	hideAtStart: false,
						  	w: '600px' } );
			    
		// Stop the event
		Event.stop(e);
				
		// Return false
		return false;
	});

	// Observe the add admin unit href
	$('add_aunit').observe( 'click', function(e) {

		// Set the form action
		document.getElementById('admin_unit_form').setAttribute('action', ipb.vars['base_url'] + 'app=perscom&module=basecamp&section=settings&action=add&type=admin_unit');

		// Clear the element values
		document.getElementById('aunit_name').setAttribute('value', '');
		document.getElementById('mos').setAttribute('value', '');
		document.getElementById('aunit_image').setAttribute('value', '');
		document.getElementById('responsibilities').innerHTML = '';
		document.getElementById('prerequisites').innerHTML = '';
		document.getElementById('aunit_usergroup').setAttribute('value', '');
		
		// Show our modal view
		_var = new ipb.Popup( 'admin_unit', { type: 'pane',
						  	initial: $('view_aunit').innerHTML,
						  	hideAtStart: false,
						  	w: '600px' } );
			    
		// Stop the event
		Event.stop(e);
				
		// Return false
		return false;
	});

	// Observe the add weapon href
	$('add_weapon').observe( 'click', function(e) {

		// Set the form action
		document.getElementById('weapon_form').setAttribute('action', ipb.vars['base_url'] + 'app=perscom&module=basecamp&section=settings&action=add&type=weapon');

		// Clear the element values
		document.getElementById('make').setAttribute('value', '');
		document.getElementById('caliber').setAttribute('value', '');
		document.getElementById('weight').setAttribute('value', '');
		document.getElementById('magazine').setAttribute('value', '');
		document.getElementById('date').setAttribute('value', '');
		document.getElementById('fire_type').setAttribute('value', '');
		document.getElementById('fire_rate').setAttribute('value', '');
		document.getElementById('firing_range').setAttribute('value', '');
		document.getElementById('weapon_image').setAttribute('value', '');
		document.getElementById('length').setAttribute('value', '');
		document.getElementById('weapon_action').setAttribute('value', '');
		document.getElementById('velocity').setAttribute('value', '');
		document.getElementById('sights').setAttribute('value', '');
		
		// Show our modal view
		_var = new ipb.Popup( 'weapon', { type: 'pane',
						  	initial: $('view_weapon').innerHTML,
						  	hideAtStart: false,
						  	w: '700px',
						 	h: '800' } );
			    
		// Stop the event
		Event.stop(e);
				
		// Return false
		return false;
	});
});

function getCombatUnitInformation() {

	// Get the currently selected combat unit option value
	var select = document.getElementById("combat_unit");
	var option = select.options[select.selectedIndex].value;

	// Create our AJAX request to get the data
	var combat_unit_link = ipb.vars['base_url'] + "app=perscom&module=ajax&section=datatables&record=combat_unit&id=" + option;
	new Ajax.Request( combat_unit_link,
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

				// Set the element values
				document.getElementById('cunit_name').setAttribute('value', t.responseJSON['name']);
				document.getElementById('position').setAttribute('value', t.responseJSON['unit_position']);
				document.getElementById('nickname').setAttribute('value', t.responseJSON['nickname']);
				document.getElementById('order').setAttribute('value', t.responseJSON['order']);

				// Show our modal view
				_var = new ipb.Popup( 'combat_unit', { type: 'pane',
								  	initial: $('view_cunit').innerHTML,
								  	hideAtStart: false,
								  	w: '600px' } );

				// Loop through our cunit usergroup selects
				jQuery("[dropdown='cunit_usergroup']").each(function(key, value) {

					// Set the selected value to our saved usergroup
					value.value = t.responseJSON['forum_usergroup'];
				});
				
				// Stop the event
				Event.stop(e);
				
				// Return false
				return false;
			}
		}
	});
}

function getAdminUnitInformation() {

	// Get the currently selected admin unit option value
	var select = document.getElementById("admin_unit");
	var option = select.options[select.selectedIndex].value;

	// Create our AJAX request to get the data
	var admin_unit_link = ipb.vars['base_url'] + "app=perscom&module=ajax&section=datatables&record=admin_unit&id=" + option;
	new Ajax.Request( admin_unit_link,
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

				// Set the element values
				document.getElementById('aunit_name').setAttribute('value', t.responseJSON['name']);
				document.getElementById('mos').setAttribute('value', t.responseJSON['mos']);
				document.getElementById('aunit_image').setAttribute('value', t.responseJSON['image']);
				document.getElementById('responsibilities').innerHTML = t.responseJSON['responsibilities'];
				document.getElementById('prerequisites').innerHTML = t.responseJSON['prerequisites'];
				document.getElementById('aunit_usergroup').setAttribute('value', t.responseJSON['forum_usergroup']);

				// Show our modal view
				_var = new ipb.Popup( 'admin_unit', { type: 'pane',
								  	initial: $('view_aunit').innerHTML,
								  	hideAtStart: false,
								  	w: '600px' } );

				// Loop through our aunit usergroup selects
				jQuery("[dropdown='aunit_usergroup']").each(function(key, value) {

					// Set the selected value to our saved usergroup
					value.value = t.responseJSON['forum_usergroup'];
				});
				
				// Stop the event
				Event.stop(e);
				
				// Return false
				return false;
			}
		}
	});
}

function getWeaponInformation() {

	// Get the currently selected weapon option value
	var select = document.getElementById("weapon");
	var option = select.options[select.selectedIndex].value;

	// Create our AJAX request to get the data
	var weapon_link = ipb.vars['base_url'] + "app=perscom&module=ajax&section=datatables&record=weapon&id=" + option;
	new Ajax.Request( weapon_link,
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

				// Set the element values
				document.getElementById('make').setAttribute('value', t.responseJSON['make_and_model']);
				document.getElementById('caliber').setAttribute('value', t.responseJSON['caliber']);
				document.getElementById('weight').setAttribute('value', t.responseJSON['weight']);
				document.getElementById('magazine').setAttribute('value', t.responseJSON['magazine']);
				document.getElementById('date').setAttribute('value', t.responseJSON['introduction_date']);
				document.getElementById('fire_type').setAttribute('value', t.responseJSON['fire_type']);
				document.getElementById('fire_rate').setAttribute('value', t.responseJSON['rate_of_fire']);
				document.getElementById('firing_range').setAttribute('value', t.responseJSON['effective_firing_range']);
				document.getElementById('weapon_image').setAttribute('value', t.responseJSON['image']);
				document.getElementById('length').setAttribute('value', t.responseJSON['barrel_length']);
				document.getElementById('weapon_action').setAttribute('value', t.responseJSON['action']);
				document.getElementById('velocity').setAttribute('value', t.responseJSON['muzzle_velocity']);
				document.getElementById('sights').setAttribute('value', t.responseJSON['sights']);

				// Show our modal view
				_var = new ipb.Popup( 'weapon', { type: 'pane',
								  	initial: $('view_weapon').innerHTML,
								  	hideAtStart: false,
								  	w: '700px',
								  	h: '800' } );
				
				// Stop the event
				Event.stop(e);
				
				// Return false
				return false;
			}
		}
	});
}

function confirmDeleteCombatUnit() {

	// Get the currently selected combat unit option value
	var select = document.getElementById("combat_unit");
	var option = select.options[select.selectedIndex].value;

	// Check to make sure there is a value to delete
	if (option && option != '') {

		// Confirm delete
		if (confirm('Are you sure you want to delete this combat unit?')) {

			// Set the delete combat unit url
			document.getElementById('delete_cunit').setAttribute('href', ipb.vars['base_url'] + 'app=perscom&module=basecamp&section=settings&do=delete&type=combat_unit&id=' + option);

			// Return
			return true;
		};
	};

	// Return
	return false;
}

function confirmDeleteAdminUnit() {

	// Get the currently selected combat unit option value
	var select = document.getElementById("admin_unit");
	var option = select.options[select.selectedIndex].value;

	// Check to make sure there is a value to delete
	if (option && option != '') {

		// Confirm delete
		if (confirm('Are you sure you want to delete this admin unit?')) {

			// Set the delete combat unit url
			document.getElementById('delete_aunit').setAttribute('href', ipb.vars['base_url'] + 'app=perscom&module=basecamp&section=settings&do=delete&type=admin_unit&id=' + option);

			// Return
			return true;	
		};
	};

	// Return
	return false;
}

function confirmDeleteWeapon() {

	// Get the currently selected combat unit option value
	var select = document.getElementById("weapon");
	var option = select.options[select.selectedIndex].value;

	// Check to make sure there is a value to delete
	if (option && option != '') {

		// Confirm delete
		if (confirm('Are you sure you want to delete this weapon?')) {

			// Set the delete combat unit url
			document.getElementById('delete_weapon').setAttribute('href', ipb.vars['base_url'] + 'app=perscom&module=basecamp&section=settings&do=delete&type=weapon&id=' + option);

			// Return
			return true;
		};
	};

	// Return
	return false;
}