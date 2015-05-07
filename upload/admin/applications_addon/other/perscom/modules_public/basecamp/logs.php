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

class public_perscom_basecamp_logs extends ipsCommand
{
	public function doExecute( ipsRegistry $registry )
	{
		// Load our language file
		$this->registry->class_localization->loadLanguageFile( array( 'public_base_camp' ) );

		// Set navigation
		$this->registry->output->addNavigation( $this->lang->words['base_camp'], 'app=perscom' );
		$this->registry->output->addNavigation( $this->lang->words['operations_center'], 'app=perscom&module=basecamp&section=information' );		
       	$this->registry->output->addNavigation( $this->lang->words['view_logs'], NULL );

		// Check to make sure member is in admin group
		if ( !IPSMember::isInGroup( $this->memberData, $this->settings['perscom_administrative_usergroup'] , true) ) {
			
			// Throw login error
			$this->registry->output->showError( 'You do not have permission to view this module.', 000123, false, '', 403 );
		}

		// Handle HTTP Request
		switch ($this->request['do']) 
		{
			case 'empty':
				$this->emptyLogs();
			break;

			default:

			break;
		}

		// Set HTML settings
		$this->registry->output->setTitle( $this->lang->words['view_logs'] );
        $this->registry->output->addContent( $this->registry->output->getTemplate('perscom')->viewLogs() );
       	$this->registry->output->sendOutput();
	}

	public function emptyLogs() {

		// Query the DB to look for all TPR's
		$this->DB->delete( $this->settings['perscom_database_requests'], 'status!="Pending"' );

		// Build query
		$this->DB->insert( $this->settings['perscom_database_requests'], array( 
			'member_id' => $this->memberData['member_id'], 
			'members_display_name' => $this->memberData['members_display_name'],
			'date' => strtotime('now'),
			'description' => sprintf('%s Emptied the Admin Logs', $this->memberData['members_display_name']),
			'type' => 'Admin',
			'administrator_member_id' => $this->memberData['member_id'],
			'administrator_members_display_name' => $this->memberData['members_display_name'],
			'status' => '---',
	       	'relational_primary_id_field' => NULL ) );
	}
}