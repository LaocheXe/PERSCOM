/*
+--------------------------------------------------------------------------
|   PERSCOM v1.0
|   =============================================
|   by 3rd Infantry Division (Erickson)
|   Copyright 2014-2015 Third Infantry Division
|   http://www.3rdinf.us
+--------------------------------------------------------------------------
*/

// Include jQuery 1.9.1
!window.jQuery && document.write('<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"><\/script>')

// Global variables
var dateRegex = /^(0[1-9]|1[012])[- \/.](0[1-9]|[12][0-9]|3[01])[- \/.](19|20)\d\d$/; 

function confirmSubmission() {

	// Display confirmation message
	return confirm('Have you checked to make sure all the information is correct?');
}

function validateAddAwardEntry() {

    // Get soldier
    var soldier = document.forms["add_award"]["soldier"].value;

    // Make sure it is set and not null
    if (soldier==null || soldier=="") {

        // If it is, return alert and stop form submission
        alert("Please add some soldiers and select a valid one before submitting this form.");
        return false;
    }

    // Get award
    var award = document.forms["add_award"]["award"].value;

    // Make sure it is set and not null
    if (award==null || award=="") {

        // If it is, return alert and stop form submission
        alert("Please add some awards and select a valid one before submitting this form.");
        return false;
    }

	// Get award date
    var award_date = document.forms["add_award"]["date"].value;

    // Make sure it is set and not null
    if (award_date==null || award_date=="") {

    	// If it is, return alert and stop form submission
        alert("Please enter in a date for the award.");
        return false;
    }
    
    // Make sure the date has been entered in properly
    if (!dateRegex.test(award_date)) {

    	// If not, show an alert and prevent form submission
        alert("Please enter a valid award date. (MM-DD-YYYY)");
		return false;
    }

    // If the user cancel's submission
    if (confirmSubmission() == false) {

    	// Stop the form from being submitted
    	return false;
    }
}

function validateCombatRecordEntry() {

    // Get soldier
    var soldier = document.forms["add_combat_record_entry"]["soldier"].value;

    // Make sure it is set and not null
    if (soldier==null || soldier=="") {

        // If it is, return alert and stop form submission
        alert("Please add some soldiers and select a valid one before submitting this form.");
        return false;
    }

    // Get the combat entry
    var combat_entry = document.forms["add_combat_record_entry"]["combat_entry"].value;

    // Make sure it is set and not null
    if (combat_entry==null || combat_entry=="") {

        // If it is, return alert and stop form submission
        alert("Please enter in a combat record entry.");
        return false;
    }

    // Get the combat entry date
    var combat_date = document.forms["add_combat_record_entry"]["date"].value;

    // Make sure it is set and not null
    if (combat_date==null || combat_date=="") {

        // If it is, return alert and stop form submission
        alert("Please enter in a date for the record.");
        return false;
    }

    // Make sure the date has been entered in properly
    if (!dateRegex.test(combat_date)) {

        // If not, show an alert and prevent form submission
        alert("Please enter a valid combat record date. (MM-DD-YYYY)");
        return false;
    }

    // If the user cancel's submission
    if (confirmSubmission() == false) {

        // Stop the form from being submitted
        return false;
    }
}

function validateServiceRecordEntry() {

    // Get soldier
    var soldier = document.forms["add_service_record_entry"]["soldier"].value;

    // Make sure it is set and not null
    if (soldier==null || soldier=="") {

        // If it is, return alert and stop form submission
        alert("Please add some soldiers and select a valid one before submitting this form.");
        return false;
    }

    // Get the record entry
    var record_entry = document.forms["add_service_record_entry"]["service_record_entry"].value;

    // Make sure it is set and not null
    if (record_entry==null || record_entry=="") {

        // If it is, return alert and stop form submission
        alert("Please enter in a service record entry.");
        return false;
    }

    // Get the record date
    var misc_date = document.forms["add_service_record_entry"]["date"].value;

    // Make sure it is set and not null
    if (misc_date==null || misc_date=="") {

        // If it is, return alert and stop form submission
        alert("Please enter in a date for the record.");
        return false;
    }

    // Make sure the date has been entered in properly
    if (!dateRegex.test(misc_date)) {

        // If not, show an alert and prevent form submission
        alert("Please enter a valid record date. (MM-DD-YYYY)");
        return false;
    }

    // If the user cancel's submission
    if (confirmSubmission() == false) {

        // Stop the form from being submitted
        return false;
    }
}

function validateAssignmentEntry() {

    // Get soldier
    var soldier = document.forms["add_assignment"]["soldier"].value;

    // Make sure it is set and not null
    if (soldier==null || soldier=="") {

        // If it is, return alert and stop form submission
        alert("Please add some soldiers and select a valid one before submitting this form.");
        return false;
    }

    // Get combat unit
    var unit = document.forms["add_assignment"]["unit"].value;

    // Make sure it is set and not null
    if (unit==null || unit=="") {

        // If it is, return alert and stop form submission
        alert("Please add some combat units and select a valid one before submitting this form.");
        return false;
    }

    // Get the soldier's position and unit
    var position = document.forms["add_assignment"]["position"].value;

    // Make sure it is set and not null
    if (position==null || position=="") {

        // If it is, return alert and stop form submission
        alert("Please enter in a position for the soldier.");
        return false;
    }

    // Ge the assignment date
    var assignment_date = document.forms["add_assignment"]["date"].value;

    // Make sure it is set and not null
    if (assignment_date==null || assignment_date=="") {

        // If it is, return alert and stop form submission
        alert("Please enter in a date for the assignment.");
        return false;
    }

    // Make sure the date has been entered in properly
    if (!dateRegex.test(assignment_date)) {

        // If not, show an alert and prevent form submission
        alert("Please enter a valid assignment date. (MM-DD-YYYY)");
        return false;
    }

    // If the user cancel's submission
    if (confirmSubmission() == false) {

        // Stop the form from being submitted
        return false;
    }
}

function vaidatePromotion() {

    // Get soldier
    var soldier = document.forms["file_promotion"]["soldier"].value;

    // Make sure it is set and not null
    if (soldier==null || soldier=="") {

        // If it is, return alert and stop form submission
        alert("Please add some soldiers and select a valid one before submitting this form.");
        return false;
    }

    // Get rank
    var rank = document.forms["file_promotion"]["rank"].value;

    // Make sure it is set and not null
    if (rank==null || rank=="") {

        // If it is, return alert and stop form submission
        alert("Please add some ranks and select a valid one before submitting this form.");
        return false;
    }

    // Get the promotion date
    var promotion_date = document.forms["file_promotion"]["date"].value;

    // Make sure it is set and not null
    if (promotion_date==null || promotion_date=="") {

        // If it is, return alert and stop form submission
        alert("Please enter in a date for the promotion.");
        return false;
    }

    // Make sure the date has been entered in properly
    if (!dateRegex.test(promotion_date)) {

        // If not, show an alert and prevent form submission
        alert("Please enter a valid promotion date. (MM-DD-YYYY)");
        return false;
    }

    // Get the new name
    var new_name = document.forms["file_promotion"]["new_name"].value;

    // Make sure it is set and not null
    if (new_name==null || new_name=="") {

        // If it is, return alert and stop form submission
        alert("Please enter a new display name for the soldier.");
        return false;
    }

    // If the user cancel's submission
    if (confirmSubmission() == false) {

        // Stop the form from being submitted
        return false;
    }
}

function vaidateDemotion() {

    // Get soldier
    var soldier = document.forms["file_demotion"]["soldier"].value;

    // Make sure it is set and not null
    if (soldier==null || soldier=="") {

        // If it is, return alert and stop form submission
        alert("Please add some soldiers and select a valid one before submitting this form.");
        return false;
    }

    // Get rank
    var rank = document.forms["file_demotion"]["rank"].value;

    // Make sure it is set and not null
    if (rank==null || rank=="") {

        // If it is, return alert and stop form submission
        alert("Please add some ranks and select a valid one before submitting this form.");
        return false;
    }


    // Get the demotion date
    var demotion_date = document.forms["file_demotion"]["date"].value;

    // Make sure it is set and not null
    if (demotion_date==null || demotion_date=="") {

        // If it is, return alert and stop form submission
        alert("Please enter in a date for the demotion.");
        return false;
    }

    // Make sure the date has been entered in properly
    if (!dateRegex.test(demotion_date)) {

        // If not, show an alert and prevent form submission
        alert("Please enter a valid demotion date. (MM-DD-YYYY)");
        return false;
    }

    // Get the new name
    var new_name = document.forms["file_demotion"]["new_name"].value;

    // Make sure it is set and not null
    if (new_name==null || new_name=="") {

        // If it is, return alert and stop form submission
        alert("Please enter a new display name for the soldier.");
        return false;
    }

    // If the user cancel's submission
    if (confirmSubmission() == false) {

        // Stop the form from being submitted
        return false;
    }
}

function validateDischarge() {

    // Get soldier
    var soldier = document.forms["file_discharge"]["soldier"].value;

    // Make sure it is set and not null
    if (soldier==null || soldier=="") {

        // If it is, return alert and stop form submission
        alert("Please add some soldiers and select a valid one before submitting this form.");
        return false;
    }

    // Get the discharge date
    var discharge_date = document.forms["file_discharge"]["date"].value;

    // Make sure it is set and not null
    if (discharge_date==null || discharge_date=="") {

        // If it is, return alert and stop form submission
        alert("Please enter in a date for the discharge.");
        return false;
    }

    // Make sure the date has been entered in properly
    if (!dateRegex.test(discharge_date)) {

        // If not, show an alert and prevent form submission
        alert("Please enter a valid discharge date. (MM-DD-YYYY)");
        return false;
    }

    // Get the new name
    var new_name = document.forms["file_discharge"]["new_name"].value;

    // Make sure it is set and not null
    if (new_name==null || new_name=="") {

        // If it is, return alert and stop form submission
        alert("Please enter a new display name for the civilian.");
        return false;
    }

    // If the user cancel's submission
    if (confirmSubmission() == false) {

        // Stop the form from being submitted
        return false;
    }
}

function validateLOARequest() {

    // Get the start date
    var start_date = document.forms["request_loa"]["start_date"].value;

    // Make sure it is set and not null
    if (start_date==null || start_date=="") {

        // If it is, return alert and stop form submission
        alert("Please fill in the expected start date.");
        return false;
    }

    // Make sure the date has been entered in properly
    if (!dateRegex.test(start_date)) {

        // If not, show an alert and prevent form submission
        alert("Please enter a valid start date. (MM-DD-YYYY)");
        return false;
    }

    // Get the end date
    var end_date = document.forms["request_loa"]["end_date"].value;

    // Make sure it is set and not null
    if (end_date==null || end_date=="") {

        // If it is, return alert and stop form submission
        alert("Please fill in the expected return date.");
        return false;
    }

    // Make sure the date has been entered in properly
    if (!dateRegex.test(end_date)) {

        // If not, show an alert and prevent form submission
        alert("Please enter a valid return date. (MM-DD-YYYY)");
        return false;
    }

    // Convert date to js dates
    var numbersStart = start_date.match(/\d+/g);
    var numbersEnd = end_date.match(/\d+/g);
    var startDate = new Date(numbersStart[2], numbersStart[0]-1, numbersStart[1]);
    var endDate = new Date(numbersEnd[2], numbersEnd[0]-1, numbersEnd[1]);
    var now = new Date();

    // Make sure the start date is not in the past
    if (startDate < now.setDate(now.getDate() - 1)) {

        // If it is, return alert and stop form submission
        alert("Error: The effective start date cannot be in the past.");
        return false;
    }

    // Make sure the end date is in the future
    if (endDate < now) {

        // If it is, return alert and stop form submission
        alert("Error: The expected return date needs to be in the future.");
        return false;
    }

    // Make sure the end is after the start date
    if (startDate >= endDate) {

        // If it is, return alert and stop form submission
        alert("Error: The expected return date does not occur after the start date.");
        return false;
    }

    // Get the explanation
    var explanation = document.forms["request_loa"]["explanation"].value;

    // Make sure it is set and not null
    if (explanation==null || explanation=="") {

        // If it is, return alert and stop form submission
        alert("Please add an explanation with your form.");
        return false;
    }

    // If the user cancel's submission
    if (confirmSubmission() == false) {

        // Stop the form from being submitted
        return false;
    }
}

function validateLOAExtension() {

    // Get the new return date
    var new_return_date = document.forms["extend_loa"]["new_return_date"].value;

    // Make sure it is set and not null
    if (new_return_date==null || new_return_date=="") {

        // If it is, return alert and stop form submission
        alert("Please fill in the new expected return date.");
        return false;
    }

    // Make sure the date has been entered in properly
    if (!dateRegex.test(new_return_date)) {
        
        // If not, show an alert and prevent form submission
        alert("Please enter a valid return date. (MM-DD-YYYY)");
        return false;
    }

    // Convert date to js dates
    var numbersEnd = new_return_date.match(/\d+/g);
    var endDate = new Date(numbersEnd[2], numbersEnd[0]-1, numbersEnd[1]);
    var now = new Date();

    // Make sure the end date is in the future
    if (endDate < now) {

        // If it is, return alert and stop form submission
        alert("Error: The expected return date needs to be in the future.");
        return false;
    }

    // If the user cancel's submission
    if (confirmSubmission() == false) {

        // Stop the form from being submitted
        return false;
    }
}

function validateTPRRequest() {

    // Get the explanation
    var explanation = document.forms["request_tpr"]["explanation"].value;

    // Make sure it is set and not null
    if (explanation==null || explanation=="") {

        // If it is, return alert and stop form submission
        alert("Please provide an explanation for your TPR.");   
        return false;
    }

    // Get the event they are missing
    var event_missing = document.forms["request_tpr"]["event"].value;

    // Make sure it is set and not null
    if (event_missing==null || event_missing=="") {

        // If it is, return alert and stop form submission
        alert("Please provide the event you are planning on missing.");
        return false;
    }

    // If the user cancel's submission
    if (confirmSubmission() == false) {

        // Stop the form from being submitted
        return false;
    }
}

function validateDischargeRequest() {

    // Get the explanation
    var explanation = document.forms["request_discharge"]["explanation"].value;

    // Make sure it is set and not null
    if (explanation==null || explanation=="") {

        // If it is, return alert and stop form submission
        alert("Please add an explanation with your form.");
        return false;
    }

    // If the user cancel's submission
    if (confirmSubmission() == false) {

        // Stop the form from being submitted
        return false;
    }
}

function validateProcessApplicant() {

    // Get rank
    var rank = document.forms["process_applicant"]["rank"].value;

    // Make sure it is set and not null
    if (rank==null || rank=="") {

        // If it is, return alert and stop form submission
        alert("Please add some ranks and select a valid one before submitting this form.");
        return false;
    }

    // Get combat unit
    var unit = document.forms["process_applicant"]["unit"].value;

    // Make sure it is set and not null
    if (unit==null || unit=="") {

        // If it is, return alert and stop form submission
        alert("Please add some combat units and select a valid one before submitting this form.");
        return false;
    }

    // Get the position
    var position = document.forms["process_applicant"]["position"].value;

    // Make sure it is set and not null
    if (position==null || position=="") {

        // If it is, return alert and stop form submission
        alert("Please provide a combat position.");
        return false;
    }

    // Get recruiting medium
    var recruiting_medium = document.forms["process_applicant"]["recruiting_medium"].value;

    // Make sure it is set and not null
    if (recruiting_medium==null || recruiting_medium=="") {

        // If it is, return alert and stop form submission
        alert("Please add some recruiting mediums and select a valid one before submitting this form.");
        return false;
    }

    // Get the MOS
    var mos = document.forms["process_applicant"]["mos"].value;

    // Make sure it is set and not null
    if (mos==null || mos=="") {

        // If it is, return alert and stop form submission
        alert("Please provide a combat MOS.");
        return false;
    }

    // Get weapon
    var weapon = document.forms["process_applicant"]["weapon"].value;

    // Make sure it is set and not null
    if (weapon==null || weapon=="") {

        // If it is, return alert and stop form submission
        alert("Please add some weapons and select a valid one before submitting this form.");
        return false;
    }

    // If the user cancel's submission
    if (confirmSubmission() == false) {

        // Stop the form from being submitted
        return false;
    }
}

function validateAddSoldier() {

    // Get the forum name
    var forum_name = document.forms["add_soldier"]["forum_name"].value;

    // Make sure it is set and not null
    if (forum_name==null || forum_name=="") {

        // If it is, return alert and stop form submission
        alert("Please fill out the forum name.");
        return false;
    }

    // Get the first name
    var first_name = document.forms["add_soldier"]["first_name"].value;

    // Make sure it is set and not null
    if (first_name==null || first_name=="") {

        // If it is, return alert and stop form submission
        alert("Please fill out the first name.");
        return false;
    }

    // Get the last name
    var last_name = document.forms["add_soldier"]["last_name"].value;

    // Make sure it is set and not null
    if (last_name==null || last_name=="") {

        // If it is, return alert and stop form submission
        alert("Please fill out the last name.");
        return false;
    }

    // Get the country
    var country = document.forms["add_soldier"]["country"].value;

    // Make sure it is set and not null
    if (country==null || country=="") {

        // If it is, return alert and stop form submission
        alert("Please fill out the country.");
        return false;
    }

    // Make sure the country entry is 2 characters long
    if (country.length != 2) {

        // If not, alert and stop form submission
        alert("The ISO country code must be 2 characters long.");
        return false;
    }

    // Get the time zone
    var timezone = document.forms["add_soldier"]["timezone"].value;

    // Make sure it is set and not null
    if (timezone==null || timezone=="") {

        // If it is, return alert and stop form submission
        alert("Please fill out the timezone.");
        return false;
    }

    // If we are bypassing
    if (document.getElementById('bypass').checked) {

        // Get rank
        var rank = document.forms["add_soldier"]["rank"].value;

        // Make sure it is set and not null
        if (rank==null || rank=="") {

            // If it is, return alert and stop form submission
            alert("Please add some ranks and select a valid one before submitting this form.");
            return false;
        }

        // Get combat unit
        var unit = document.forms["add_soldier"]["unit"].value;

        // Make sure it is set and not null
        if (unit==null || unit=="") {

            // If it is, return alert and stop form submission
            alert("Please add some combat units and select a valid one before submitting this form.");
            return false;
        }

        // Get the position
        var position = document.forms["add_soldier"]["position"].value;

        // Make sure it is set and not null
        if (position==null || position=="") {

            // If it is, return alert and stop form submission
            alert("Please provide a position for the soldier.");
            return false;
        }

        // Get recruiting medium
        var recruiting_medium = document.forms["add_soldier"]["recruiting_medium"].value;

        // Make sure it is set and not null
        if (recruiting_medium==null || recruiting_medium=="") {

            // If it is, return alert and stop form submission
            alert("Please add some recruiting mediums and select a valid one before submitting this form.");
            return false;
        }

        // Get the MOS
        var mos = document.forms["add_soldier"]["mos"].value;

        // Make sure it is set and not null
        if (mos==null || mos=="") {

            // If it is, return alert and stop form submission
            alert("Please provide a MOS for the soldier.");
            return false;
        }

        // Get weapon
        var weapon = document.forms["add_soldier"]["weapon"].value;

        // Make sure it is set and not null
        if (weapon==null || weapon=="") {

            // If it is, return alert and stop form submission
            alert("Please add some weapons and select a valid one before submitting this form.");
            return false;
        }

        // Get the enlistment date
        var enlistment = document.forms["add_soldier"]["enlistment_date"].value;

        // Make sure it is set and not null
        if (enlistment==null || enlistment=="") {

            // If it is, return alert and stop form submission
            alert("Please provide an enlistment date for the soldier.");
            return false;
        }

        // Make sure the date has been entered in properly
        if (!dateRegex.test(enlistment)) {

            // If not, show an alert and prevent form submission
            alert("Please enter a valid enlistment date. (MM-DD-YYYY)");
            return false;
        }

        // Get the induction date
        var induction = document.forms["add_soldier"]["induction_date"].value;

        // Make sure it is set and not null
        if (induction==null || induction=="") {

            // If it is, return alert and stop form submission
            alert("Please provide an induction date for the soldier.");
            return false;
        }

        // Make sure the date has been entered in properly
        if (!dateRegex.test(induction)) {

            // If not, show an alert and prevent form submission
            alert("Please enter a valid induction date. (MM-DD-YYYY)");
            return false;
        }

        // Get the promotion date           
        var promotion = document.forms["add_soldier"]["promotion_date"].value;

        // Make sure it is set and not null
        if (promotion==null || promotion=="") {

            // If it is, return alert and stop form submission
            alert("Please provide the soldier's last known promotion date.");
            return false;
        }

        // Make sure the date has been entered in properly
        if (!dateRegex.test(promotion)) {

            // If not, show an alert and prevent form submission
            alert("Please enter a valid promotion date. (MM-DD-YYYY)");
            return false;
        }       
    }

    // If the user cancel's submission
    if (confirmSubmission() == false) {

        // Stop the form from being submitted
        return false;
    }
}

function validateAddRank() {

    // Get title
    var title = document.forms["add_rank"]["title"].value;

    // Make sure it is set and not null
    if (title==null || title=="") {

        // If it is, return alert and stop form submission
        alert("Please enter in a name for the rank.");
        return false;
    }

    // Get pay grade
    var pay_grade = document.forms["add_rank"]["pay_grade"].value;

    // Make sure it is set and not null
    if (pay_grade==null || pay_grade=="") {

        // If it is, return alert and stop form submission
        alert("Please enter in a pay grade for the rank.");
        return false;
    }

    // Get abbreviation
    var abbreviation = document.forms["add_rank"]["abbreviation"].value;

    // Make sure it is set and not null
    if (abbreviation==null || abbreviation=="") {

        // If it is, return alert and stop form submission
        alert("Please enter in a abbreviation for the rank.");
        return false;
    }

    // Get description
    var description = document.forms["add_rank"]["description"].value;

    // Make sure it is set and not null
    if (description==null || description=="") {

        // If it is, return alert and stop form submission
        alert("Please enter in a description for the rank.");
        return false;
    }

    // Get prerequisites
    var prerequisites = document.forms["add_rank"]["prerequisites"].value;

    // Make sure it is set and not null
    if (prerequisites==null || prerequisites=="") {

        // If it is, return alert and stop form submission
        alert("Please enter in prerequisites for the rank.");
        return false;
    }

    // Get order
    var order = document.forms["add_rank"]["order"].value;

    // Make sure it is set and not null
    if (order==null || order=="") {

        // If it is, return alert and stop form submission
        alert("Please enter in a display order for the rank.");
        return false;
    }

    // Make sure the order is a number
    if (isNaN(order)) {

        // If it is string, stop form submission
        alert("Please enter in an integer for the display order.");
        return false;
    };
    
    // If the user cancel's submission
    if (confirmSubmission() == false) {

        // Stop the form from being submitted
        return false;
    }
}

function validateAddAward() {

    // Get title
    var title = document.forms["add_award"]["title"].value;

    // Make sure it is set and not null
    if (title==null || title=="") {

        // If it is, return alert and stop form submission
        alert("Please enter in a name for the award.");
        return false;
    }

    // Get award image
    var award_image = document.forms["add_award"]["award_image"].value;

    // Make sure it is set and not null
    if (award_image==null || award_image=="") {

        // If it is, return alert and stop form submission
        alert("Please enter in an image for the award.");
        return false;
    }

    // Get history
    var history = document.forms["add_award"]["history"].value;

    // Make sure it is set and not null
    if (history==null || history=="") {

        // If it is, return alert and stop form submission
        alert("Please enter in some history about the award.");
        return false;
    }

    // Get prerequisites
    var prerequisites = document.forms["add_award"]["prerequisites"].value;

    // Make sure it is set and not null
    if (prerequisites==null || prerequisites=="") {

        // If it is, return alert and stop form submission
        alert("Please enter in prerequisites for the award.");
        return false;
    }

    // Get order
    var order = document.forms["add_award"]["order"].value;

    // Make sure it is set and not null
    if (order==null || order=="") {

        // If it is, return alert and stop form submission
        alert("Please enter in a display order for the award.");
        return false;
    }

    // Make sure the order is a number
    if (isNaN(order)) {

        // If it is string, stop form submission
        alert("Please enter in an integer for the display order.");
        return false;
    };
    
    // If the user cancel's submission
    if (confirmSubmission() == false) {

        // Stop the form from being submitted
        return false;
    }
}