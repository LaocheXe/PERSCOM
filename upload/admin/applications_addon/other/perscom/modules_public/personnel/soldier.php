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

class public_perscom_personnel_soldier extends ipsCommand
{
	private $allowEdit	= FALSE;
	private $soldier	= array();

	public function doExecute( ipsRegistry $registry )
	{
		// Load our language file
		$this->registry->class_localization->loadLanguageFile( array( 'public_personnel' ) );
		$this->registry->class_localization->loadLanguageFile( array( 'public_citations' ) );		
		$this->registry->class_localization->loadLanguageFile( array( 'public_base_camp' ) );

		// Set our navigation
		$this->registry->output->addNavigation( $this->lang->words['base_camp'], 'app=perscom' );
       	$this->registry->output->addNavigation( $this->lang->words['personnel_files'], 'app=perscom&module=personnel' );		

		// Check to make sure we have an id
		if ($this->request['id'] == '' || !isset($this->request['id'])) {

			// Show error
			$this->registry->output->showError( 'No soldier specified.', 12345678, false, '', 404 );
		}

		// Check if user is in admin group or BMO group to edit user.
		if ( IPSMember::isInGroup( $this->memberData, $this->settings['perscom_administrative_usergroup'] , true ) ) {
			$this->allowEdit = TRUE;
		}
		else if ( IPSMember::isInGroup( $this->memberData, $this->settings['perscom_bmo_usergroup'] , true ) ) {
			$this->allowEdit = TRUE;			
		}

		// Get perscom classes
		$this->combat_units = $this->registry->combat_units;
		$this->admin_units = $this->registry->admin_units;
		$this->ranks = $this->registry->ranks;
		$this->weapons = $this->registry->weapons;
		$this->status = $this->registry->status;
		$this->personnel = $this->registry->personnel;

		// Handle HTTP Request
		switch ($this->request['do']) 
		{
			case 'submit':
				$this->editSoldier();
			break;

			case 'delete':
				$this->deleteSoldier();
			break;

			default:

			break;
		}
		
		// Fire off all the information getters
		$this->getSoldierInformation();
		$this->getSoldierRecords();
		$this->getSigningSoldier();
		$this->getSupervisor();
		$this->getForumData();

		// Set HTML settings
        $this->registry->output->addContent( $this->registry->output->getTemplate('perscom')->viewSoldier( $this->soldier, $this->allowEdit, $this->combat_units->loadCombatUnits(), $this->ranks->loadRanks(), $this->weapons->loadWeapons(), $this->admin_units->loadAdminUnits(), $this->status->loadStatus(), $this->error, $this->personnel->loadPersonnel() ) );
       	$this->registry->output->sendOutput();
	}

	public function getSoldierInformation() {

		// Get the soldier information
		$this->soldier = $this->personnel->loadSoldierInformation( $this->request['id'] );
		
		// Set the nav and title with the soldier's name
		$this->registry->output->setTitle( sprintf('%s %s %s', $this->soldier['rank_short'], $this->soldier['firstname'], $this->soldier['lastname']) );
       	$this->registry->output->addNavigation( sprintf('%s %s %s', $this->soldier['rank_short'], $this->soldier['firstname'], $this->soldier['lastname']), NULL );
	}

	public function getSoldierRecords() {

		// Load all our soldier records
		$service_record = $this->personnel->loadServiceRecord( $this->request['id'] );
		$award_record = $this->personnel->loadAwardRecord( $this->request['id'] );
		$combat_record = $this->personnel->loadCombatRecord( $this->request['id'] );

		// Set the soldier array data
		$this->soldier['service_record_entries'] = $service_record;
		$this->soldier['award_record_entries'] = $award_record;
		$this->soldier['combat_record_entries'] = $combat_record;
	}

	public function getSigningSoldier() {

		// Get the signing soldier
		$this->soldier['perscom_signing_soldier'] = $this->personnel->loadCitationSigningSoldier();
	}

	public function getSupervisor() {

		// Get the signing soldier
		$this->soldier['soldier_supervisor'] = $this->personnel->loadSupervisor($this->soldier['supervisor']);
	}

	public function getForumData() {
		
		// Get the signing soldier
		$forum_data = $this->personnel->loadSoldierForumData($this->request['id']);
		$this->soldier['forum_name'] = $forum_data['forum_name'];
		$this->soldier['title'] = $forum_data['title'];
	}

	public function editSoldier() {

		// Load the member
		$member = IPSMember::load( $this->request['member_id'] );

		// If we don't get a member...
		if (!$member) {

			// Throw an error
			$this->registry->output->showError( 'Unable to load member specified.', 12345678, false, '', 404 );
		}

		// Get dmos array
		if (is_array($this->request['dmos'])) {

			// Set up our arrays
			$dmos_forum_ids = array();

			// Load dmos from the database
			$admin_units = $this->admin_units->loadAdminUnits();

			// Loop through the dmos
			foreach ($admin_units as $dmos) {

				// If the primary id filepro_fieldcount(oid)
				if (in_array($dmos['primary_id_field'], $this->request['dmos'])) {

					// Add the forum id of that dmos to the forum ids array
					array_push($dmos_forum_ids, $dmos['forum_usergroup']);
				}
			}

			// Set dmos var
			$dmos_ids = implode(',', $this->request['dmos']);
		}
		else {

			// No dmos to set so make it empty
			$dmos_ids = '';
		}

		// Create our dates
		$enlistment_date = DateTime::createFromFormat('m-d-Y',  $this->request['enlistment']);
		$induction_date = DateTime::createFromFormat('m-d-Y',  $this->request['induction']);
		$promotion_date = DateTime::createFromFormat('m-d-Y',  $this->request['promotion']);

		// Update personnel file
		$this->DB->update( $this->settings['perscom_database_personnel_files'], array ( 
			'status' => $this->request['status'], 
			'weapon' => $this->request['weapon'], 
			'mos' => $this->request['mos'], 
			'combat_unit' => $this->request['combat_unit'],
			'supervisor' => $this->request['supervisor'],
			'recruiter' => $this->request['recruiter'],
			'admin_unit' => $dmos_ids,
			'email' => $this->request['email'], 
			'steam' => $this->request['steam'], 
			'timezone' => $this->request['timezone'], 
			'country' => $this->request['country'],
			'position' => $this->request['position'],
			'firstname' => $this->request['first'],
			'lastname' => $this->request['last'],
			'enlistment_date' => $enlistment_date->getTimestamp(),
			'induction_date' => $induction_date->getTimestamp(),
			'promotion_date' => $promotion_date->getTimestamp(),
	       	'member_id' => $member['member_id'] ), 'member_id="' . $this->request['member_id'] . '"' );

		// Update profile picture
		$this->DB->update( 'profile_portal', array ( 
			'pp_main_photo' => 'perscom/insignia/large/multicam_background/'.$this->request['avatar'].'.jpg', 
			'pp_main_width' => '100', 
			'pp_main_height' => '100', 
			'pp_thumb_photo' => 'perscom/insignia/large/multicam_background/'.$this->request['avatar'].'.jpg', 
			'pp_thumb_width' => '100', 
			'pp_thumb_height' => '100' ), 'pp_member_id="' . $member['member_id'] . '"' );

		// Load the users current mgoups
		$current_mgroup_others_array = array_filter(explode(',', $member['mgroup_others']));

		// Get all the possible forum usergroups
		$admin_units_forum_usergroups = $this->admin_units->loadAdminUnits($column = 'forum_usergroup');

		// Remove all of them from the current mgroups so we can start fresh
		$new_mgroup_others_array = array_diff($current_mgroup_others_array, $admin_units_forum_usergroups);

		// If we have dmos forum usergroup ids to add to the users mgroups
		if (count($dmos_forum_ids) > 0) {

			// Loop through the new mgroups and make sure they are added to the existing
			foreach ($dmos_forum_ids as $id) {
				
				// If the current mgroups doesnt have the id...
				if (!in_array($id, $mgroup_others_array)) {
					
					// Add it to the array
					array_push($new_mgroup_others_array, $id);
				}
			}		
		}

		// Update the members name and title as well
		IPSMember::save( $member['member_id'], array( 'core' => array( 
			'members_display_name' => $this->request['forum_name'], 
			'mgroup_others' => implode(',', $new_mgroup_others_array),
			'name' => $this->request['forum_name'], 
			'title' => $this->request['title'] ) ) );
	}

	public function deleteSoldier() {

		// Delete the personnel file
		$this->DB->delete( $this->settings['perscom_database_personnel_files'], 
			'member_id="' . $this->request['id'] . '"' );
	}
}