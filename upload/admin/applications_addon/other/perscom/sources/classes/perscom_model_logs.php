<?php 
/*
+--------------------------------------------------------------------------
|   PERSCOM v1.0
|   =============================================
|   by 3rd Infantry Division (Erickson)
|   Copyright 2014-2015 Third Infantry Division
|   http://www.3rdinf.us
+--------------------------------------------------------------------------
*/

// Check to make sure the user is not accessing the files directly
if ( ! defined( 'IN_IPB' ) )
{
	print "<h1>Incorrect access</h1>You cannot access this file directly. If you have recently upgraded, make sure you upgraded all the relevant files.";
	exit();
}

class perscom_model_logs extends perscom_model_perscom {	

	public function loadLogs() {

		// Query the DB to look for all TPR's
		$this->DB->build( array( 
			'select' => 'primary_id_field, date, members_display_name as soldier, type, description, administrator_members_display_name as completed_by, status', 
			'from' 	=> $this->settings['perscom_database_requests'], 
			'order' => 'primary_id_field DESC', 
			'where' => 'status!="Pending"' ) );

		// Execute the query
		$result = $this->DB->execute();

		// Define array
		$rows = array();

		// Loop over the results and add them to the soldier array
		while( $r = $this->DB->fetch( $result ) )
		{
			// Format the date
			$r['date'] = strftime($this->settings['clock_short2'], $r['date']);

			// Add the members on end of the array
			array_push($rows, $r);
		}

		// Return the result
		return $rows;
	}
}