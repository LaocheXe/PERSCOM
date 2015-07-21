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
		
	// Get the users country code
	$.getJSON("http://freegeoip.net/json/", function(result){

		// If we get a result
		if (result) {

			// Set the input value
			document.getElementById('country').value = result.country_code;
		};
	});

	// Get the users timezone
	var tz = jstz.determine(); // Determines the time zone of the browser client

	// Set the timezone input value
	document.getElementById('timezone').value = tz.name();
});
 
// Validate the enlistment application submission
function validateEnlistmentApplication() {

	// Get the user's first name
    var first_name = document.forms["enlistment_application"]["first_name"].value;

    // If the value is null or empty
    if (first_name==null || first_name=="") {

    	// Prompt the user to fill in the field
        alert("Please fill out your first name.");
        return false;
    }

    // Check for non-english characters
    if (/^[a-zA-Z0-9- ]*$/.test(first_name) == false) {
        
        // Prompt the user to fill in the field
        alert("Your first name contains a special character. Please use only 0-9, a-z or A-Z. English characters only.");
        return false;
    }

    // Get the user's last name
    var last_name = document.forms["enlistment_application"]["last_name"].value;

    // If the value is null or empty
    if (last_name==null || last_name=="") {

    	// Prompt the user to fill in the field
        alert("Please fill out your last name.");
        return false;
    }

    // Check for non-english characters
    if (/^[a-zA-Z0-9- ]*$/.test(last_name) == false) {
        
        // Prompt the user to fill in the field
        alert("Your first name contains a special character. Please use only 0-9, a-z or A-Z. English characters only.");
        return false;
    }

    // Get the user's DOB
    var date_of_birth = document.forms["enlistment_application"]["date_of_birth"].value;

    // If the value is null or empty
    if (date_of_birth==null || date_of_birth=="") {

    	// Prompt the user to fill in the field
        alert("Please fill out your date of birth.");
        return false;
    }

    // Set regex for validating date input
    var regex = /^(0[1-9]|1[012])[- \/.](0[1-9]|[12][0-9]|3[01])[- \/.](19|20)\d\d$/; 

    // If the date is not in the right format
    if (!regex.test(date_of_birth)) {

    	// Prompt the user to fill out the field correctly
        alert("Please enter a valid date of birth. (MM-DD-YYYY)");
		return false;
    }

    // Get the user's age
    var age = document.forms["enlistment_application"]["age"].value;

    // If the value is null or empty
    if (age==null || age=="") {

    	// Prompt the user to fill in the field
        alert("Please fill out your age.");
        return false;
    }

    // Get the user's country
    var country = document.forms["enlistment_application"]["country"].value;

    // If the value is null or empty
    if (country==null || country=="") {

    	// Prompt the user to fill in the field
        alert("Please fill out your country.");
        return false;
    }

    // Make sure the ISO code is two characters, if not
    if (country.length != 2) {

    	// Prompt the user to fill in the field correctly
        alert("The ISO country code must be 2 characters long.");
        return false;
    }

    // Get the user's timezone
    var timezone = document.forms["enlistment_application"]["timezone"].value;

    // If the value is null or empty
    if (timezone==null || timezone=="") {

    	// Prompt the user to fill in the field
        alert("Please fill out your timezone.");
        return false;
    }

    // Get the user's in-game name
    var in_game_name = document.forms["enlistment_application"]["in_game_name"].value;

    // If the value is null or empty
    if (in_game_name==null || in_game_name=="") {

    	// Prompt the user to fill in the field
        alert("Please fill out your current in-game name.");
        return false;
    }

    // Get the user's player ID
    var player_id = document.forms["enlistment_application"]["player_id"].value;

    // If the value is null or empty
    if (player_id==null || player_id=="") {

    	// Prompt the user to fill in the field
        alert("Please fill out your player ID.");
        return false;
    }

    // Get if the user has had nay past clans
    var previous_clans_radio = document.forms["enlistment_application"]["previous_clans_radio"].value;

    // If they have been in a previous unit
    if (previous_clans_radio=="yes") {

    	// Get the list of previous clans
        var previous_clans = document.forms["enlistment_application"]["previous_clans"].value;

        // If the value is null or empty
        if (previous_clans_radio==null || previous_clans_radio=="") {

        	// Prompt the user to fill in the field
            alert("Please fill out your previous units.");
            return false;
        }
    }

    // Get the user's committment time
    var commitment_time = document.forms["enlistment_application"]["commitment_time"].value;

    // If the value is null or empty
    if (commitment_time==null || commitment_time=="") {

    	// Prompt the user to fill in the field
        alert("Please fill out your estimated commitment time.");
        return false;
    }

    // Get the user's two reasons
    var two_reasons = document.forms["enlistment_application"]["two_reasons"].value;

    // If the value is null or empty
    if (two_reasons==null || two_reasons=="") {

    	// Prompt the user to fill in the field
        alert("Please give two reasons why you would like to join this unit.");
        return false;
    }

    // Get the users info on how they heard about us
    var hear_about_us = document.forms["enlistment_application"]["hear_about_us"].value;

    // If the value is null or empty
    if (hear_about_us==null || hear_about_us=="") {

    	// Prompt the user to fill in the field
        alert("Please tell us how you heard about us!");
        return false;
    }
}