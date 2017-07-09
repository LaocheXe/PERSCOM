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

class perscom_model_awards extends perscom_model_perscom {	

	public function loadAwards( $award_type = '', $order = 'title ASC' ) {

		// Query the DB to get all the awards
		$this->DB->build( array( 'select' => '*', 
			'from' => $this->settings['perscom_database_awards'], 
			'order' => isset($order) && $order != '' ? $order : '',
	       	'where' => isset($award_type) && $award_type != '' ? 'type="' . $award_type . '"' : '' ) );

		// Define array
		$rows = array();

		// Execute the DB query
		$result = $this->DB->execute();

		// Loop through the results and create an array of RRO soldiers
		while( $r = $this->DB->fetch( $result ) )
		{
			// Add the members on end of the array
			array_push($rows, $r);
		}

		// Return the result
		return $rows;
	}
}