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

class perscom_model_combat_units extends perscom_model_perscom {	

	public function loadCombatUnits($column = NULL) {

		// Query the DB to get all the combat units
		$this->DB->build( array( 'select' => '*', 
			'from' => $this->settings['perscom_database_units'], 
			'order' => '`order` ASC', 
			'where' => 'name!="Civilians"' ) );

		// Define array
		$rows = array();

		// Execute the DB query
		$result = $this->DB->execute();

		// Loop through the results and create an array of RRO soldiers
		while( $r = $this->DB->fetch( $result ) )
		{
			// If we want to fetch a specific column
			if (!is_null($column)) {
				
				// Add the sepcific column onto the array
				array_push($rows, $r[$column]);
			}

			// We want to fetch all columns
			else {

				// Add the admin unit on end of the array
				array_push($rows, $r);
			}
		}

		// Return the result
		return $rows;
	}

	public function loadCombatUnitInformation($id) {

		// Query the DB to get the combat unit
		$information = $this->DB->buildAndFetch( array( 'select' => '*', 
					'from' => $this->settings['perscom_database_units'], 
					'where' => 'primary_id_field="' . $id . '"' ) );

		// Query the DB to get the combat unit
		return $information;
	}
}