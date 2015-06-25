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

       	// Check if user is in admin group or BMO group to edit user.
		if ( IPSMember::isInGroup( $this->memberData, $this->settings['perscom_administrative_usergroup'] , true ) ) {

			// Set allow edit to TRUE
			$this->allowEdit = TRUE;
		}

		// Get PERSCOM rank class
		$this->ranks = $this->registry->ranks;		

		// Handle HTTP Request
		switch ($this->request['action']) 
		{
			case 'add':
				$this->addRank();
			break;

			case 'edit':
				$this->editRank();
			break;

			case 'delete':
				$this->deleteRank();
			break;

			default:

			// Set navigation
			$this->registry->output->addNavigation( $this->lang->words['ranks_and_insignia'], NULL );

			// Set HTML settings
	        $this->registry->output->setTitle( $this->lang->words['ranks_and_insignia'] );		
	        $this->registry->output->addContent( $this->registry->output->getTemplate('perscom')->viewRanks( $this->ranks->loadRanks( '`order` DESC' ), $this->allowEdit ) );
	       	$this->registry->output->sendOutput();

			break;
		}
	}

	public function addRank() {

		// If we are submitting
		if ($this->request['do'] == 'submit') {
			
			// Submit new rank
			$this->DB->insert( $this->settings['perscom_database_ranks'], array ( 
				'order' => $this->request['order'], 
				'title' => $this->request['title'], 
				'abbreviation' => $this->request['abbreviation'], 
				'pay_grade' => $this->request['pay_grade'],
				'prerequisites' => $this->request['prerequisites'],
				'description' => $this->request['description'] ) );

			// Execute the request
			$this->DB->execute();
		}

		// Add navigation
		$this->registry->output->addNavigation( $this->lang->words['ranks_and_insignia'], 'app=perscom&module=ranks' );
		$this->registry->output->addNavigation( $this->lang->words['add_ranks_and_insignia'], NULL );

		// Set HTML settings
	    $this->registry->output->setTitle( $this->lang->words['add_ranks_and_insignia'] );		
	    $this->registry->output->addContent( $this->registry->output->getTemplate('perscom')->addRank() );
	    $this->registry->output->sendOutput();
	}

	public function editRank() {

		// Check to make sure we have an id
		if ($this->request['id'] == '' || !isset($this->request['id'])) {

			// Show error
			$this->registry->output->showError( 'No rank specified.', 12345678, false, '', 404 );
		}

		// If we are submitting
		if ($this->request['do'] == 'submit') {
			
			// Update rank
			$this->DB->update( $this->settings['perscom_database_ranks'], array ( 
				'`order`' => $this->request['order'], 
				'title' => $this->request['title'], 
				'abbreviation' => $this->request['abbreviation'], 
				'pay_grade' => $this->request['pay_grade'],
				'prerequisites' => $this->request['prerequisites'],
				'description' => $this->request['description'] ), 'primary_id_field="' . $this->request['id'] . '"' );

			// Execute the request
			$this->DB->execute();
		}

		// Fetch our rank
		$rank = $this->DB->buildAndFetch( array ( 'select' => '*', 
			'from' => $this->settings['perscom_database_ranks'], 
			'where' => 'primary_id_field="' . $this->request['id'] . '"' ) );

		// Add navigation
		$this->registry->output->addNavigation( $this->lang->words['ranks_and_insignia'], 'app=perscom&module=ranks' );
		$this->registry->output->addNavigation( $this->lang->words['edit_ranks_and_insignia'], NULL );

		// Set HTML settings
	    $this->registry->output->setTitle( $this->lang->words['edit_ranks_and_insignia'] );		
	    $this->registry->output->addContent( $this->registry->output->getTemplate('perscom')->editRank( $rank ) );
	    $this->registry->output->sendOutput();
	}

	public function deleteRank() {

		// Check to make sure we have an id
		if ($this->request['id'] == '' || !isset($this->request['id'])) {

			// Show error
			$this->registry->output->showError( 'No rank specified.', 12345678, false, '', 404 );
		}
			
		// Delete rank
		$this->DB->delete( $this->settings['perscom_database_ranks'], 'primary_id_field="' . $this->request['id'] . '"' );

		// Execute the request
		$this->DB->execute();

		// Add navigation
		$this->registry->output->addNavigation( $this->lang->words['ranks_and_insignia'], 'app=perscom&module=ranks' );
		$this->registry->output->addNavigation( $this->lang->words['edit_ranks_and_insignia'], NULL );

		// Set HTML settings
	    $this->registry->output->setTitle( $this->lang->words['edit_ranks_and_insignia'] );		
	    $this->registry->output->addContent( $this->registry->output->getTemplate('perscom')->editRank( $rank ) );
	    $this->registry->output->sendOutput();
	}
}