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

class public_perscom_basecamp_assign extends ipsCommand
{
	public function doExecute( ipsRegistry $registry )
	{
		// Load our language files
		$this->registry->class_localization->loadLanguageFile( array( 'public_base_camp' ) );
		$this->registry->class_localization->loadLanguageFile( array( 'public_notifications' ) );

		// Set navigation
		$this->registry->output->addNavigation( $this->lang->words['base_camp'], 'app=perscom' );		
       	$this->registry->output->addNavigation( $this->lang->words['add_file_assignment_entry'], NULL );

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
		$this->combat_units = $this->registry->combat_units;
		$this->notifications = $this->registry->notifications;

		// Handle HTTP Request
		switch ($this->request['do']) 
		{
			case 'submit':
				$this->submitAssignment();
			break;

			default:

			break;
		}

		// Output HTML
		$this->registry->output->setTitle( $this->lang->words['add_file_assignment_entry'] );
        $this->registry->output->addContent( $this->registry->output->getTemplate('perscom')->fileAssignment( $this->personnel->loadPersonnel(), $this->combat_units->loadCombatUnits() ) );
       	$this->registry->output->sendOutput();
	}

	public function submitAssignment() {

		// Get the soldier who is being dealt with
		$soldier = IPSMember::load( $this->request['soldier'] );

		// If we did not load a solider
		if (!$soldier) {
	
			// Throw an error
			$this->registry->output->showError( 'Unable to load specified soldier.', 12345678, false, '', 404 );
		}

		// Load the combat unit that is being selected
		$combat_unit = $this->DB->buildAndFetch( array ( 'select' => '*', 
			'from' => $this->settings['perscom_database_units'], 
			'where' => 'primary_id_field="' . $this->request['unit'] . '"' ) );

		// Create our date
		$date = DateTime::createFromFormat('m-d-Y', $this->request['date']);
	
		// Add to log
		$this->DB->insert( $this->settings['perscom_database_requests'], array( 
			'member_id' => $soldier['member_id'], 
			'members_display_name' => $soldier['members_display_name'],
			'date' => strtotime('now'),
			'description' => sprintf('%s: %s, %s - Forum Group ID: %s', $this->request['assignment'], $this->request['position'], $combat_unit['name'], $combat_unit['forum_usergroup']),
			'type' => 'Assignment',
			'administrator_member_id' => $this->memberData['member_id'],
			'administrator_members_display_name' => $this->memberData['members_display_name'],
			'status' => '---',
			'relational_primary_id_field' => NULL ) );

		// Add service record
		$this->DB->insert( $this->settings['perscom_database_records'], array( 
			'member_id' => $soldier['member_id'], 
			'members_display_name' => $soldier['members_display_name'],
			'date' => $date->getTimestamp(),
			'entry' => sprintf('%s: %s, %s', $this->request['assignment'], $this->request['position'], $combat_unit['name']),
			'type' => 'Assignment',
			'award' => '',
			'rank' => '',
			'discharge_grade' => '',
			'display' => $this->request['service_record'],
			'position' => $this->request['position'],
			'combat_unit' => $combat_unit['name'] ) );

		// Update personnel file
		$this->DB->update( $this->settings['perscom_database_personnel_files'], array ( 
			'combat_unit' => $combat_unit['primary_id_field'],
			'supervisor' => $this->request['supervisor'], 
			'position' => $this->request['position'] ), 'member_id="' . $soldier['member_id'] . '"' );

		// If we are assigning the user cstaff permissions
		if ($this->request['permissions'] == 'Yes') {

			// Set the primary group id
			$primary_group_id = $this->settings['perscom_administrative_usergroup'];
		}
		else {
			// Set the primary group id
			$primary_group_id = $this->settings['perscom_base_unit_usergroup'];
		}

		// Load the users current mgoups
		$current_mgroup_others_array = array_filter(explode(',', $soldier['mgroup_others']));

		// Make sure base usergroup and admin usergroup are not in the secondary groups
		if (in_array($this->settings['perscom_base_unit_usergroup'], $current_mgroup_others_array)) {

			// Get the position of the element in the array
			$key = array_search($this->settings['perscom_base_unit_usergroup'], $current_mgroup_others_array);

			// Remove it
			unset($current_mgroup_others_array[$key]);
		}
		if (in_array($this->settings['perscom_administrative_usergroup'], $current_mgroup_others_array)) {

			// Get the position of the element in the array
			$key = array_search($this->settings['perscom_administrative_usergroup'], $current_mgroup_others_array);

			// Remove it
			unset($current_mgroup_others_array[$key]);
		}

		// Get all the possible forum usergroups
		$combat_units_forum_usergroups = $this->combat_units->loadCombatUnits($column = 'forum_usergroup');

		// Remove all of them from the current mgroups so we can start fresh
		$new_mgroup_others_array = array_diff($current_mgroup_others_array, $combat_units_forum_usergroups);

		// Add the forum id of the user group to the secondary groups
		array_push($new_mgroup_others_array, $combat_unit['forum_usergroup']);

		// Set the new member group ID
		IPSMember::save( $soldier['member_id'], array( 'core' => array( 
			'member_group_id' => $primary_group_id, 
			'mgroup_others' => implode(',', $new_mgroup_others_array) ) ) );

		// If send the notification
		if ($this->request['opord'] == 'Yes') {

			// Send the notification
			$this->notifications->sendNotification( array (
				'to' => $soldier, 
				'from' => $this->settings['perscom_application_submission_author'], 
				'title' => 'PERSCOM: New Assignment', 
				'text' => sprintf( $this->lang->words['notification_assignment'], $this->request['assignment'] == 'Assignment' ? 'assigned' : 're-assigned', $combat_unit['name'], $this->request['position'] ) ) );
		}
	}
}