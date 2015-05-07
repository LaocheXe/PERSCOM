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

class public_perscom_basecamp_settings extends ipsCommand
{
	private $perscom_settings = array();

	public function doExecute( ipsRegistry $registry )
	{
		// Load our language file
		$this->registry->class_localization->loadLanguageFile( array( 'public_base_camp' ) );

		// Set navigation
		$this->registry->output->addNavigation( $this->lang->words['base_camp'], 'app=perscom' );		
       	$this->registry->output->addNavigation( $this->lang->words['settings'], NULL );

		// Check to make sure member is in admin group
		if ( !IPSMember::isInGroup( $this->memberData, $this->settings['perscom_administrative_usergroup'] , true) ) {
			
			// Throw login error
			$this->registry->output->showError( 'You do not have permission to view this module.', 000123, false, '', 403 );
		}

		// Get all the settings
		$this->getForumList();
		$this->getUserGroups();
		$this->getCitationSigningUser();

		// Handle HTTP Request
		switch ($this->request['do']) 
		{
			case 'save':
				$this->saveSettings();
			break;

			default:

			break;
		}

		// Output HTML
		$this->registry->output->setTitle( $this->lang->words['settings'] );
        $this->registry->output->addContent( $this->registry->output->getTemplate('perscom')->viewSettings( $this->perscom_settings ) );
       	$this->registry->output->sendOutput();
	}

	public function getUserGroups() {

		// Query the database
		$this->DB->build( array( 'select' => '*', 
			'from' => 'groups', 
			'order' => 'g_title ASC' ) );

		// Execute it
		$results = $this->DB->execute();

		// Build array
		$rows = array();

		// Loop through results and add to array
		while ( $r =  $this->DB->fetch( $results ) ) {
		
			// Add to array
			array_push($rows, $r);
		}

		// Save the result
		$this->perscom_settings['usergroups'] = $rows;
	}

	public function getForumList() {

		// Load forums list
		$this->perscom_settings['forum_list'] = $this->registry->getClass('class_forums')->forumsForumJump( $html=1, $override=0, $remove_redirects=0, $selected = array( 'f' => $this->settings['perscom_application_submission_forum']));
	
	}

	public function getCitationSigningUser() {

		// Load the user
		$user = IPSMember::load( $this->settings['perscom_signing_soldier'] );

		// Get user
		$this->perscom_settings['citation_signing_user'] = $user['members_display_name'];
	}
}