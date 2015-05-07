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

class public_perscom_basecamp_addawardentry extends ipsCommand
{
	public function doExecute( ipsRegistry $registry )
	{
		// Load our language files
		$this->registry->class_localization->loadLanguageFile( array( 'public_base_camp' ) );
		$this->registry->class_localization->loadLanguageFile( array( 'public_notifications' ) );

		// Set navigation
		$this->registry->output->addNavigation( $this->lang->words['base_camp'], 'app=perscom' );		
       	$this->registry->output->addNavigation( $this->lang->words['add_award_entry'], NULL );

		// Check to make sure member is in admin group
		if ( !IPSMember::isInGroup( $this->memberData, $this->settings['perscom_administrative_usergroup'] , true) ) {

			// If they arent in the admin group but are in the BMO group
			if ( !IPSMember::isInGroup( $this->memberData, $this->settings['perscom_bmo_usergroup'] , true) ) {
			
				// Throw login error
				$this->registry->output->showError( 'You do not have permission to view this module.', 000123, false, '', 403 );
			}
		}

		// Get perscom classes
		$this->personnel = $this->registry->personnel;
		$this->awards = $this->registry->awards;
		$this->notifications = $this->registry->notifications;

		// Handle HTTP Request
		switch ($this->request['do']) 
		{
			case 'submit':
				$this->submitAward();
			break;

			default:

			break;
		}

		// Output HTML
		$this->registry->output->setTitle( $this->lang->words['add_award_entry'] );
        $this->registry->output->addContent( $this->registry->output->getTemplate('perscom')->addAwardEntry( $this->personnel->loadPersonnel(), $this->awards->loadAwards() ) );
       	$this->registry->output->sendOutput();
	}

	public function submitAward() {

		// Get the soldier who is being promoted
		$soldier = IPSMember::load( $this->request['soldier'] );

		// If we did not load a solider
		if (!$soldier) {
	
			// Throw an error
			$this->registry->output->showError( 'Unable to load specified soldier.', 12345678, false, '', 404 );
		}

		// Create our date
		$date = DateTime::createFromFormat('m-d-Y', $this->request['date']);

		// Add to log
		$this->DB->insert( $this->settings['perscom_database_requests'], array( 
			'member_id' => $soldier['member_id'], 
			'members_display_name' => $soldier['members_display_name'],
			'date' => strtotime('now'),
			'description' => sprintf('%s was awarded the %s', $soldier['members_display_name'], $this->request['award']),
			'type' => 'Award / Commendation',
			'administrator_member_id' => $this->memberData['member_id'],
			'administrator_members_display_name' => $this->memberData['members_display_name'],
			'status' => '---',
			'relational_primary_id_field' => NULL ) );

		// Add service record
		$this->DB->insert( $this->settings['perscom_database_records'], array( 
			'member_id' => $soldier['member_id'], 
			'members_display_name' => $soldier['members_display_name'],
			'date' => $date->getTimestamp(),
			'entry' => sprintf('Awarded the %s', $this->request['award']),
			'type' => 'Award / Commendation',
			'award' => $this->request['award'],
			'rank' => '',
			'discharge_grade' => '',
			'display' => $this->request['service_record'],
	       	'position' => '',
			'combat_unit' => '' ) );

		// If send the notification
		if ($this->request['opord'] == 'Yes') {

			// Send the notification
			$this->notifications->sendNotification( array (
				'to' => $soldier, 
				'from' => $this->settings['perscom_application_submission_author'], 
				'title' => 'PERSCOM: Award / Commendation', 
				'text' => sprintf( $this->lang->words['notification_award'], $this->request['award'] ) ) );
		}
	}
}