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

class public_perscom_basecamp_view extends ipsCommand
{
	public function doExecute( ipsRegistry $registry )
	{
		// Load our language file
		$this->registry->class_localization->loadLanguageFile( array( 'public_base_camp' ) );
		$this->registry->class_localization->loadLanguageFile( array( 'public_personnel' ) );
		$this->registry->class_localization->loadLanguageFile( array( 'public_ranks' ) );
		$this->registry->class_localization->loadLanguageFile( array( 'public_awards' ) );

		// Set navigation
       	$this->registry->output->addNavigation( $this->lang->words['base_camp'], NULL );

		// Set HTML settings
        $this->registry->output->setTitle( $this->lang->words['base_camp'] );
        $this->registry->output->addContent( $this->registry->output->getTemplate('perscom')->viewBaseCamp( $this->checkIfAdministrator(), $this->checkIfMember(), $this->getNotificationCount(), $this->checkIfBMOMember(), $this->checkIfRROMember(), $this->getApplicationCount() ) );
       	$this->registry->output->sendOutput();
	}

	public function checkIfMember () {

		// Check if current user is in group
		return IPSMember::isInGroup( $this->memberData, $this->settings['perscom_base_unit_usergroup'] , true);
	}

	public function checkIfBMOMember () {

		// Check if current user is in group
		return IPSMember::isInGroup( $this->memberData, $this->settings['perscom_bmo_usergroup'] , true);
	}

	public function checkIfRROMember () {

		// Check if current user is in group
		return IPSMember::isInGroup( $this->memberData, $this->settings['perscom_recruiting_and_retention_group'] , true);
	}

	public function checkIfAdministrator () {

		// Check if current user is in group
		return IPSMember::isInGroup( $this->memberData, $this->settings['perscom_administrative_usergroup'] , true);
	}

	public function getNotificationCount() {

		// Query the DB to look for all TPR's
		$this->DB->build( array( 'select' => '*', 
			'from' => $this->settings['perscom_database_requests'], 
			'where' => 'status="Pending"' ) );

		// Execute the DB query
		$result = $this->DB->execute();

		// Return the number of notifications
		return $result->num_rows;
	}

	public function getApplicationCount() {

		// Query the DB to look for all TPR's
		$this->DB->build( array( 'select' => '*', 
			'from' => $this->settings['perscom_database_personnel_files'], 
			'where' => 'status="5"' ) );

		// Execute the DB query
		$result = $this->DB->execute();

		// Return the number of notifications
		return $result->num_rows;
	}
}