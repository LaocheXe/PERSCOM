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

class public_perscom_personnel_view extends ipsCommand
{
	public function doExecute( ipsRegistry $registry )
	{
		// Load our language file
		$this->registry->class_localization->loadLanguageFile( array( 'public_personnel' ) );
		$this->registry->class_localization->loadLanguageFile( array( 'public_base_camp' ) );

		// Get perscom classes
		$this->personnel = $this->registry->personnel;		
		$this->weapons = $this->registry->weapons;

		// Set Navigation
		$this->registry->output->addNavigation( $this->lang->words['base_camp'], 'app=perscom' );
		$this->registry->output->addNavigation( $this->lang->words['personnel_files'], NULL );

		// Set HTML settings
        $this->registry->output->setTitle( $this->lang->words['personnel_files'] );
        $this->registry->output->addContent( $this->registry->output->getTemplate('perscom')->viewPersonnel( $this->getPersonnel(), $this->weapons->loadWeapons() ) );
       	$this->registry->output->sendOutput();
	}

	public function getPersonnel() {

		// Get the personnel
		$rows = $this->personnel->loadPersonnelForDisplay();

		// Define array
		$personnel = array();

		// Loop through the soliders and sort by unit
		foreach ($rows as $soldier) {
			if (!array_key_exists($soldier['name'], $personnel)) {
				$personnel[$soldier['name']][0] = $soldier;
			}
			else {
				array_push($personnel[$soldier['name']], $soldier);
			}
		}

		// Return the personnel
		return $personnel;
	}
}