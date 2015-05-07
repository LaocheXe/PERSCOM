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

class public_perscom_basecamp_tpr extends ipsCommand
{
	public function doExecute( ipsRegistry $registry )
	{
		// Load our language files
		$this->registry->class_localization->loadLanguageFile( array( 'public_base_camp' ) );
		$this->registry->class_localization->loadLanguageFile( array( 'public_notifications' ) );

		// Set navigation
		$this->registry->output->addNavigation( $this->lang->words['base_camp'], 'app=perscom' );		
       	$this->registry->output->addNavigation( $this->lang->words['add_temporary_pass_request'], NULL );

		// Check to make sure member is in unit group
		if ( !IPSMember::isInGroup( $this->memberData, $this->settings['perscom_base_unit_usergroup'] , true) ) {

			// Check to make sure they are in the administrative group
			if ( !IPSMember::isInGroup( $this->memberData, $this->settings['perscom_administrative_usergroup'] , true) ) {
			
				// Throw login error
				$this->registry->output->showError( 'You do not have permission to view this module.', 000123, false, '', 403 );
			}
		}

		// Load perscom classes
		$this->notifications = $this->registry->notifications;

		// Handle HTTP Request
		switch ($this->request['do']) 
		{
			case 'submit':
				$this->submitTPR();
			break;

			default:

			break;
		}

		// Output HTML
		$this->registry->output->setTitle( $this->lang->words['add_temporary_pass_request'] );
        $this->registry->output->addContent( $this->registry->output->getTemplate('perscom')->requestTPR() );
       	$this->registry->output->sendOutput();
	}

	public function submitTPR() {

		// Build query
		$this->DB->insert( $this->settings['perscom_database_tpr'], array( 
			'member_id' => $this->memberData['member_id'], 
			'display_name' => $this->memberData['members_display_name'],
			'explanation' => $this->request['event'] . ' - ' . $this->request['explanation'],
			'expiration' => strtotime('+' . $this->settings['perscom_tpr_expiration'] . ' day') ) );

		// Send the notification to the user
		$this->notifications->sendNotification( array (
			'to' => $this->memberData, 
			'from' => $this->settings['perscom_application_submission_author'], 
			'title' => 'PERSCOM: Temporary Pass Request Submitted', 
			'text' => sprintf( $this->lang->words['notification_tpr_submission'], $this->settings['perscom_tpr_expiration'] ),
			'key' => 'perscom_notification_tpr' ) );

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
				'title' => sprintf( 'PERSCOM: %s Submitted a TPR', $this->memberData['members_display_name'] ), 
				'text' => sprintf( $this->lang->words['notification_tpr_submission_supervisor'], $this->memberData['members_display_name'], $this->request['event'], $this->settings['perscom_tpr_expiration'], $this->request['explanation'] ) ) );
		}
	}
}