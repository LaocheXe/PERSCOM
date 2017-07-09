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

	// Create the function to display the award modal
	$('personnel').on( 'click', '.weapon', function(e) {    

	    // Get element target
	    var element = e.target;

	    // Set div id variabe
	    var div = 'view_weapon_';

	    // Show our modal view
	    _var = new ipb.Popup( 'booboo', { type: 'pane',
	                      initial: $(div.concat(element.getAttribute('weapon'))).innerHTML,
	                      hideAtStart: false,
	                      w: '600px' } );
	      
	    // Stop the event      
	    Event.stop(e);  
	    
	    // Return false
	    return false;
	});
});