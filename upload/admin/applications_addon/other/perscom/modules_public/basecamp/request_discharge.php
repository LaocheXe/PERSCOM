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

class public_perscom_basecamp_request_discharge extends ipsCommand
{
	public function doExecute( ipsRegistry $registry )
	{
		// Load our language files
		$this->registry->class_localization->loadLanguageFile( array( 'public_base_camp' ) );
		$this->registry->class_localization->loadLanguageFile( array( 'public_notifications' ) );

		// Set navigation
		$this->registry->output->addNavigation( $this->lang->words['base_camp'], 'app=perscom' );		
       	$this->registry->output->addNavigation( $this->lang->words['request_discharge'], NULL );

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
				$result = $this->requestDischarge();
			break;

			default:

			break;
		}

		// Output HTML
		$this->registry->output->setTitle( $this->lang->words['request_discharge'] );
        $this->registry->output->addContent( $this->registry->output->getTemplate('perscom')->requestDischarge( $result ) );
       	$this->registry->output->sendOutput();
	}

	public function requestDischarge() {

		// Check to make sure the user has no pending discharges
		$this->DB->build( array(
			'select' => '*',
			'from' => $this->settings['perscom_database_requests'],
			'where' => 'members_display_name="' .  $this->memberData['members_display_name'] . '" AND status="Pending" AND type="Discharge"') );

		// Get the results
		$results = $this->DB->execute();

		// If we want to continue inputing the Discharge 
		if ($results->num_rows == 0) {

			// Build query
			$this->DB->insert( $this->settings['perscom_database_requests'], array( 
				'member_id' => $this->memberData['member_id'], 
				'members_display_name' => $this->memberData['members_display_name'],
				'date' => strtotime('now'),
				'description' => sprintf('%s is requesting a(n) %s. Explanation: %s', $this->memberData['members_display_name'], $this->request['grade'], $this->request['explanation']),
				'type' => 'Discharge',
				'administrator_member_id' => 0,
				'administrator_members_display_name' => NULL,
				'status' => 'Pending',
		       	'relational_primary_id_field' => NULL ) );
		
			// Send a notification
			$this->notifications->sendNotification( array ( 
				'to' => $this->memberData, 
				'from' => $this->settings['perscom_application_submission_author'],
				'title' => 'PERSCOM: Discharge Submitted',
				'text' => $this->lang->words['notification_discharge_submission'] ) );			

			// Get user's personnel file so we can get their supervisor
			$personnel_file = $this->DB->buildAndFetch( array( 'select' => '*',
				'from' => $this->settings['perscom_database_personnel_files'],
				'where' => 'member_id="' . $this->memberData['member_id'] . '"') );

			// If we get a personnel file
			if ($personnel_file && $personnel_file['supervisor'] != 0) {
				
				// Send the notification to the user's supervisor
				$this->notifications->sendNotification( array (
					'to' => $personnel_file['supervisor'], 
					'from' => $this->settings['perscom_application_submission_author'], 
					'title' => sprintf( 'PERSCOM: %s Requested a Discharge', $this->memberData['members_display_name'] ), 
					'text' => sprintf( $this->lang->words['notification_discharge_submission_supervisor'], $this->memberData['members_display_name'], $this->request['grade'], $this->request['explanation'] ) ) );
			}

			// Return true
			return true;
		}

		// Return false
		return false;
	}
}