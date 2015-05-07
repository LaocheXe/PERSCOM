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

class public_perscom_ajax_datatables extends ipsAjaxCommand
{
	public function doExecute( ipsRegistry $registry )
	{	
		// Handle HTTP Request
		switch ($this->request['record']) 
		{
			case 'service':

				// Return the JSON
				$this->returnJsonArray( $registry->personnel->loadServiceRecord( $this->request['id'] ) );
			
			break;

			case 'award':

				// Return the JSON
				$this->returnJsonArray( $registry->personnel->loadAwardRecord( $this->request['id'] ) );

			break;

			case 'combat':

				// Return the JSON
				$this->returnJsonArray( $registry->personnel->loadCombatRecord( $this->request['id'] ) );

			break;

			case 'logs':

				// Return the JSON
				$this->returnJsonArray( $registry->logs->loadLogs() );

			break;

			case 'requests':

				// Return the JSON
				$this->returnJsonArray( $registry->requests->loadRequests() );

			break;

			default:

			break;
		}

		// Handle do requests
		switch ($this->request['do']) {

			case 'approve':

				// Handle type requests
				switch ($this->request['type']) {

					case 'LOA':
						
						// Check to make sure it was not already processed
						$loa = $this->DB->buildAndFetch( array( 
							'select' => '*', 
							'from' => $this->settings['perscom_database_requests'], 
							'where' => 'primary_id_field="' . $this->request['id'] . '"' ));

						// If not already set
						if ($loa['status'] != 'Approved') {

							// Get the member the LOA is for
							$soldier = IPSMember::load( $loa['member_id'] );

							// If we did not load a solider
							if (!$soldier) {
			
								// Throw an error
								$this->registry->output->showError( 'Unable to load specified soldier.', 12345678, false, '', 404 );
							}

							// Update the LOA entry as Approved and set the approvers name
							$this->DB->update( $this->settings['perscom_database_loa'], array( 
								'status' => 'Approved', 
								'returned' => 'false',
								'approved_member_id' => $this->memberData['member_id'],
								'approved_display_name' => $this->memberData['members_display_name'],
								'approved_date' => strtotime('now') ), 'primary_id_field="' . $this->request['relational'] . '"' );

							// Update the request entry to Approved and set the approver's name
							$this->DB->update( $this->settings['perscom_database_requests'], array( 
								'status' => 'Approved', 
								'administrator_members_display_name' => $this->memberData['members_display_name'],
								'administrator_member_id' => $this->memberData['member_id'] ), 'primary_id_field="' . $this->request['id'] . '"' );

							// Update the personnel file by setting status to LOA and move them to the LOA combat unit
							$this->DB->update( $this->settings['perscom_database_personnel_files'], array( 
								'status' => '4', 
								'combat_unit' => '625' ), 'member_id="' . $soldier['member_id'] . '"' );
						
							// Get the LOA
							$loa = $this->DB->buildAndFetch( array( 
								'select' => '*', 
								'from' => $this->settings['perscom_database_loa'], 
								'where' => 'primary_id_field="' . $this->request['relational'] . '"' ) );
				
							// Add service record
							$this->DB->insert( $this->settings['perscom_database_records'], array( 
								'member_id' => $loa['member_id'], 
								'members_display_name' => $soldier['members_display_name'],
								'date' => strtotime('now'),
								'entry' => sprintf('Placed on Leave of Absence. Expected Return Date: %s', strftime($this->settings['clock_short2'], $loa['end_date'])),
								'type' => 'LOA',
								'award' => '',
								'rank' => '',
								'discharge_grade' => '',
								'display' => 'Yes',
			     			  	'position' => '',
								'combat_unit' => '' ) );

							// Send notification
							$registry->notifications->sendNotification( array (
								'to' => $soldier, 
								'from' => $this->settings['perscom_application_submission_author'], 
								'title' => 'PERSCOM: Leave of Absence Approved', 
								'text' => $this->lang->words['notification_loa_approved'] ) );
						}

					break;

					case 'Discharge':
					
						// Check to make sure it was not already denied
						$discharge = $this->DB->buildAndFetch( array( 
							'select' => '*', 
							'from' => $this->settings['perscom_database_requests'], 
							'where' => 'primary_id_field="' . $this->request['id'] . '"' ));

						// If not already set as Approved
						if ($discharge['status'] != 'Approved') {

							// Get the member the LOA is for
							$soldier = IPSMember::load( $discharge['member_id'] );

							// If we did not load a solider
							if (!$soldier) {
			
								// Throw an error
								$this->registry->output->showError( 'Unable to load specified soldier.', 12345678, false, '', 404 );
							}

							// Create the update query
							$this->DB->update( $this->settings['perscom_database_requests'], array( 
								'status' => 'Approved', 
								'administrator_members_display_name' => $this->memberData['members_display_name'],
								'administrator_member_id' => $this->memberData['member_id'] ), 'primary_id_field="' . $this->request['id'] . '"' );	

							// Send notification
							$registry->notifications->sendNotification( array (
								'to' => $soldier, 
								'from' => $this->settings['perscom_application_submission_author'], 
								'title' => 'PERSCOM: Discharge Approved', 
								'text' => $this->lang->words['notification_discharge_approved'] ) );
						}

					break;
				}

			break;

			case 'deny':
				
				// Handle type requests
				switch ($this->request['type']) {

					case 'LOA':

						// Check to make sure it was not already denied
						$loa = $this->DB->buildAndFetch( array( 
							'select' => '*', 
							'from' => $this->settings['perscom_database_requests'], 
							'where' => 'primary_id_field="' . $this->request['id'] . '"' ));

						// If not already set as Denied
						if ($loa['status'] != 'Denied') {

							// Get the member the LOA is for
							$soldier = IPSMember::load( $loa['member_id'] );

							// If we did not load a solider
							if (!$soldier) {
			
								// Throw an error
								$this->registry->output->showError( 'Unable to load specified soldier.', 12345678, false, '', 404 );
							}

							// Update the LOA entry
							$this->DB->update( $this->settings['perscom_database_loa'], array( 
								'status' => 'Denied', 
								'returned' => 'true',
								'approved_member_id' => $this->memberData['member_id'],
								'approved_display_name' => $this->memberData['members_display_name'],
								'approved_date' => strtotime('now') ), 'primary_id_field="' . $this->request['relational'] . '"' );

							// Update the request entry
							$this->DB->update( $this->settings['perscom_database_requests'], array( 
								'status' => 'Denied', 
								'administrator_members_display_name' => $this->memberData['members_display_name'],
								'administrator_member_id' => $this->memberData['member_id'] ), 'primary_id_field="' . $this->request['id'] . '"' );

							// Send notification
							$registry->notifications->sendNotification( array (
								'to' => $soldier, 
								'from' => $this->settings['perscom_application_submission_author'], 
								'title' => 'PERSCOM: Leave of Absence Denied', 
								'text' => $this->lang->words['notification_loa_denied'] ) );
						}
					
					break;

					case 'Discharge':	

						// Check to make sure it was not already denied
						$discharge = $this->DB->buildAndFetch( array( 
							'select' => '*', 
							'from' => $this->settings['perscom_database_requests'], 
							'where' => 'primary_id_field="' . $this->request['id'] . '"' ));

						// If not already set as Denied
						if ($discharge['status'] != 'Denied') {

							// Get the member the LOA is for
							$soldier = IPSMember::load( $discharge['member_id'] );

							// If we did not load a solider
							if (!$soldier) {
			
								// Throw an error
								$this->registry->output->showError( 'Unable to load specified soldier.', 12345678, false, '', 404 );
							}

							// Update the request entry
							$this->DB->update( $this->settings['perscom_database_requests'], array( 
								'status' => 'Denied', 
								'administrator_members_display_name' => $this->memberData['members_display_name'],
								'administrator_member_id' => $this->memberData['member_id'] ), 'primary_id_field="' . $this->request['id'] . '"' );

							// Send notification
							$registry->notifications->sendNotification( array (
								'to' => $soldier, 
								'from' => $this->settings['perscom_application_submission_author'], 
								'title' => 'PERSCOM: Discharge Denied', 
								'text' => $this->lang->words['notification_discharge_denied'] ) );
						}

					break;
				}

			break;

			case 'drop':

				// Handle type requests
				switch ($this->request['type']) {

					case 'LOA':
					
						// Check to make sure it was not already denied
						$loa = $this->DB->buildAndFetch( array( 
							'select' => '*', 
							'from' => $this->settings['perscom_database_requests'], 
							'where' => 'primary_id_field="' . $this->request['id'] . '"' ));

						// If not already set as Dropped
						if ($loa['status'] != 'Dropped') {

							// Get the member the LOA is for
							$soldier = IPSMember::load( $loa['member_id'] );

							// If we did not load a solider
							if (!$soldier) {
			
								// Throw an error
								$this->registry->output->showError( 'Unable to load specified soldier.', 12345678, false, '', 404 );
							}

							// Update the LOA entry
							$this->DB->update( $this->settings['perscom_database_loa'], array( 
								'status' => 'Dropped', 
								'returned' => 'true',
								'approved_member_id' => $this->memberData['member_id'],
								'approved_display_name' => $this->memberData['members_display_name'],
								'approved_date' => strtotime('now') ), 'primary_id_field="' . $this->request['relational'] . '"' );

							// Update the request entry
							$this->DB->update( $this->settings['perscom_database_requests'], array( 
								'status' => 'Dropped', 
								'administrator_members_display_name' => $this->memberData['members_display_name'],
								'administrator_member_id' => $this->memberData['member_id'] ), 'primary_id_field="' . $this->request['id'] . '"' );

							// Send notification
							$registry->notifications->sendNotification( array (
								'to' => $soldier, 
								'from' => $this->settings['perscom_application_submission_author'], 
								'title' => 'PERSCOM: Leave of Absence Dropped', 
								'text' => $this->lang->words['notification_loa_dropped'] ) );
						}

					break;

					case 'Discharge':

						// Check to make sure it was not already denied
						$discharge = $this->DB->buildAndFetch( array( 
							'select' => '*', 
							'from' => $this->settings['perscom_database_requests'], 
							'where' => 'primary_id_field="' . $this->request['id'] . '"' ));

						// If not already set as Dropped
						if ($discharge['status'] != 'Dropped') {

							// Get the member the LOA is for
							$soldier = IPSMember::load( $discharge['member_id'] );

							// If we did not load a solider
							if (!$soldier) {
			
								// Throw an error
								$this->registry->output->showError( 'Unable to load specified soldier.', 12345678, false, '', 404 );
							}

							// Update the request entry
							$this->DB->update( $this->settings['perscom_database_requests'], array( 
								'status' => 'Dropped', 
								'administrator_members_display_name' => $this->memberData['members_display_name'],
								'administrator_member_id' => $this->memberData['member_id'] ), 'primary_id_field="' . $this->request['id'] . '"' );

							// Send notification
							$registry->notifications->sendNotification( array (
								'to' => $soldier, 
								'from' => $this->settings['perscom_application_submission_author'], 
								'title' => 'PERSCOM: Discharge Dropped', 
								'text' => $this->lang->words['notification_discharge_dropped'] ) );
						}
					
					break;
				}

			break;
		
			case 'delete':

				// Handle type requests
				switch ($this->request['type']) {

					case 'LOA':
					
					break;

					case 'Discharge':
					
					break;

					case 'Record':

						// Create delete query
						$this->DB->delete( $this->settings['perscom_database_records'], 'primary_id_field="' . $this->request['id'] . '"');

						// Return success
						$this->returnJsonArray( array( 'success' => 1, 'message' => 'You successfully deleted the entry!' ) );

					break;
				}

			break;
		
			default:
			
			break;
		}
	}		
}