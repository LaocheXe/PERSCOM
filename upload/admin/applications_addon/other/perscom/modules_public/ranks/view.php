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

class public_perscom_ranks_view extends ipsCommand
{
	public function doExecute( ipsRegistry $registry )
	{
		// Load our language file
		$this->registry->class_localization->loadLanguageFile( array( 'public_ranks' ) );
		$this->registry->class_localization->loadLanguageFile( array( 'public_base_camp' ) );

		// Set navigation
		$this->registry->output->addNavigation( $this->lang->words['base_camp'], 'app=perscom' );
       	$this->registry->output->addNavigation( $this->lang->words['ranks_and_insignia'], NULL );

		// Get PERSCOM rank class
		$this->ranks = $this->registry->ranks;		

		// Set HTML settings
        $this->registry->output->setTitle( $this->lang->words['ranks_and_insignia'] );		
        $this->registry->output->addContent( $this->registry->output->getTemplate('perscom')->viewRanks( $this->ranks->loadRanks( '`order` DESC' ) ) );
       	$this->registry->output->sendOutput();
	}
}