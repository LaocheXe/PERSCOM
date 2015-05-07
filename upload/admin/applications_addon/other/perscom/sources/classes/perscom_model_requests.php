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

class perscom_model_requests extends perscom_model_perscom {	

	public function loadRequests() {

		// Query the database to get all the requests
		$this->DB->build( array( 'select' => '*', 
			'from' => $this->settings['perscom_database_requests'], 
			'order' => 'primary_id_field DESC', 
			'where' => 'status="Pending"' ) );

		// Define array
		$rows = array();

		// Execute the DB query
		$result = $this->DB->execute();

		// Loop through the results and create an array of loas
		while( $r = $this->DB->fetch( $result ) )
		{
			// Format the date
			$r['date'] = strftime($this->settings['clock_short2'], $r['date']);

			// Format the description
			$r['description_link'] = '<u><a href="#" id="request_'.$r['primary_id_field'].'" class="request" type="'.$r['type'].'" relational="'.$r['relational_primary_id_field'].'">'.$r['description'].'</a></u>';
			
			// Add the members on end of the array
			array_push($rows, $r);
		}

		// Return the rows
		return $rows;
	}
}