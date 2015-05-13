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
	// Array that holds all the enlistment application statistics
	private $stats = array();

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
				$this->resetEnlistmentApplicationStatistics();
			break;

			default:

			break;
		}

		// Get the enlistment application statistics
		$this->getEnlistmentApplicationStatistics();

		// Set HTML settings
		$this->registry->output->setTitle( $this->lang->words['enlistment_applications'] );
        $this->registry->output->addContent( $this->registry->output->getTemplate('perscom')->viewApplications( $this->enlistment_applications->loadEnlistmentApplications(), $this->stats ) );
       	$this->registry->output->sendOutput();
	}

	public function getEnlistmentApplicationStatistics() {

		// Get the number of total applications
		$total_applications = $this->DB->buildAndFetch( array( 'select' => '*', 
			'from' => $this->settings['perscom_database_settings'], 
			'where' => '`key`="total_applications"' ) );

		// Get the number of accepted applications
		$accepted_applications = $this->DB->buildAndFetch( array( 'select' => '*', 
			'from' => $this->settings['perscom_database_settings'], 
			'where' => '`key`="accepted_applications"' ) );

		// Get the number of accepted applications
		$denied_applications = $this->DB->buildAndFetch( array( 'select' => '*', 
			'from' => $this->settings['perscom_database_settings'], 
			'where' => '`key`="denied_applications"' ) );

		// Get the number of accepted applications
		$dropped_applications = $this->DB->buildAndFetch( array( 'select' => '*', 
			'from' => $this->settings['perscom_database_settings'], 
			'where' => '`key`="dropped_applications"' ) );

		// Get all the recruiters
		$this->DB->build( array( 'select' => 'recruiter',
			'from' => $this->settings['perscom_database_personnel_files'] ) );

		// Execute the DB query
		$recruiters_result = $this->DB->execute();

		// Create an array to store all the recruiters
		$recruiters = array();

		// Loop through the results and add to array
		while( $r = $this->DB->fetch( $recruiters_result ) )
		{
			// If not a 0
			if ($r['recruiter'] != '0') {
				
				// Add the result to the recruiters array
				array_push($recruiters, $r['recruiter']);
			}
		}

		// Count the array and look for the most popular result
		$count = array_count_values($recruiters); 
		$recruiter = array_search(max($count), $count);

		// If we get a result
		if ($recruiter) {
			
			// Get the member
			$recruiter_pfile = IPSMember::load( $recruiter );

			// If we get a member profile
			if ($recruiter_pfile) {
				
				// Add the statistic
				$this->stats['most_active_recruiter'] = $recruiter_pfile['members_display_name'] . ' (' . $count[$recruiter] . ' Recruits)';
			}

			// Unable to load member
			else {

				// Inform the user
				$this->stats['most_active_recruiter'] = 'Unable to load member';
			}
		}

		// Get the latest reset date of the enlistment statistics
		$enlistment_statistics_reset = $this->DB->buildAndFetch( array( 'select' => '*', 
			'from' => $this->settings['perscom_database_settings'], 
			'where' => '`key`="enlistment_statistics_reset"' ) );

		// Set our stats array
		$this->stats['total_applications'] = $total_applications['value'];
		$this->stats['applications_accepted'] = $accepted_applications['value'];
		$this->stats['denied_applications'] = $denied_applications['value'];
		$this->stats['dropped_applications'] = $dropped_applications['value'];
		$this->stats['enlistment_statistics_reset'] = $enlistment_statistics_reset['value'];

		// Query the DB to look for all accepted enlisment applications from today's year
		$this->DB->build( array( 'select' => '*', 
			'from' => $this->settings['perscom_database_personnel_files'], 
			'where' => 'FROM_UNIXTIME(enlistment_date, "%Y")=YEAR(CURDATE()) AND status != "8" AND status != "7" AND status !="5"' ) );

		// Execute the DB query
		$result = $this->DB->execute();

		// Create an array to store all the processing days values
		$days = array();

		// Loop through the accepted applications and find the difference between the date applied and date enlisted
		while( $r = $this->DB->fetch( $result ) )
		{
			// Calculate the difference in time between the induction date and enlistment date and add the number of days to the array
			$datediff = $r['enlistment_date'] - $r['induction_date'];
			array_push($days, abs(floor($datediff/(60*60*24))));
		}

		// Set the average processing day value
		$this->stats['application_processing_time'] = count($days) > 0 ? round(array_sum($days) / count($days), 3) : 0;
	}

	public function resetEnlistmentApplicationStatistics() {

		// Update the last reset statistics date
		$this->DB->update( $this->settings['perscom_database_settings'], array( '`value`' => strtotime('now') ), '`key`="enlistment_statistics_reset"' );

		// Update the total applications
		$this->DB->update( $this->settings['perscom_database_settings'], array( '`value`' => '0' ), '`key`="total_applications"' );

		// Update the accepted applications
		$this->DB->update( $this->settings['perscom_database_settings'], array( '`value`' => '0' ), '`key`="accepted_applications"' );

		// Update the denied applications
		$this->DB->update( $this->settings['perscom_database_settings'], array( '`value`' => '0' ), '`key`="denied_applications"' );

		// Update dropped applications
		$this->DB->update( $this->settings['perscom_database_settings'], array( '`value`' => '0' ), '`key`="dropped_applications"' );
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