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

class public_perscom_basecamp_promote extends ipsCommand
{
	public function doExecute( ipsRegistry $registry )
	{
		// Load our language files
		$this->registry->class_localization->loadLanguageFile( array( 'public_base_camp' ) );
		$this->registry->class_localization->loadLanguageFile( array( 'public_notifications' ) );

		// Set navigation
		$this->registry->output->addNavigation( $this->lang->words['base_camp'], 'app=perscom' );		
       	$this->registry->output->addNavigation( $this->lang->words['file_promotion'], NULL );

		// Check to make sure member is in admin group
		if ( !IPSMember::isInGroup( $this->memberData, $this->settings['perscom_administrative_usergroup'] , true) ) {
			
			// Throw login error
			$this->registry->output->showError( 'You do not have permission to view this module.', 000123, false, '', 403 );
		}

		// Get PERSCOM classes
		$this->personnel = $this->registry->personnel;
		$this->ranks = $this->registry->ranks;
		$this->notifications = $this->registry->notifications;

		// Handle HTTP Request
		switch ($this->request['do']) 
		{
			case 'submit':
				$this->submitPromotion();
			break;

			default:

			break;
		}

		// Output HTML
		$this->registry->output->setTitle( $this->lang->words['file_promotion'] );
        $this->registry->output->addContent( $this->registry->output->getTemplate('perscom')->filePromotion( $this->personnel->loadPersonnel(), $this->ranks->loadRanks() ) );
       	$this->registry->output->sendOutput();
	}

	public function submitPromotion() {

		// Get the soldier who is being promoted
		$soldier = IPSMember::load( $this->request['soldier'] );
		
		// If we did not load a solider
		if (!$soldier) {
	
			// Throw an error
			$this->registry->output->showError( 'Unable to load specified soldier.', 12345678, false, '', 404 );
		}

		// Get the rank
		$rank = $this->DB->buildAndFetch( array( 'select' => '*', 
			'from' => $this->settings['perscom_database_ranks'], 
			'where' => 'primary_id_field="' . $this->request['rank'] . '"' ) );

		// Create our date
		$date = DateTime::createFromFormat('m-d-Y', $this->request['date']);
	
		// Add to log
		$this->DB->insert( $this->settings['perscom_database_requests'], array( 
			'member_id' => $soldier['member_id'], 
			'members_display_name' => $soldier['members_display_name'],
			'date' => strtotime('now'),
			'description' => sprintf('%s was promoted to the rank of %s', $soldier['members_display_name'], $rank['title']),
			'type' => 'Promotion',
			'administrator_member_id' => $this->memberData['member_id'],
			'administrator_members_display_name' => $this->memberData['members_display_name'],
			'status' => '---',
			'relational_primary_id_field' => NULL ) );

		// Add service record
		$this->DB->insert( $this->settings['perscom_database_records'], array( 
			'member_id' => $soldier['member_id'], 
			'members_display_name' => $soldier['members_display_name'],
			'date' => $date->getTimestamp(),
			'entry' => sprintf('Promoted to %s', $rank['title']),
			'type' => 'Promotion',
			'award' => '',
			'rank' => $rank['title'],
			'discharge_grade' => '',
			'display' => $this->request['service_record'],
	       	'position' => '',
			'combat_unit' => '' ) );

		// Update personnel file
		$this->DB->update( $this->settings['perscom_database_personnel_files'], array ( 
			'rank' => $rank['primary_id_field'], 
			'promotion_date' => strtotime('now') ), 'member_id="' . $soldier['member_id'] . '"' );

		// Update soldiers new name
		IPSMember::save( $soldier['member_id'], array( 'core' => array( 
			'members_display_name' => $this->request['new_name'], 
			'name' => $this->request['new_name'], 
			'title' => $rank['title'] ) ) );

		// Update soldiers avatar
		$this->DB->update( 'profile_portal', array ( 
			'pp_main_photo' => 'perscom/insignia/large/multicam_background/'.$rank['abbreviation'].'.jpg', 
			'pp_main_width' => '100', 
			'pp_main_height' => '100', 
			'pp_thumb_photo' => 'perscom/insignia/large/multicam_background/'.$rank['abbreviation'].'.jpg', 
			'pp_thumb_width' => '100', 
			'pp_thumb_height' => '100' ), 'pp_member_id="'.$soldier['member_id'].'"' );

		// If send the notification
		if ($this->request['opord'] == 'Yes') {

			// Send a notification
			$this->notifications->sendNotification( array ( 
				'to' => $soldier, 
				'from' => $this->settings['perscom_application_submission_author'],
				'title' => 'PERSCOM: Promotion',
				'text' => sprintf( $this->lang->words['notification_promotion'], $rank['title'], $this->request['new_name'] ) ) );	

		}
	}
}