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

class public_perscom_basecamp_applications extends ipsCommand
{
	public function doExecute( ipsRegistry $registry )
	{
		// Load our language files
		$this->registry->class_localization->loadLanguageFile( array( 'public_base_camp' ) );
		$this->registry->class_localization->loadLanguageFile( array( 'public_notifications' ) );

		// Set navigation
		$this->registry->output->addNavigation( $this->lang->words['base_camp'], 'app=perscom' );		
       	$this->registry->output->addNavigation( $this->lang->words['enlistment_applications'], NULL );

		// Check to make sure member is in admin group
		if ( !IPSMember::isInGroup( $this->memberData, $this->settings['perscom_administrative_usergroup'] , true) ) {
			
			// If they arent in the admin group but are in the BMO group
			if ( !IPSMember::isInGroup( $this->memberData, $this->settings['perscom_recruiting_and_retention_group'] , true) ) {
			
				// Throw login error
				$this->registry->output->showError( 'You do not have permission to view this module.', 000123, false, '', 403 );
			}
		}

		// Get PERSCOM classes
		$this->notifications = $this->registry->notifications;
		$this->enlistment_applications = $this->registry->enlistment_applications;
		$this->statistics = $this->registry->statistics;

		// Handle HTTP Request
		switch ($this->request['do']) 
		{
			case 'deny':
				$this->denyApplication();
			break;

			case 'drop':
				$this->dropApplication();
			break;

			case 'reset':
				$this->statistics->resetEnlistmentApplicationStatistics();
			break;

			default:

			break;
		}

		// Set HTML settings
		$this->registry->output->setTitle( $this->lang->words['enlistment_applications'] );
        $this->registry->output->addContent( $this->registry->output->getTemplate('perscom')->viewApplications( $this->enlistment_applications->loadEnlistmentApplications(), $this->statistics->loadEnlistmentApplicationStatistics() ) );
       	$this->registry->output->sendOutput();
	}

	public function denyApplication() {

		// Get our application that we are modifying so we can edit the user details
		$application = $this->DB->buildAndFetch( array( 'select' => '*', 
			'from' => $this->settings['perscom_database_personnel_files'],
			'where' => 'primary_id_field="' . $this->request['id'] . '"' ) );

		// If we get an application
		if ($application) {
		
			// Add to log
			$this->DB->insert( $this->settings['perscom_database_requests'], array( 
				'member_id' => $application['member_id'], 
				'members_display_name' => sprintf('%s %s', $application['firstname'], $application['lastname']),
				'date' => strtotime('now'),
				'description' => sprintf('%s %s\'s Enlistment Application was Denied', $application['firstname'], $application['lastname']),
				'type' => 'Enlistment Application',
				'administrator_member_id' => $this->memberData['member_id'],
				'administrator_members_display_name' => $this->memberData['members_display_name'],
				'status' => 'Denied',
				'relational_primary_id_field' => NULL ) );

			// Update our member and make sure they are still in the civilian group with no secondary groups
			IPSMember::save( $application['member_id'], array( 'core' => array( 
				'title' => 'Civilian',
				'member_group_id' => $this->settings['perscom_civilian_usergroup'],
				'mgroup_others' => '' ) ) );

			// Send the notification
			$this->notifications->sendNotification( array (
				'to' => $application['member_id'], 
				'from' => $this->settings['perscom_application_submission_author'], 
				'title' => 'PERSCOM: Enlistment Application Denied', 
				'text' => $this->lang->words['notification_application_denied'] ) );

			// Delete the application
			$this->DB->delete( $this->settings['perscom_database_personnel_files'], 
				'primary_id_field="' . $this->request['id'] . '"');

			// Update the denied applications
			$this->DB->update( $this->settings['perscom_database_settings'], '`value`=`value`+1', '`key`="denied_applications"', false, true );	
		}
	}

	public function dropApplication() {

		// Get our application that we are modifying so we can edit the user details
		$application = $this->DB->buildAndFetch( array( 'select' => '*', 
			'from' => $this->settings['perscom_database_personnel_files'], 
			'where' => 'primary_id_field="' . $this->request['id'] . '"' ) );

		// If we get an application
		if ($application) {
		
			// Add to log
			$this->DB->insert( $this->settings['perscom_database_requests'], array( 
				'member_id' => $application['member_id'], 
				'members_display_name' => sprintf('%s %s', $application['firstname'], $application['lastname']),
				'date' => strtotime('now'),
				'description' => sprintf('%s %s\'s Enlistment Application was Dropped', $application['firstname'], $application['lastname']),
				'type' => 'Enlistment Application',
				'administrator_member_id' => $this->memberData['member_id'],
				'administrator_members_display_name' => $this->memberData['members_display_name'],
				'status' => 'Dropped',
				'relational_primary_id_field' => NULL ) );

			// Update our member and make sure they are still in the civilian group with no secondary groups
			IPSMember::save( $application['member_id'], array( 'core' => array( 
				'title' => 'Civilian',
				'member_group_id' => $this->settings['perscom_civilian_usergroup'],
				'mgroup_others' => '' ) ) );

			// Send the notification
			$this->notifications->sendNotification( array (
				'to' => $application['member_id'], 
				'from' => $this->settings['perscom_application_submission_author'], 
				'title' => 'PERSCOM: Enlistment Application Dropped', 
				'text' => $this->lang->words['notification_application_dropped'] ) );

			// Delete the application
			$this->DB->delete( $this->settings['perscom_database_personnel_files'], 
				'primary_id_field="' . $this->request['id'] . '"');

			// Update dropped applications
			$this->DB->update( $this->settings['perscom_database_settings'], '`value`=`value`+1', '`key`="dropped_applications"', false, true );	
		}
	}
}