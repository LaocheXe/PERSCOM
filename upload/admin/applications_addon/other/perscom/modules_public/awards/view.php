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

		// Check if user is in admin group or BMO group to edit user.
		if ( IPSMember::isInGroup( $this->memberData, $this->settings['perscom_administrative_usergroup'] , true ) ) {

			// Set allow edit to TRUE
			$this->allowEdit = TRUE;
		}

		// If there is a parameter
		if (isset($this->request['type']) && $this->request['type'] != '') {

			// Set the query type
			$this->query_type = $this->request['type'];
		}

		// Get PERSCOM award class
		$this->awards = $this->registry->awards;

		// Handle HTTP Request
		switch ($this->request['action']) 
		{
			case 'add':
				$this->addAward();
			break;

			case 'edit':
				$this->editAward();
			break;

			case 'delete':
				$this->deleteAward();
			break;

			default:

			// Set navigation
			$this->registry->output->addNavigation( $this->lang->words['awards_and_commendations'], NULL );

			// Set HTML settings
        	$this->registry->output->setTitle( $this->lang->words['awards_and_commendations'] );		
        	$this->registry->output->addContent( $this->registry->output->getTemplate('perscom')->viewAwards( $this->awards->loadAwards( $this->query_type, '`order` ASC' ), $this->allowEdit ) );
       		$this->registry->output->sendOutput();

			break;
		}
	}

	public function addAward() {

		// If we are submitting
		if ($this->request['do'] == 'submit') {
			
			// Submit new award
			$this->DB->insert( $this->settings['perscom_database_awards'], array ( 
				'title' => $this->request['title'], 
				'award_image' => $this->request['award_image'], 
				'history' => $this->request['history'], 
				'prerequisites' => $this->request['prerequisites'],
				'type' => $this->request['type'],
				'order' => $this->request['order'] ) );

			// Execute the request
			$this->DB->execute();
		}

		// Add navigation
		$this->registry->output->addNavigation( $this->lang->words['awards_and_commendations'], 'app=perscom&module=awards' );
		$this->registry->output->addNavigation( $this->lang->words['add_awards_and_commendations'], NULL );

		// Set HTML settings
	    $this->registry->output->setTitle( $this->lang->words['add_awards_and_commendations'] );		
	    $this->registry->output->addContent( $this->registry->output->getTemplate('perscom')->addAward() );
	    $this->registry->output->sendOutput();
	}

	public function editAward() {

		// Check to make sure we have an id
		if ($this->request['id'] == '' || !isset($this->request['id'])) {

			// Show error
			$this->registry->output->showError( 'No award specified.', 12345678, false, '', 404 );
		}

		// If we are submitting
		if ($this->request['do'] == 'submit') {
			
			// Update award
			$this->DB->update( $this->settings['perscom_database_awards'], array ( 
				'title' => $this->request['title'], 
				'award_image' => $this->request['award_image'], 
				'history' => $this->request['history'], 
				'prerequisites' => $this->request['prerequisites'],
				'type' => $this->request['type'],
				'`order`' => $this->request['order'] ), 'primary_id_field="' . $this->request['id'] . '"' );

			// Execute the request
			$this->DB->execute();
		}

		// Fetch our award
		$award = $this->DB->buildAndFetch( array ( 'select' => '*', 
			'from' => $this->settings['perscom_database_awards'], 
			'where' => 'primary_id_field="' . $this->request['id'] . '"' ) );

		// Add navigation
		$this->registry->output->addNavigation( $this->lang->words['awards_and_commendations'], 'app=perscom&module=awards' );
		$this->registry->output->addNavigation( $this->lang->words['edit_awards_and_commendations'], NULL );

		// Set HTML settings
	    $this->registry->output->setTitle( $this->lang->words['edit_awards_and_commendations'] );		
	    $this->registry->output->addContent( $this->registry->output->getTemplate('perscom')->editAward( $award ) );
	    $this->registry->output->sendOutput();
	}

	public function deleteAward() {

		// Check to make sure we have an id
		if ($this->request['id'] == '' || !isset($this->request['id'])) {

			// Show error
			$this->registry->output->showError( 'No award specified.', 12345678, false, '', 404 );
		}
			
		// Delete award
		$this->DB->delete( $this->settings['perscom_database_awards'], 'primary_id_field="' . $this->request['id'] . '"' );

		// Execute the request
		$this->DB->execute();

		// Add navigation
		$this->registry->output->addNavigation( $this->lang->words['awards_and_commendations'], 'app=perscom&module=awards' );
		$this->registry->output->addNavigation( $this->lang->words['edit_awards_and_commendations'], NULL );

		// Set HTML settings
	    $this->registry->output->setTitle( $this->lang->words['edit_awards_and_commendations'] );		
	    $this->registry->output->addContent( $this->registry->output->getTemplate('perscom')->editAward( $award ) );
	    $this->registry->output->sendOutput();
	}
}