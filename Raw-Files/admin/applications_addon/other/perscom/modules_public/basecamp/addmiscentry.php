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

class public_perscom_basecamp_addmiscentry extends ipsCommand
{
	public function doExecute( ipsRegistry $registry )
	{
		// Load our language file
		$this->registry->class_localization->loadLanguageFile( array( 'public_base_camp' ) );

		// Set navigation
		$this->registry->output->addNavigation( $this->lang->words['base_camp'], 'app=perscom' );		
       	$this->registry->output->addNavigation( $this->lang->words['add_service_record_entry'], NULL );

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

		// Handle HTTP Request
		switch ($this->request['do']) 
		{
			case 'submit':
				$this->submitServiceRecord();
			break;

			default:

			break;
		}

		// Output HTML
		$this->registry->output->setTitle( $this->lang->words['add_service_record_entry'] );
        $this->registry->output->addContent( $this->registry->output->getTemplate('perscom')->addMiscEntry( $this->personnel->loadPersonnel() ) );
       	$this->registry->output->sendOutput();
	}

	public function submitServiceRecord() {

		// Get the soldier who is being dealt with
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
			'description' => sprintf('Service Record Update: %s', $this->request['service_record_entry']),
			'type' => 'Service Record Entry',
			'administrator_member_id' => $this->memberData['member_id'],
			'administrator_members_display_name' => $this->memberData['members_display_name'],
			'status' => '---',
			'relational_primary_id_field' => NULL ) );

		// Add service record
		$this->DB->insert( $this->settings['perscom_database_records'], array( 
			'member_id' => $soldier['member_id'], 
			'members_display_name' => $soldier['members_display_name'],
			'date' => $date->getTimestamp(),
			'entry' => $this->request['service_record_entry'],
			'type' => 'Service Record Entry',
			'award' => '',
			'rank' => '',
			'discharge_grade' => '',
			'display' => 'Yes',
	       	'position' => '',
			'combat_unit' => '' ) );
	}
}