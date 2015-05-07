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

class public_perscom_basecamp_information extends ipsCommand
{
	private $stats = array();
	private $times = array();

	public function doExecute( ipsRegistry $registry )
	{
		// Load our language files
		$this->registry->class_localization->loadLanguageFile( array( 'public_base_camp' ) );
		$this->registry->class_localization->loadLanguageFile( array( 'public_notifications' ) );

		// Set navigation
		$this->registry->output->addNavigation( $this->lang->words['base_camp'], 'app=perscom' );		
       	$this->registry->output->addNavigation( $this->lang->words['operations_center'], NULL );

		// Check to make sure member is in unit group
		if ( !IPSMember::isInGroup( $this->memberData, $this->settings['perscom_base_unit_usergroup'] , true) ) {

			// Check to make sure they are in the administrative group
			if ( !IPSMember::isInGroup( $this->memberData, $this->settings['perscom_administrative_usergroup'] , true) ) {

				// Check to make sure they are in the retired group
				if ( !IPSMember::isInGroup( $this->memberData, $this->settings['perscom_retired_usergroup'] , true) ) {
			
					// Throw login error
					$this->registry->output->showError( 'You do not have permission to view this module.', 000123, false, '', 403 );
				}
			}
		}

		// Get the active users on PERSCOM
		$classToLoad = IPSLib::loadLibrary( IPS_ROOT_PATH . 'sources/classes/session/api.php', 'session_api' );
		$sessions = new $classToLoad( $this->registry );
		$activeUsers = $sessions->getUsersIn('perscom');

		// Get PERSCOM classes
		$this->notifications = $this->registry->notifications;
		$this->requests = $this->registry->requests;

		// Handle HTTP Request
		switch ($this->request['do']) 
		{
			case 'delete':
				$this->deleteFile();
			break;

			case 'muster':
				$result = $this->musterIn();
			break;

			default:

			break;
		}

		// Check if the current user is an administrator
		$admin = IPSMember::isInGroup( $this->memberData, $this->settings['perscom_administrative_usergroup'] , true);

		// Get personnel statistics
		$this->getPersonnelStatistics();

		// Get times for clocks
		$this->getTimes();

		// Get our list of TPR's
		$tpr = $this->getTPRs();

		// Get our list of LOA's
		$loa = $this->getLOAs();

		// Check if current user is on LOA
		$onLOA = $this->isOnLOA();

		// Get musters
		$this->getMusters();

		// Get our last stats
		$this->getRecentUpdates();

		// Output HTML
		$this->registry->output->setTitle( $this->lang->words['operations_center'] );
        $this->registry->output->addContent( $this->registry->output->getTemplate('perscom')->viewInformation( $tpr, $loa, $this->stats, $admin, $result, $onLOA, $this->times, $this->requests->loadRequests(), $activeUsers ) );
       	$this->registry->output->sendOutput();
	}

	public function getTPRs() {
		
		// Query the DB to look for all TPR's
		$this->DB->build( array( 'select' => '*', 
			'from' => $this->settings['perscom_database_tpr'], 
			'order' => 'expiration DESC' ) );

		// Define array
		$rows = array();

		// Execute the DB query
		$result = $this->DB->execute();

		// Loop through the results and create an array of RRO soldiers
		while( $r = $this->DB->fetch( $result ) )
		{
			// Evaluate if the time is within the expiration days
			if ($r['expiration'] < strtotime('+' . $this->settings['perscom_tpr_expiration'] . ' day') && $r['expiration'] >= strtotime('now')) {

				// Add the members on end of the array
				array_push($rows, $r);
			}
		}

		// Add to our stats array
		$this->stats['tpr'] = count($rows);

		// Return the result
		return $rows;
	}

	public function getLOAs() {
		
		// Query the DB to look for all TPR's
		$this->DB->build( array( 'select' => '*', 
			'from' => $this->settings['perscom_database_loa'], 
			'order' => 'end_date DESC', 
			'where' => 'returned="false"' ) );

		// Define array
		$rows = array();

		// Execute the DB query
		$result = $this->DB->execute();

		// Loop through the results and create an array of loas
		while( $loa = $this->DB->fetch( $result ) )
		{
			// Get personnel file
			$personnel_file = $this->DB->buildAndFetch( array( 
				'select' => 'p.*', 
				'from' => array( $this->settings['perscom_database_personnel_files'] => 'p' ),
			    'where' => 'p.member_id="' . $loa['member_id'] . '"',
				'add_join'	=> array(
								array(
									'select'	=> 'r.title AS rank_long, r.abbreviation AS rank_short',
									'from'		=> array( $this->settings['perscom_database_ranks']  => 'r' ),
									'where'		=> 'r.primary_id_field=p.rank',
									'type'		=> 'left',
								),
								array(
									'select'	=> 'u.name AS unit_long, u.unit_position AS unit_short',
									'from'		=> array( $this->settings['perscom_database_units']  => 'u' ),
									'where'		=> 'u.primary_id_field=p.combat_unit',
									'type'		=> 'left',
								),
								array(
									'select'	=> 's.primary_id_field AS status_id, s.status, s.hex_color AS status_hex_color',
									'from'		=> array( $this->settings['perscom_database_status']  => 's' ),
									'where'		=> 's.primary_id_field=p.status',
									'type'		=> 'left',
								),
							)
						)
					);				

			// Add the personnel file to the list
			$loa['personnel_file'] = $personnel_file;

			// Get approver's personnel file
			$approved_personnel_file = $this->DB->buildAndFetch( array( 
				'select' => 'p.*', 
				'from' => array( $this->settings['perscom_database_personnel_files'] => 'p' ),
			    'where' => 'p.member_id="' . $loa['approved_member_id'] . '"' ) );

			// Add the personnel file to the list
			$loa['approved_personnel_file'] = $approved_personnel_file;	

			// Add the members on end of the array
			array_push($rows, $loa);
		}

		// Add to our stats array
		$this->stats['loa'] = count($rows);

		//echo '<pre>'; print_r($rows); echo '</pre>'; exit;

		// Return the result
		return $rows;
	}

	public function getMusters() {

		// Query the DB to look for all TPR's
		$this->DB->build( array( 'select' => '*', 
			'from' => $this->settings['perscom_database_muster'], 
			'order' => 'display_name ASC' ) );

		// Execute the DB query
		$result = $this->DB->execute();

		// Set up variables
		$musters = array();
		$date = NULL;
			
		// Read the query
		while ( $r =  $this->DB->fetch( $results ) ) {
			
			// If today is sunday
			if (date('l', strtotime('now')) == "Sunday") {

				// If the results ais today, which is sunday
				if ( date('Y-m-d H:i:s', $r['date']) > date('Y-m-d H:i:s', strtotime('this Sunday') ) ) {

					// Add them to the array;
					array_push($musters, $r['display_name']);
				}
			}

			// Its not sunday, so get all the results greater than last sunday
			else {

				// If the results are greater than last sunda
				if ( date('Y-m-d H:i:s', $r['date']) > date('Y-m-d H:i:s', strtotime('last Sunday') ) ) {

					// Add them to the array;
					array_push($musters, $r['display_name']);
				}
			}
		}

		// Add to our stats array
		$this->stats['musters']['personnel'] = $musters;
		$this->stats['musters']['count'] = count($musters);
		$this->stats['musters']['date'] = date('D', strtotime('now')) == "Sun" ? strtotime('now') : strtotime('last Sunday');
	}

	public function getRecentUpdates() {
		
		// Query the DB to look for the last 5 discharge
		$this->DB->build( array( 'select' => '*', 
			'from' => $this->settings['perscom_database_records'], 
			'order' => 'date DESC', 
			'where' => 'type="Discharge" AND display="Yes"', 
			'limit' => array ( 0, 5 ) ) );

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

		// Add to our stats array
		$this->stats['discharges'] = $rows;

		// Query the DB to look for the last 5 awards
		$this->DB->build( array( 'select' => '*', 
			'from' => $this->settings['perscom_database_records'], 
			'order' => 'date DESC', 
			'where' => 'type="Award / Commendation" AND display="Yes"', 
			'limit' => array ( 0, 5 ) ) );

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

		// Add to our stats array
		$this->stats['awards'] = $rows;

		// Query the DB to look for the last 5 promotions
		$this->DB->build( array( 'select' => '*', 
			'from' => $this->settings['perscom_database_records'], 
			'order' => 'date DESC', 
			'where' => 'type="Promotion" AND display="Yes"', 
			'limit' => array ( 0, 5 ) ) );

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

		// Add to our stats array
		$this->stats['promotions'] = $rows;

		// Query the DB to look for the last 5 demotions
		$this->DB->build( array( 'select' => '*', 
			'from' => $this->settings['perscom_database_records'], 
			'order' => 'date DESC', 
			'where' => 'type="Demotion" AND display="Yes"', 
			'limit' => array ( 0, 5 ) ) );

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

		// Add to our stats array
		$this->stats['demotions'] = $rows;
	}

	public function getPersonnelStatistics() {

		// Query the DB to look for all TPR's
		$this->DB->build( array( 'select' => '*', 
			'from' => $this->settings['perscom_database_personnel_files'], 
			'where' => 'status="1"' ) );

		// Execute the DB query
		$result = $this->DB->execute();

		// Add to our stats array
		$this->stats['personnel'] = $result->num_rows;
	}

	public function deleteFile() {

		// Switch between the type of request
		switch ($this->request['type']) 
		{
			case 'LOA':

				// Delete the request
				$this->DB->delete( $this->settings['perscom_database_loa'], 'primary_id_field=' . $this->request['id'] );
				
				break;

			case 'TPR':

				// Delete the entry
				$this->DB->delete( $this->settings['perscom_database_tpr'], 'primary_id_field=' . $this->request['id'] );
				
				break;

			case 'Discharge':

				// Delete the entry
				$this->DB->delete( $this->settings['perscom_database_discharges'], 'primary_id_field=' . $this->request['relational'] );
				
				break;

			default:

				break;
		}
	}

	public function musterIn() {

		// Check to make sure the user has not already mustered today
		$this->DB->build( array(
			'select' => '*',
			'from' => $this->settings['perscom_database_muster'],
			'where' => 'member_id="' .  $this->memberData['member_id'] . '"') );

		// Set our continue bool
		$continue = TRUE;

		// Get the results
		$results = $this->DB->execute();

		// Read the query
		while ( $r =  $this->DB->fetch( $results ) ) {

			// If today is Sunday
			if ( date( 'l', strtotime('now') ) == 'Sunday' ) {

				// If any of the results are today or within last week since Sunday
				if ( date( 'Y-m-d H:i:s', $r['date'] ) > date( 'Y-m-d H:i:s', strtotime('this Sunday') ) ) {

					// Set our bool to stop
					$continue = FALSE;

					// Break the loop
					break;
				}
			}

			// It is not Sunday today
			else {

				// If any of the results are today or within last week since Sunday
				if ( date( 'Y-m-d H:i:s', $r['date'] ) > date( 'Y-m-d H:i:s', strtotime('last Sunday') ) ) {

					// Set our bool to stop
					$continue = FALSE;

					// Break the loop
					break;
				}
			}
		}

		// If we need to insert the muster
		if ($continue) {

			// Build query
			$this->DB->insert( $this->settings['perscom_database_muster'], array( 
				'member_id' => $this->memberData['member_id'], 
				'display_name' => $this->memberData['members_display_name'],
				'date' => strtotime('now') ) );

			// Send notification
			$this->notifications->sendNotification( array (
				'to' => $this->memberData, 
				'from' => $this->settings['perscom_application_submission_author'], 
				'title' => 'PERSCOM: Muster In', 
				'text' => $this->lang->words['notification_muster_in'],
				'key' => 'perscom_notification_muster' ) );

			// Return true, meaning the muster went through
			return true;
		}

		// Return false, meaning they have already mustered in
		return false;
	}

	public function isOnLOA() {

		// Query the DB to look for a possible active LOA where it has not been returned
		$loa = $this->DB->buildAndFetch( array( 'select' => '*', 
				'from' => $this->settings['perscom_database_loa'], 
				'where' => 'member_id="' .  $this->memberData['member_id'] .'" AND returned="false" AND status="Approved"' ) );

		// If we get a LOA back, return it
		if ($loa) {

			// Return the LOA
			return $loa;
		}

		// Return false, which means the user is not on LOA
		return false;
	}

	public function getTimes() {

		// Set PST
		date_default_timezone_set("US/Pacific");
		$this->times['pacific'] = strftime($this->settings['clock_long']);

		// Set MST
		date_default_timezone_set("US/Mountain");
		$this->times['mountain'] = strftime($this->settings['clock_long']);

		// Set CST
		date_default_timezone_set("US/Central");
		$this->times['central'] = strftime($this->settings['clock_long']);

		// Set EST
		date_default_timezone_set("US/Eastern");
		$this->times['eastern'] = strftime($this->settings['clock_long']);
	}
}