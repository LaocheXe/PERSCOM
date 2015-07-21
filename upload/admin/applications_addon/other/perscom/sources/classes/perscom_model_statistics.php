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

class perscom_model_statistics extends perscom_model_perscom {	

	public function loadEnlistmentApplicationTrends() {

		// Query the DB to look for all TPR's
		$this->DB->build( array( 'select' => 'enlistment_date', 
			'from' => $this->settings['perscom_database_personnel_files'] ) );

		// Define array
		$rows = array();

		// Execute the DB query
		$result = $this->DB->execute();

		// Loop through the results and create an array of loas
		while( $r = $this->DB->fetch( $result ) )
		{
			// Add the members on end of the array
			array_push($rows, $r['enlistment_date']);
		}

		$months = array("January" => array(),
			"February" => array(),
			"March" => array(),
			"April" => array(),
			"May" => array(),
			"June" => array(),
			"July" => array(),
			"August" => array(),
			"September" => array(),
			"October" => array(),
			"November" => array(),
			"December" => array() );

		// Create our data array
		$data = array( "enlistment" => $months, "members" => $months );

		// Loop over timestamps
		foreach ($rows as $timestamp) {

			// Get the date of the timestamp
			$date = getdate($timestamp);
			
			// Add it to the array under the already created month key
			array_push($data['enlistment'][$date['month']], $timestamp);
		}

		// Query the DB to look for registration dates
		$this->DB->build( array( 'select' => 'joined', 
			'from' => 'members' ) );

		// Define array
		$members = array();

		// Execute the DB query
		$result = $this->DB->execute();

		// Loop through the results and create an array of loas
		while( $r = $this->DB->fetch( $result ) )
		{
			// Add the members on end of the array
			array_push($members, $r['joined']);
		}

		// Loop over timestamps
		foreach ($members as $timestamp) {

			// Get the date of the timestamp
			$date = getdate($timestamp);
			
			// Add it to the array under the already created month key
			array_push($data['members'][$date['month']], $timestamp);
		}

		// Return the applications
		return $data;
	}

	public function loadRecruitingMediumTrends() {

		// Get our recruiting mediums
		$mediums = array_map(function() { return '0'; }, array_flip($this->registry->personnel->loadRecruitingMediums()));

		// Query the DB to look for all TPR's
		$this->DB->build( array( 'select' => '*', 
			'from' => $this->settings['perscom_database_personnel_files'] ) );

		// Execute the DB query
		$result = $this->DB->execute();

		// Loop through the results and create an array of loas
		while( $r = $this->DB->fetch( $result ) )
		{
			// If the soldier's recruiting medium matches with one in the array
			if (array_key_exists($r['recruiting_medium'], $mediums)) {
				
				// Increase the value of that key by one
				++$mediums[$r['recruiting_medium']];
			}
		}

		// Return the mediums
		return $mediums;
	}

	public function loadEnlistmentApplicationStatistics() {

		// Get the number of total applications
		$total_applications = $this->DB->buildAndFetch( array( 'select' => '*', 
			'from' => $this->settings['perscom_database_settings'], 
			'where' => '`key`="total_applications"' ) );

		// Get the number of accepted applications
		$accepted_applications = $this->DB->buildAndFetch( array( 'select' => '*', 
			'from' => $this->settings['perscom_database_settings'], 
			'where' => '`key`="accepted_applications"' ) );

		// Get the number of accepted applications
		$denied_applications = $this->DB->buildAndFetch( array( 'select' => '*', 
			'from' => $this->settings['perscom_database_settings'], 
			'where' => '`key`="denied_applications"' ) );

		// Get the number of accepted applications
		$dropped_applications = $this->DB->buildAndFetch( array( 'select' => '*', 
			'from' => $this->settings['perscom_database_settings'], 
			'where' => '`key`="dropped_applications"' ) );

		// Get the latest reset date of the enlistment statistics
		$enlistment_statistics_reset = $this->DB->buildAndFetch( array( 'select' => '*', 
			'from' => $this->settings['perscom_database_settings'], 
			'where' => '`key`="enlistment_statistics_reset"' ) );

		// Get all the recruiters
		$this->DB->build( array( 'select' => 'recruiter',
			'from' => $this->settings['perscom_database_personnel_files'],
			'where' => 'induction_date > ' . $enlistment_statistics_reset['value'] ) );

		// Execute the DB query
		$recruiters_result = $this->DB->execute();

		// Create an array to store all the recruiters
		$recruiters = array();

		// Loop through the results and add to array
		while( $r = $this->DB->fetch( $recruiters_result ) )
		{
			// If not a 0
			if ($r['recruiter'] != '0') {
				
				// Add the result to the recruiters array
				array_push($recruiters, $r['recruiter']);
			}
		}

		// Count the array and look for the most popular result
		$count = array_count_values($recruiters); 

		// If we have an array with more than one element
		if (count($count) > 0) {

			// Get the most active recruiter's member id
			$recruiter = array_search(max($count), $count);

			// If we get a result
			if ($recruiter) {
				
				// Get the member
				$recruiter_pfile = IPSMember::load( $recruiter );

				// If we get a member profile
				if ($recruiter_pfile) {
					
					// Add the statistic
					$stats['most_active_recruiter'] = $recruiter_pfile['members_display_name'] . ' - ' . $count[$recruiter] . ' recruit(s) since ' . strftime($this->settings['clock_short2'], $enlistment_statistics_reset['value']);
				}

				// Unable to load member
				else {

					// Inform the user
					$stats['most_active_recruiter'] = 'Unable to load recruiter';
				}
			}
		}

		// No recruiters found
		else {

			// Inform the user
			$stats['most_active_recruiter'] = 'There have been no assigned recruiters since the last reset date';
		}

		// Set our stats array
		$stats['total_applications'] = $total_applications['value'];
		$stats['applications_accepted'] = $accepted_applications['value'];
		$stats['denied_applications'] = $denied_applications['value'];
		$stats['dropped_applications'] = $dropped_applications['value'];
		$stats['enlistment_statistics_reset'] = $enlistment_statistics_reset['value'];

		// Query the DB to look for all accepted enlisment applications from today's year
		$this->DB->build( array( 'select' => '*', 
			'from' => $this->settings['perscom_database_personnel_files'], 
			'where' => 'FROM_UNIXTIME(enlistment_date, "%Y")=YEAR(CURDATE()) AND status != "8" AND status != "7" AND status !="5"' ) );

		// Execute the DB query
		$result = $this->DB->execute();

		// Create an array to store all the processing days values
		$days = array();

		// Loop through the accepted applications and find the difference between the date applied and date enlisted
		while( $r = $this->DB->fetch( $result ) )
		{
			// Calculate the difference in time between the induction date and enlistment date and add the number of days to the array
			$datediff = $r['enlistment_date'] - $r['induction_date'];
			array_push($days, abs(floor($datediff/(60*60*24))));
		}

		// Set the average processing day value
		$stats['application_processing_time'] = count($days) > 0 ? round(array_sum($days) / count($days), 3) : 0;

		// Return the stats
		return $stats;
	}

	public function resetEnlistmentApplicationStatistics() {

		// Update the last reset statistics date
		$this->DB->update( $this->settings['perscom_database_settings'], array( '`value`' => strtotime('now') ), '`key`="enlistment_statistics_reset"' );

		// Update the total applications
		$this->DB->update( $this->settings['perscom_database_settings'], array( '`value`' => '0' ), '`key`="total_applications"' );

		// Update the accepted applications
		$this->DB->update( $this->settings['perscom_database_settings'], array( '`value`' => '0' ), '`key`="accepted_applications"' );

		// Update the denied applications
		$this->DB->update( $this->settings['perscom_database_settings'], array( '`value`' => '0' ), '`key`="denied_applications"' );

		// Update dropped applications
		$this->DB->update( $this->settings['perscom_database_settings'], array( '`value`' => '0' ), '`key`="dropped_applications"' );
	}
}