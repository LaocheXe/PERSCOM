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

class public_perscom_basecamp_discharge extends ipsCommand
{
	public function doExecute( ipsRegistry $registry )
	{
		// Load our language files
		$this->registry->class_localization->loadLanguageFile( array( 'public_base_camp' ) );
		$this->registry->class_localization->loadLanguageFile( array( 'public_notifications' ) );

		// Set navigation
		$this->registry->output->addNavigation( $this->lang->words['base_camp'], 'app=perscom' );		
       	$this->registry->output->addNavigation( $this->lang->words['file_discharge'], NULL );

		// Check to make sure member is in admin group
		if ( !IPSMember::isInGroup( $this->memberData, $this->settings['perscom_administrative_usergroup'] , true) ) {
			
			// Throw login error
			$this->registry->output->showError( 'You do not have permission to view this module.', 000123, false, '', 403 );
		}

		// Get PERSCOM classes
		$this->personnel = $this->registry->personnel;
		$this->notifications = $this->registry->notifications;

		// Handle HTTP Request
		switch ($this->request['do']) 
		{
			case 'submit':
				$this->submitDischarge();
			break;

			default:

			break;
		}

		// Output HTML
		$this->registry->output->setTitle( $this->lang->words['file_discharge'] );
        $this->registry->output->addContent( $this->registry->output->getTemplate('perscom')->fileDischarge( $this->personnel->loadPersonnel() ) );
       	$this->registry->output->sendOutput();
	}

	public function submitDischarge() {

		// Get the soldier who is being promoted
		$soldier = IPSMember::load( $this->request['soldier'] );

		// If we did not load a solider
		if (!$soldier) {
	
			// Throw an error
			$this->registry->output->showError( 'Unable to load specified soldier.', 12345678, false, '', 404 );
		}

		// Create our date
		$date = DateTime::createFromFormat('m-d-Y', $this->request['date']);

		// If the grade was honorable retirement
		if ($this->request['grade'] == 'Honorable Retirement') {	
			
			// Add to log
			$this->DB->insert( $this->settings['perscom_database_requests'], array( 
				'member_id' => $soldier['member_id'], 
				'members_display_name' => $soldier['members_display_name'],
				'date' => strtotime('now'),
				'description' => sprintf('%s was Honorably Retired', $soldier['members_display_name']),
				'type' => 'Retirement',
				'administrator_member_id' => $this->memberData['member_id'],
				'administrator_members_display_name' => $this->memberData['members_display_name'],
				'status' => '---',
				'relational_primary_id_field' => NULL ) );

			// Add service record
			$this->DB->insert( $this->settings['perscom_database_records'], array( 
				'member_id' => $soldier['member_id'], 
				'members_display_name' => $soldier['members_display_name'],
				'date' => $date->getTimestamp(),
				'entry' => 'Honorably Retired',
				'type' => 'Retirement',
				'award' => '',
				'rank' => '',
				'discharge_grade' => '',
				'display' => $this->request['service_record'],
		       	'position' => '',
				'combat_unit' => '' ) );

			// Get the retired group forum id
			$combat_unit = $this->DB->buildAndFetch( array ( 'select' => '*', 
				'from' => $this->settings['perscom_database_units'], 
				'where' => 'name="Retired"' ) );

			// Update personnel file
			$this->DB->update( $this->settings['perscom_database_personnel_files'], array ( 
				'combat_unit' => $combat_unit['primary_id_field'], 
				'position' => 'Retired', 
				'status' => '9' ), 'member_id="' . $soldier['member_id'] . '"' );

			// Set the new name
			$new_name = explode(' ', $soldier['members_display_name'], 2);
			$new_name[0] = 'RET';
			$name = implode(' ', $new_name);

			// Set the retired members properties
			IPSMember::save( $soldier['member_id'], array( 'core' => array( 
				'member_group_id' => $this->settings['perscom_retired_usergroup'], 
				'mgroup_others' => '',
		       	'title' => 'Retired',
				'name' => $name,
				'members_display_name' => $name ) ) );

			// If send the notification
			if ($this->request['opord'] == 'Yes') {

				// Send the notification
				$this->notifications->sendNotification( array (
					'to' => $soldier, 
					'from' => $this->settings['perscom_application_submission_author'], 
					'title' => 'PERSCOM: Honorable Retirement', 
					'text' => sprintf( $this->lang->words['notification_retirement'], $name ) ) );
			}
		}

		// Normal discharge
		else {

			// Delete personnel file
			$this->DB->delete( $this->settings['perscom_database_personnel_files'], 
				'member_id="' . $soldier['member_id'] . '"');

			// Add to log
			$this->DB->insert( $this->settings['perscom_database_requests'], array( 
				'member_id' => $soldier['member_id'], 
				'members_display_name' => $soldier['members_display_name'],
				'date' => strtotime('now'),
				'description' => sprintf('%s was discharged with grade: %s', $soldier['members_display_name'], $this->request['grade']),
				'type' => 'Discharge',
				'administrator_member_id' => $this->memberData['member_id'],
				'administrator_members_display_name' => $this->memberData['members_display_name'],
				'status' => '---',
				'relational_primary_id_field' => NULL ) );

			// Add service record
			$this->DB->insert( $this->settings['perscom_database_records'], array( 
				'member_id' => $soldier['member_id'], 
				'members_display_name' => $soldier['members_display_name'],
				'date' => $date->getTimestamp(),
				'entry' => $this->request['grade'],
				'type' => 'Discharge',
				'award' => '',
				'rank' => '',
				'discharge_grade' => $this->request['grade'],
				'display' => $this->request['service_record'],
		       	'position' => '',
				'combat_unit' => '' ) );

			// Update soldiers avatar
			$this->DB->update( 'profile_portal', array ( 
				'pp_main_photo' => '', 
				'pp_main_width' => '0', 
				'pp_main_height' => '0', 
				'pp_thumb_photo' => '', 
				'pp_thumb_width' => '0', 
				'pp_thumb_height' => '0', 
				'signature' => '' ), 'pp_member_id="'.$soldier['member_id'].'"' );

			// Update soldiers new name
			IPSMember::save( $soldier['member_id'], array( 'core' => array( 
				'members_display_name' => $this->request['new_name'], 
				'name' => $this->request['new_name'], 
				'title' => 'Civilian', 
				'member_group_id' => $this->settings['perscom_civilian_usergroup'], 
				'mgroup_others' => '' ) ) );

			// If send the notification
			if ($this->request['opord'] == 'Yes') {

				// Send the notification
				$this->notifications->sendNotification( array (
					'to' => $soldier, 
					'from' => $this->settings['perscom_application_submission_author'], 
					'title' => 'PERSCOM: Discharge', 
					'text' => sprintf( $this->lang->words['notification_discharge'], $this->request['grade'], $this->request['new_name'] ) ) );
			}
		}

		// Delete all the soldier's LOA's
		$this->DB->delete( $this->settings['perscom_database_loa'],
			'member_id="' . $soldier['member_id'] . '"' );

		// Delete all the soldier's TPR's
		$this->DB->delete( $this->settings['perscom_database_tpr'],
		       	'member_id="' . $soldier['member_id'] . '"' );
	}
}