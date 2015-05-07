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

class public_perscom_enlistment_requirements extends ipsCommand
{
	public function doExecute( ipsRegistry $registry )
	{
		// Load our language file
		$this->registry->class_localization->loadLanguageFile( array( 'public_enlistment' ) );

		// Check to make sure the member is logged in
		$member = $this->registry->member()->fetchMemberData();
		if ($member['member_id'] == '0') {
			
			// Throw login error
			$this->registry->output->showError( 'You must have an account and be logged in to submit an application.', 000123, false, '', 403 );
		}
		
		// Set Title
		$this->registry->output->setTitle( $this->lang->words['enlistment_requirements'] );

		// Add Navigation
		$this->registry->output->addNavigation( $this->lang->words['enlistment_requirements'], NULL );

		// Output HTML
		$this->registry->output->addContent( $this->registry->output->getTemplate('perscom')->viewEnlistmentRequirements( ) );

		// Output the HTML
       	$this->registry->output->sendOutput();
	}
}