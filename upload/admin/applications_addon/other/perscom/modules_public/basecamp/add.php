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

class public_perscom_basecamp_add extends ipsCommand
{
	public function doExecute( ipsRegistry $registry )
	{
		// Load our language file
		$this->registry->class_localization->loadLanguageFile( array( 'public_base_camp' ) );
		$this->registry->class_localization->loadLanguageFile( array( 'public_enlistment' ) );

		// Set navigation
		$this->registry->output->addNavigation( $this->lang->words['base_camp'], 'app=perscom' );		
       	$this->registry->output->addNavigation( $this->lang->words['add_soldier'], NULL );

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
		$this->personnel = $this->registry->personnel;

		// Handle HTTP Request
		switch ($this->request['do']) 
		{
			case 'submit':
				$this->addSoldier();
			break;

			default:

			break;
		}

		// Set HTML settings
		$this->registry->output->setTitle( $this->lang->words['add_soldier'] );
        $this->registry->output->addContent( $this->registry->output->getTemplate('perscom')->addSoldier( $this->ranks->loadRanks(), $this->combat_units->loadCombatUnits(), $this->weapons->loadWeapons(), $this->personnel->loadPersonnel() ) );
       	$this->registry->output->sendOutput();
	}

	public function addSoldier() {

		// Load member we are adding to the perscom system
		$member = IPSMember::load( $this->request['forum_name'], 'all', 'displayname');

		// If we dont load a member, display an error
		if (!$member) {

			// Throw error
			$this->registry->output->showError( sprintf('Unable to find the username %s', $this->request['forum_name']), 000123, false, '', 400 );
		}

		// Check if we already have a personnel file for that soldier
		$personnel_file = $this->DB->buildAndFetch( array( 'select' => '*',
			'from' => $this->settings['perscom_database_personnel_files'],
			'where' => 'member_id="' . $member['member_id'] . '"') );

		// If we get a result back
		if ($personnel_file) {

			// Throw error
			$this->registry->output->showError( 'There is a soldier with that member ID/forum name already in the system.', 000123, false, '', 400 );
		}

		// If we are bypassing
		if ($this->request['bypass'] == 'Yes') {

			// Set bool
			$bypass = TRUE;

			// Create our dates
			$enlistment_date = DateTime::createFromFormat('m-d-Y',  $this->request['enlistment_date']);
			$promotion_date = DateTime::createFromFormat('m-d-Y',  $this->request['promotion_date']);
			$induction_date = DateTime::createFromFormat('m-d-Y',  $this->request['induction_date']);			
		}

		// Create personnel file
		// Build query
		$this->DB->insert( $this->settings['perscom_database_personnel_files'], array( 
			'member_id' => $member['member_id'], 
			'firstname' => ucfirst($this->request['first_name']),
			'lastname' => ucfirst($this->request['last_name']),
			'rank' => $bypass == TRUE ? $this->request['rank'] : '128',
			'position' => $bypass == TRUE ? $this->request['position'] : 'New Applicant',
			'supervisor' => $bypass == TRUE ? $this->request['supervisor'] : '0',
			'recruiter' => $bypass == TRUE ? $this->request['recruiter'] : '0',
			'mos' => $bypass == TRUE ? $this->request['mos'] : '',
			'admin_unit' => '',
			'enlistment_date' => $bypass == TRUE ? $enlistment_date->getTimestamp() : strtotime('now'),
			'timezone' => $this->request['timezone'],
			'combat_unit' => $bypass == TRUE ? $this->request['unit'] : '624',
			'weapon' => $bypass == TRUE ? $this->request['weapon'] : '3',
			'steam' => $this->request['steam_id'],
			'country' => $this->request['country'],
			'email' => $member['email'],
			'status' => $bypass == TRUE ? '1' : '5',
			'promotion_date' => $bypass == TRUE ? $promotion_date->getTimestamp() : '',
			'induction_date' => $bypass == TRUE ? $induction_date->getTimestamp() : '' ) );

		// If we are bypassing
		if ($bypass) {

			// Query the DB to get the application that we are dealing with
			$rank = $this->DB->buildAndFetch( array( 'select' => '*', 
				'from' => $this->settings['perscom_database_ranks'], 
				'where' => 'primary_id_field="' . $this->request['rank'] .'"' ) );

			// Create our formatted display name to save to the member DB
			$name = sprintf('%s %s.%s', $rank['abbreviation'], strtoupper(substr($this->request['first_name'], 0, 1)), ucfirst(strtolower($this->request['last_name'])));

			// Get the combat unit we are adding the soldier to so we can get the associated user group
			$unit = $this->DB->buildAndFetch( array( 'select' => '*',
				'from' => $this->settings['perscom_database_units'],
				'where' => 'primary_id_field="' . $this->request['unit'] . '"' ) );

			// Determine what are secondary usergroup should be
			if ($this->request['usergroup'] == $this->settings['perscom_administrative_usergroup']) {

				// Set the primary usergroup as the administrative usergroup
				$primary_usergroup = $this->settings['perscom_administrative_usergroup'];
			}

			// The primary usergroup is not the administrative group
			else {

				// Set the primary group as the base unit usergroup
				$primary_usergroup = $this->settings['perscom_base_unit_usergroup'];
			}

			// Make sure the secondary group is not the same as the primary group
			if ($unit['forum_usergroup'] != $primary_usergroup) {
				
				// The second usergroups should be nothing
				$secondary_usergroup = $unit['forum_usergroup'];	
			}

			// Update the user in IPB
			IPSMember::save( $member['member_id'], array( 'core' => array( 
				'members_display_name' => $name, 
				'name' => $name,
				'members_seo_name' => strtolower(str_replace('.', '', $name)),
				'title' => $rank['title'],
				'member_group_id' => $primary_usergroup,
				'mgroup_others' => $secondary_usergroup ) ) );

			// Update soldiers avatar
			$this->DB->update( 'profile_portal', array ( 
				'pp_main_photo' => 'perscom/insignia/large/multicam_background/'.$rank['abbreviation'].'.jpg', 
				'pp_main_width' => '100', 
				'pp_main_height' => '100', 
				'pp_thumb_photo' => 'perscom/insignia/large/multicam_background/'.$rank['abbreviation'].'.jpg', 
				'pp_thumb_width' => '100', 
				'pp_thumb_height' => '100' ), 'pp_member_id="'.$member['member_id'].'"' );
		}

		// We are not bypassing, we are adding the soldier the the application processing stage
		else {

			// Get current mgroups
			$groups = explode(',', $member['mgroup_others']);

			// Add the enlistees group id onto the end
			array_push($groups, $this->settings['perscom_application_submission_group']);

			// Save the new data
			IPSMember::save( $member['member_id'], array( 'core' => array( 
				'mgroup_others' =>  implode(',', $groups) ) ) );

			// Update the total applications
			$this->DB->update( $this->settings['perscom_database_settings'], '`value`=`value`+1', '`key`="total_applications"', false, true );
		}

		// Add to log
		$this->DB->insert( $this->settings['perscom_database_requests'], array( 
			'member_id' => $this->memberData['member_id'], 
			'members_display_name' => sprintf('%s %s', $this->request['first_name'], $this->request['last_name']),
			'date' => strtotime('now'),
			'description' => sprintf('%s %s was manually added to the PERSCOM system.', $this->request['first_name'], $this->request['last_name'] ),
			'type' => 'Add Soldier',
			'administrator_member_id' => $this->memberData['member_id'],
			'administrator_members_display_name' => $this->memberData['members_display_name'],
			'status' => '---',
			'relational_primary_id_field' => NULL ) );
	}
}