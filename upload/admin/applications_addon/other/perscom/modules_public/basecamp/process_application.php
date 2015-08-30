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

class public_perscom_basecamp_process_application extends ipsCommand
{
	public function doExecute( ipsRegistry $registry )
	{
		// Load our language files
		$this->registry->class_localization->loadLanguageFile( array( 'public_base_camp' ) );
		$this->registry->class_localization->loadLanguageFile( array( 'public_notifications' ) );

		// Set navigation
		$this->registry->output->addNavigation( $this->lang->words['base_camp'], 'app=perscom' );		
       	$this->registry->output->addNavigation( $this->lang->words['process_applicant'], NULL );

		// Check to make sure member is in admin group
		if ( !IPSMember::isInGroup( $this->memberData, $this->settings['perscom_administrative_usergroup'] , true) ) {
			
			// If they arent in the admin group but are in the BMO group
			if ( !IPSMember::isInGroup( $this->memberData, $this->settings['perscom_recruiting_and_retention_group'] , true) ) {
			
				// Throw login error
				$this->registry->output->showError( 'You do not have permission to view this module.', 000123, false, '', 403 );
			}
		}

		// Get perscom classes
		$this->ranks = $this->registry->ranks;
		$this->combat_units = $this->registry->combat_units;
		$this->weapons = $this->registry->weapons;
		$this->notifications = $this->registry->notifications;
		$this->messenger = $this->registry->messenger;
		$this->personnel = $this->registry->personnel;
		$this->enlistment_applications = $this->registry->enlistment_applications;

		// Handle HTTP Request
		switch ($this->request['do']) 
		{
			case 'submit':
				$this->processApplication();
			break;

			default:

			break;
		}		

		// Set HTML settings
		$this->registry->output->setTitle( $this->lang->words['process_applicant'] );
        $this->registry->output->addContent( $this->registry->output->getTemplate('perscom')->processApplicant( $this->enlistment_applications->loadEnlistmentApplications(), $this->ranks->loadRanks(), $this->combat_units->loadCombatUnits(), $this->weapons->loadWeapons(), $this->personnel->loadPersonnel(), $this->personnel->loadRecruitingMediums(), $this->personnel->loadUniforms() ) );
       	$this->registry->output->sendOutput();
	}

	public function processApplication() {

		// Update our personnel file
		$this->DB->update( $this->settings['perscom_database_personnel_files'], array( 
			'rank' => $this->request['rank'], 
			'position' => $this->request['position'], 
			'mos' => $this->request['mos'], 
			'combat_unit' => $this->request['unit'], 
			'supervisor' => $this->request['supervisor'],
			'recruiter' => $this->request['recruiter'],
			'recruiting_medium' => $this->request['recruiting_medium'],
			'weapon' => $this->request['weapon'], 
			'status' => '1', 
			'uniform' => $this->request['uniform'],
			'induction_date' => strtotime('now'), 
			'promotion_date' => strtotime('now') ), 'primary_id_field=' . $this->request['application'] );

		// Query the DB to get the application that we are dealing with
		$application = $this->DB->buildAndFetch( array( 'select' => '*', 
			'from' => $this->settings['perscom_database_personnel_files'], 
			'where' => 'primary_id_field="' . $this->request['application'] .'"' ) );

		// Query the DB to get the application that we are dealing with
		$rank = $this->DB->buildAndFetch( array( 'select' => '*', 
			'from' => $this->settings['perscom_database_ranks'], 
			'where' => 'primary_id_field="' . $this->request['rank'] .'"' ) );

		// Query the DB to get the unit that the soldier is being assigned to
		$unit = $this->DB->buildAndFetch( array( 'select' => '*', 
			'from' => $this->settings['perscom_database_units'], 
			'where' => 'primary_id_field="' . $this->request['unit'] .'"' ) );

		// Create our formatted display name to save to the member DB
		$name = trim(sprintf('%s %s.%s', $rank['abbreviation'], strtoupper(substr($application['firstname'], 0, 1)), ucfirst(strtolower($application['lastname']))));

		// Update the user in IPB
		IPSMember::save( $application['member_id'], array( 'core' => array( 
			'members_display_name' => $name, 
			'name' => $name,
			'members_seo_name' => strtolower(str_replace('.', '', $name)),
			'title' => $rank['title'],
			'member_group_id' => $this->settings['perscom_base_unit_usergroup'],
	       	'mgroup_others' => $unit['forum_usergroup'] ) ) );

		// If the update avatar setting is set to yes
		if ($this->settings['perscom_update_avatar']) {
	
			// Update soldiers avatar
			$this->DB->update( 'profile_portal', array ( 
				'pp_main_photo' => 'perscom/insignia/large/multicam_background/'.$rank['abbreviation'].'.jpg', 
				'pp_main_width' => '100', 
				'pp_main_height' => '100', 
				'pp_thumb_photo' => 'perscom/insignia/large/multicam_background/'.$rank['abbreviation'].'.jpg', 
				'pp_thumb_width' => '100', 
				'pp_thumb_height' => '100' ), 
				'pp_member_id="' . $application['member_id'] . '"' );
		}

		// Add to log
		$this->DB->insert( $this->settings['perscom_database_requests'], array( 
			'member_id' => $application['member_id'], 
			'members_display_name' => sprintf('%s %s', $application['firstname'], $application['lastname']),
			'date' => strtotime('now'),
			'description' => sprintf('%s %s\'s Enlistment Application was Approved. Rank: %s, Position: %s, Unit: %s', $application['firstname'], $application['lastname'], $rank['title'], $this->request['position'], $unit['name']),
			'type' => 'Enlistment Application',
			'administrator_member_id' => $this->memberData['member_id'],
			'administrator_members_display_name' => $this->memberData['members_display_name'],
			'status' => 'Approved',
			'relational_primary_id_field' => NULL ) );

		// Update the accepted applications
		$this->DB->update( $this->settings['perscom_database_settings'], '`value`=`value`+1', '`key`="accepted_applications"', false, true );

		// If new enlistment
		if ($this->request['enlistment_type'] == 'New Enlistment') {

			// Add our service record entries
			$this->DB->insert( $this->settings['perscom_database_records'], array( 
				'member_id' => $application['member_id'], 
				'members_display_name' => $name,
				'date' => strtotime('now'),
				'entry' => 'Enlisted with the ' . $this->settings['board_name'],
				'type' => 'Enlistment',
				'award' => '',
				'rank' => '',
				'discharge_grade' => '',
				'display' => 'Yes',
		       	'position' => '',
				'combat_unit' => '' ) );

			// Add our service record entries
			$this->DB->insert( $this->settings['perscom_database_records'], array( 
				'member_id' => $application['member_id'], 
				'members_display_name' => $name,
				'date' => strtotime('now'),
				'entry' => sprintf('Inducted as a %s', $rank['title']),
				'type' => 'Induction',
				'award' => '',
				'rank' => '',
				'discharge_grade' => '',
				'display' => 'Yes',
		       	'position' => '',
				'combat_unit' => '' ) );
		}
		else {

			// Add our service record entries
			$this->DB->insert( $this->settings['perscom_database_records'], array( 
				'member_id' => $application['member_id'], 
				'members_display_name' => $name,
				'date' => strtotime('now'),
				'entry' => sprintf('Re-instated as %s', $rank['title']),
				'type' => 'Enlistment',
				'award' => '',
				'rank' => '',
				'discharge_grade' => '',
				'display' => 'Yes',
		       	'position' => '',
				'combat_unit' => '' ) );
		}

		// Add our service record entries
		$this->DB->insert( $this->settings['perscom_database_records'], array( 
			'member_id' => $application['member_id'], 
			'members_display_name' => $name,
			'date' => strtotime('now'),
			'entry' => sprintf('Assigned to %s', $unit['name']),
			'type' => 'Assignment',
			'award' => '',
			'rank' => '',
			'discharge_grade' => '',
			'display' => 'Yes',
	       	'position' => $this->request['position'],
			'combat_unit' => $unit['name'] ) );

		// Send a notification to user
		$this->notifications->sendNotification( array ( 
			'to' => $application['member_id'], 
			'from' => $this->settings['perscom_application_submission_author'],
			'title' => 'PERSCOM: Enlistment Application Processed',
			'text' => sprintf( $this->lang->words['notification_application_processed'], $name ) ) );

		// Get recruiters's name
		$recruiter = IPSMember::load( $this->request['recruiter'] );

		// Get the supervisor's name
		$supervisor = IPSMember::load( $this->request['supervisor'] );

		// Send the private message
		$this->messenger->sendPrivateMessage( array (
			'to' => $application['member_id'], 
			'from' => $this->memberData['member_id'], 
			'title' => 'PERSCOM: Enlistment Application Information', 
			'text' => sprintf( $this->lang->words['notification_application_private_message'], ucfirst(strtolower($application['firstname'])), $rank['title'], $unit['name'], $this->request['position'], $supervisor['members_display_name'], $recruiter['members_display_name'] ) ) );
			
	}
}