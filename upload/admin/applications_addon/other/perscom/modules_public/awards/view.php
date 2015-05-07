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

class public_perscom_awards_view extends ipsCommand
{
	private $query_type = 'Medal';

	public function doExecute( ipsRegistry $registry )
	{
		// Load our language file
		$this->registry->class_localization->loadLanguageFile( array( 'public_base_camp' ) );
		$this->registry->class_localization->loadLanguageFile( array( 'public_awards' ) );

		// Set navigation
		$this->registry->output->addNavigation( $this->lang->words['base_camp'], 'app=perscom' );
		$this->registry->output->addNavigation( $this->lang->words['awards_and_commendations'], NULL );

		// If there is a parameter
		if (isset($this->request['type']) && $this->request['type'] != '') {

			// Set the query type
			$this->query_type = $this->request['type'];
		}

		// Get PERSCOM award class
		$this->awards = $this->registry->awards;		

		// Set HTML settings
        $this->registry->output->setTitle( $this->lang->words['awards_and_commendations'] );		
        $this->registry->output->addContent( $this->registry->output->getTemplate('perscom')->viewAwards( $this->awards->loadAwards( $this->query_type, '`order` ASC' ) ) );
       	$this->registry->output->sendOutput();
	}
}