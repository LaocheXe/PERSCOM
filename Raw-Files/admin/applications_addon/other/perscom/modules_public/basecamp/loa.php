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

class public_perscom_basecamp_loa extends ipsCommand
{
	public function doExecute( ipsRegistry $registry )
	{
		// Load our language files
		$this->registry->class_localization->loadLanguageFile( array( 'public_base_camp' ) );
		$this->registry->class_localization->loadLanguageFile( array( 'public_notifications' ) );

		// Check to make sure member is in unit group
		if ( !IPSMember::isInGroup( $this->memberData, $this->settings['perscom_base_unit_usergroup'] , true) ) {

			// Check to make sure they are in the administrative group
			if ( !IPSMember::isInGroup( $this->memberData, $this->settings['perscom_administrative_usergroup'] , true) ) {
			
				// Throw login error
				$this->registry->output->showError( 'You do not have permission to view this module.', 000123, false, '', 403 );
			}
		}

		// Get PERSCOM classes
		$this->notifications = $this->registry->notifications;
		
		// Handle HTTP Request
		switch ($this->request['do']) 
		{
			case 'submit':
				$result = $this->submitLOA();
			break;

			case 'extend':
				$result = $this->extendLOA();
			break;

			case 'return':
				$result = $this->returnLOA();
			break;

			default:

			break;
		}

		// Handle HTTP Request
		switch ($this->request['mode']) 
		{
			case 'extend':

				// Set navigation
				$this->registry->output->addNavigation( $this->lang->words['base_camp'], 'app=perscom' );		
       			$this->registry->output->addNavigation( $this->lang->words['add_leave_of_absence'], 'app=perscom&module=basecamp&section=loa' );
       			$this->registry->output->addNavigation( $this->lang->words['extend_leave_of_absence'], NULL );

				// Output HTML
				$this->registry->output->setTitle( $this->lang->words['extend_leave_of_absence'] );
        		$this->registry->output->addContent( $this->registry->output->getTemplate('perscom')->extendLOA( ) );
			break;

			case 'return':

				// Set navigation
				$this->registry->output->addNavigation( $this->lang->words['base_camp'], 'app=perscom' );		
       			$this->registry->output->addNavigation( $this->lang->words['add_leave_of_absence'], 'app=perscom&module=basecamp&section=loa' );
       			$this->registry->output->addNavigation( $this->lang->words['return_leave_of_absence'], NULL );

				// Output HTML
				$this->registry->output->setTitle( $this->lang->words['return_leave_of_absence'] );
        		$this->registry->output->addContent( $this->registry->output->getTemplate('perscom')->returnLOA( ) );
			break;

			default:

				// Set navigation
				$this->registry->output->addNavigation( $this->lang->words['base_camp'], 'app=perscom' );		
       			$this->registry->output->addNavigation( $this->lang->words['add_leave_of_absence'], NULL );

				// Output HTML
				$this->registry->output->setTitle( $this->lang->words['add_leave_of_absence'] );
        		$this->registry->output->addContent( $this->registry->output->getTemplate('perscom')->requestLOA( $result ) );
			break;
		}

		// Send the HTML output
       	$this->registry->output->sendOutput();
	}

	public function submitLOA() {

		// Check to make sure the user has no active LOA's
		$loa = $this->DB->buildAndFetch( array(
			'select' => '*',
			'from' => $this->settings['perscom_database_loa'],
			'where' => 'member_id="' .  $this->memberData['member_id'] . '" AND returned="false"') );

		// If we dont get a loa back
		if (!$loa) {

			// Create our dates
			$startDate = DateTime::createFromFormat('m-d-Y', $this->request['start_date']);
			$endDate = DateTime::createFromFormat('m-d-Y', $this->request['end_date']);

			// Get the personnel file so we can set the group that they will transfer out of if approved
			$personnel_file = $this->DB->buildAndFetch( array( 'select' => '*', 
				'from' => $this->settings['perscom_database_personnel_files'], 
				'where' => 'member_id="' . $this->memberData['member_id'] . '"') );

			// Insert the LOA request
			$loa = $this->DB->insert( $this->settings['perscom_database_loa'], array( 
				'member_id' => $this->memberData['member_id'], 
				'display_name' => $this->memberData['members_display_name'],
				'created_date' => strtotime('now'),
				'start_date' => $startDate->getTimestamp(),
				'end_date' => $endDate->getTimestamp(),
				'explanation' => $this->request['explanation'],
				'status' => 'Pending',
				'combat_unit_id' => $personnel_file['combat_unit'],
		       	'returned' => 'false' ) );

			// Create log
			$this->DB->insert( $this->settings['perscom_database_requests'], array( 
				'member_id' => $this->memberData['member_id'], 
				'members_display_name' => $this->memberData['members_display_name'],
				'date' => strtotime('now'),
				'description' => sprintf('%s is requesting a Leave of Absence. Explanation:<br /><br /> %s', $this->memberData['members_display_name'], $this->request['explanation']),
				'type' => 'LOA',
				'administrator_member_id' => 0,
				'administrator_members_display_name' => NULL,
				'status' => 'Pending',
		       	'relational_primary_id_field' => $this->DB->getInsertId() ) );

			// Send a notification
			$this->notifications->sendNotification( array ( 
				'to' => $this->memberData, 
				'from' => $this->settings['perscom_application_submission_author'],
				'title' => 'PERSCOM: Leave of Absence Submitted',
				'text' => $this->lang->words['notification_loa_submission'] ) );

			// If we get a personnel file
			if ($personnel_file && $personnel_file['supervisor'] != 0) {
				
				// Send the notification to the user's supervisor
				$this->notifications->sendNotification( array (
					'to' => $personnel_file['supervisor'], 
					'from' => $this->settings['perscom_application_submission_author'], 
					'title' => sprintf( 'PERSCOM: %s Requested a LOA', $this->memberData['members_display_name'] ), 
					'text' => sprintf( $this->lang->words['notification_loa_submission_supervisor'], $this->memberData['members_display_name'], $startDate->format('F t, Y'), $endDate->format('F t, Y'), $this->request['explanation'] ) ) );
			}			

			// Return true
			return true;
		}

		// Return false
		return false;
	}

	public function extendLOA() {

		// If we did not load a LOA id
		if (!$this->request['id'] || !isset($this->request['id'])) {
	
			// Throw an error
			$this->registry->output->showError( 'Unable to load specified Leave of Absence.', 12345678, false, '', 404 );
		}

		// Convert the date to a timestamp
		$newEndDate = DateTime::createFromFormat('m-d-Y', $this->request['new_return_date']);

		// Update the leave of absence
		$this->DB->update( $this->settings['perscom_database_loa'], 
			array( 'end_date' => $newEndDate->getTimestamp() ), 
			'primary_id_field="' . $this->request['id'] . '"' );

		// Add service record
		$this->DB->insert( $this->settings['perscom_database_records'], array( 
			'member_id' => $this->memberData['member_id'], 
			'members_display_name' => $this->memberData['members_display_name'],
			'date' => strtotime('now'),
			'entry' => sprintf('Leave of Absence Extended. New Expected Return Date: %s', strftime($this->settings['clock_short2'], $newEndDate->getTimestamp())),
			'type' => 'LOA',
			'award' => '',
			'rank' => '',
			'discharge_grade' => '',
			'display' => 'Yes',
			'position' => '',
			'combat_unit' => '' ) );

		// Create log
		$this->DB->insert( $this->settings['perscom_database_requests'], array( 
			'member_id' => $this->memberData['member_id'], 
			'members_display_name' => $this->memberData['members_display_name'],
			'date' => strtotime('now'),
			'description' => sprintf('%s extended their Leave of Absence until %s', $this->memberData['members_display_name'], strftime($this->settings['clock_short2'], $newEndDate->getTimestamp())),
			'type' => 'LOA',
			'administrator_member_id' => $this->memberData['member_id'],
			'administrator_members_display_name' => $this->memberData['members_display_name'],
			'status' => '---',
	       	'relational_primary_id_field' => NULL ) );

		// Send a notification
		$this->notifications->sendNotification( array ( 
			'to' => $this->memberData, 
			'from' => $this->settings['perscom_application_submission_author'],
			'title' => 'PERSCOM: Leave of Absence Extended',
			'text' => $this->lang->words['notification_loa_extension'] ) );
	}

	public function returnLOA() {

		// If we did not load a LOA id
		if (!$this->request['id'] || !isset($this->request['id'])) {
	
			// Throw an error
			$this->registry->output->showError( 'Unable to load specified Leave of Absence.', 12345678, false, '', 404 );
		}

		// Query the database to make sure it has not been updated already
		$loa = $this->DB->buildAndFetch( array( 'select' => '*', 
			'from' => $this->settings['perscom_database_loa'], 
			'where' => 'primary_id_field=' . $this->request['id'] ) );

		// If it has not already been returned
		if ($loa['returned'] == "false") {

			// Create the update query
			$this->DB->update( $this->settings['perscom_database_loa'], array( 
				'status' => 'Returned',
		       	'returned' => 'true' ), 'primary_id_field="' . $this->request['id'] .'"' );

			// Update the personnel file
			$this->DB->update( $this->settings['perscom_database_personnel_files'], array( 
				'status' => '1', 
				'combat_unit' => $loa['combat_unit_id'] ), 'member_id="' . $this->memberData['member_id'] .'"' );

			// Add service record
			$this->DB->insert( $this->settings['perscom_database_records'], array( 
				'member_id' => $this->memberData['member_id'], 
				'members_display_name' => $this->memberData['members_display_name'],
				'date' => strtotime('now'),
				'entry' => 'Returned from Leave of Absence',
				'type' => 'LOA',
				'award' => '',
				'rank' => '',
				'discharge_grade' => '',
				'display' => 'Yes',
	     	  	'position' => '',
				'combat_unit' => '' ) );

			// Send a notification
			$this->notifications->sendNotification( array ( 
				'to' => $this->memberData, 
				'from' => $this->settings['perscom_application_submission_author'],
				'title' => 'PERSCOM: Return From LOA',
				'text' => $this->lang->words['notification_loa_return'] ) );
		}
	}
}