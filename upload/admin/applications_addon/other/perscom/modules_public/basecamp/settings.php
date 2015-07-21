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

// Define some variables
define( 'IPS_XML_RPC_DEBUG_ON'  , 0 );
define( 'IPS_XML_RPC_DEBUG_FILE', '' );

// Require xml
require_once( "ips_kernel/classXmlRpc.php" );

class public_perscom_basecamp_settings extends ipsCommand
{
	public function doExecute( ipsRegistry $registry )
	{
		// Load our language file
		$this->registry->class_localization->loadLanguageFile( array( 'public_base_camp' ) );

		// Set navigation
		$this->registry->output->addNavigation( $this->lang->words['base_camp'], 'app=perscom' );		
       	$this->registry->output->addNavigation( $this->lang->words['file_settings'], NULL );

		// Check to make sure member is in admin group
		if ( !IPSMember::isInGroup( $this->memberData, $this->settings['perscom_administrative_usergroup'] , true) ) {
			
			// Throw login error
			$this->registry->output->showError( 'You do not have permission to view this module.', 000123, false, '', 403 );
		}

		// Add PERSCOM classes
		$this->combat_units = $this->registry->combat_units;
		$this->admin_units = $this->registry->admin_units;
		$this->weapons = $this->registry->weapons;

		// Handle HTTP Request
		switch ($this->request['do']) 
		{
			case 'submit':

				// Save whatever we want
				$this->saveSettings();

			break;

			case 'delete':

				// Delete whatever we want
				$this->deleteItem();

			break;

			default:

			break;
		}

		$this->getUsergroups();

		// Output HTML
		$this->registry->output->setTitle( $this->lang->words['file_settings'] );
        $this->registry->output->addContent( $this->registry->output->getTemplate('perscom')->viewSettings( $this->combat_units->loadCombatUnits(), $this->admin_units->loadAdminUnits(), $this->weapons->loadWeapons(), $this->getUsergroups(), $this->getLicenseInformation() ) );
       	$this->registry->output->sendOutput();
	}

	public function getUsergroups() {

		// Query the database for the groups
		$this->DB->build( array( 'select' => '*', 
			'from' => 'groups', 
			'order' => 'g_title ASC' ) );

		// Execute the query
		$this->DB->execute();

		// Create our drop down array
		$dropdown = array();

		// Loop through the results
		while( $row = $this->DB->fetch() )
		{
			// If the group is a staff group
			if ( $row['g_access_cp'] ) {

				// Alter the name and add the STAFF string
				$row['g_title'] .= ' ' . $this->lang->words['setting_staff_tag'] . ' ';
			}
			
			// Add the group to the dropdown array
			array_push($dropdown, $row);
		}

		// Return the groups
		return $dropdown;
	}

	public function getLicenseInformation() {

		// Check our license key
		$classXmlRpc = new classXmlRpc();
		$response = $classXmlRpc->sendXmlRpc( "http://www.3rdinf.us/interface/licenses.php", "info", array( 
			'key' => ipsRegistry::$settings['perscom_license_key'] ) );

		// Return the params
		return $classXmlRpc->xmlrpc_params[0];
	}

	public function deleteItem() {

		// If we did not load a delete id
		if (!$this->request['id'] || !isset($this->request['id'])) {
	
			// Throw an error
			$this->registry->output->showError( 'Unable to delete specified item.', 12345678, false, '', 404 );
		}

		// Switch between the different types
		switch ($this->request['type']) {
			
			// If a combat unit
			case 'combat_unit':

				// Get the combat unit
				$combat_unit = $this->DB->buildAndFetch( array( 'select' => 'name', 
					'from' => $this->settings['perscom_database_units'], 
					'where' => 'primary_id_field="' . $this->request['id'] . '"' ) );
				
				// Create our delete statement
				$this->DB->delete( $this->settings['perscom_database_units'], 'primary_id_field="' . $this->request['id'] . '"' );

				// Set description
				$description = sprintf('The %s combat unit was deleted from the database.', $combat_unit['name']);

				// Set the type
				$type = 'Combat Unit';

			break;

			// If an admin unit
			case 'admin_unit':
				
				// Get the admin unit
				$admin_unit = $this->DB->buildAndFetch( array( 'select' => 'name', 
					'from' => $this->settings['perscom_database_dmos'], 
					'where' => 'primary_id_field="' . $this->request['id'] . '"' ) );
				
				// Create our delete statement
				$this->DB->delete( $this->settings['perscom_database_dmos'], 'primary_id_field="' . $this->request['id'] . '"' );

				// Set description
				$description = sprintf('The %s administrative unit was deleted from the database.', $admin_unit['name']);

				// Set the type
				$type = 'Administrative Unit';

			break;

			// If a weapon
			case 'weapon':
				
				// Get the weapon
				$weapon = $this->DB->buildAndFetch( array( 'select' => 'make_and_model', 
					'from' => $this->settings['perscom_database_weapons'], 
					'where' => 'primary_id_field="' . $this->request['id'] . '"' ) );
				
				// Create our delete statement
				$this->DB->delete( $this->settings['perscom_database_weapons'], 'primary_id_field="' . $this->request['id'] . '"' );

				// Set description
				$description = sprintf('The %s weapon was deleted from the database.', $weapon['make_and_model']);

				// Set the type
				$type = 'Weapon';

			break;
		}

		// Add to log
		$this->DB->insert( $this->settings['perscom_database_requests'], array( 
			'member_id' => '---', 
			'members_display_name' => '---',
			'date' => strtotime('now'),
			'description' => $description,
			'type' => $type,
			'administrator_member_id' => $this->memberData['member_id'],
			'administrator_members_display_name' => $this->memberData['members_display_name'],
			'status' => '---',
			'relational_primary_id_field' => NULL ) );

		// Execute
		$this->DB->execute();
	}

	public function saveSettings() {

		// If we are editing
		if ($this->request['action'] == 'edit') {
		
			// If we did not load a edit id
			if (!$this->request['id'] || !isset($this->request['id'])) {
		
				// Throw an error
				$this->registry->output->showError( 'Unable to save specified item.', 12345678, false, '', 404 );
			}	
		}

		// Switch between the different types
		switch ($this->request['type']) {
			
			// If a combat unit
			case 'combat_unit':

				// Check to make sure we dont already have a combat unit with that usergroup id
				$combat_unit = $this->DB->buildAndFetch( array( 'select' => '*', 
					'from' => $this->settings['perscom_database_units'], 
					'where' => 'forum_usergroup="' . $this->request['cunit_usergroup'] . '"') );

				// If we got a combat unit
				if ($combat_unit && $combat_unit['primary_id_field'] != $this->request['id']) {
					
					// Throw an error
					$this->registry->output->showError( 'There is alreay a combat unit with that usergroup assigned to it. Please choose another usergroup.', 12345678, false, '', 401 );
				}

				// We did not get a combat unit
				else {

					// If we are editing
					if ($this->request['action'] == 'edit') {

						// Update the combat unit
						$this->DB->update( $this->settings['perscom_database_units'], array( 'name' => $this->request['cunit_name'], 
							'unit_position' => $this->request['position'],
							'nickname' => $this->request['nickname'],
							'`order`' => $this->request['order'],
							'forum_usergroup' => $this->request['cunit_usergroup'] ), 'primary_id_field="' . $this->request['id'] . '"' );

						// Set description
						$description = sprintf('The %s combat unit was updated in the database.', $this->request['cunit_name']);
					}

					// We are adding
					else {

						// Insert the combat unit
						$this->DB->insert( $this->settings['perscom_database_units'], array( 'name' => $this->request['cunit_name'], 
							'unit_position' => $this->request['position'],
							'nickname' => $this->request['nickname'],
							'order' => $this->request['order'],
							'forum_usergroup' => $this->request['cunit_usergroup'] ) );

						// Set description
						$description = sprintf('The %s combat unit was added to the database.', $this->request['cunit_name']);
					}

					// Set the type
					$type = 'Combat Unit';
				}

			break;

			// If an admin unit
			case 'admin_unit':

				// Check to make sure we dont already have a admin unit with that usergroup id
				$admin_unit = $this->DB->buildAndFetch( array( 'select' => '*', 
					'from' => $this->settings['perscom_database_dmos'], 
					'where' => 'forum_usergroup="' . $this->request['aunit_usergroup'] . '"') );

				// If we got a admin unit
				if ($admin_unit && $admin_unit['primary_id_field'] != $this->request['id']) {
					
					// Throw an error
					$this->registry->output->showError( 'There is alreay an administrative unit with that usergroup assigned to it. Please choose another usergroup.', 12345678, false, '', 401 );
				}

				// We did not get a admin unit
				else {

					// If we are editing
					if ($this->request['action'] == 'edit') {

						// Update the admin unit
						$this->DB->update( $this->settings['perscom_database_dmos'], array( 'name' => $this->request['aunit_name'], 
							'mos' => $this->request['mos'],
							'image' => $this->request['aunit_image'],
							'responsibilities' => $this->request['responsibilities'],
							'prerequisites' => $this->request['prerequisites'],
							'forum_usergroup' => $this->request['aunit_usergroup'] ), 'primary_id_field="' . $this->request['id'] . '"' );

						// Set description
						$description = sprintf('The %s administrative unit was updated in the database.', $this->request['aunit_name']);
					}

					// We are adding
					else {

						// Insert the admin unit
						$this->DB->insert( $this->settings['perscom_database_dmos'], array( 'name' => $this->request['aunit_name'], 
							'mos' => $this->request['mos'],
							'image' => $this->request['aunit_image'],
							'responsibilities' => $this->request['responsibilities'],
							'prerequisites' => $this->request['prerequisites'],
							'forum_usergroup' => $this->request['aunit_usergroup'] ) );

						// Set description
						$description = sprintf('The %s administrative unit was added to the database.', $this->request['aunit_name']);
					}

					// Set the type
					$type = 'Administrative Unit';
				}

			break;

			// If a weapon
			case 'weapon':

				// If we are editing
				if ($this->request['action'] == 'edit') {

					// Update the weapon
					$this->DB->update( $this->settings['perscom_database_weapons'], array( 'make_and_model' => $this->request['make'], 
						'caliber' => $this->request['caliber'],
						'weight' => $this->request['weight'],
						'magazine' => $this->request['magazine'],
						'introduction_date' => $this->request['date'],
						'fire_type' => $this->request['fire_type'],
						'rate_of_fire' => $this->request['fire_rate'],
						'effective_firing_range' => $this->request['firing_range'],
						'image' => $this->request['weapon_image'],
						'barrel_length' => $this->request['length'],
						'action' => $this->request['weapon_action'],
						'muzzle_velocity' => $this->request['velocity'],
						'sights' => $this->request['sights'] ), 'primary_id_field="' . $this->request['id'] . '"' );

					// Set description
					$description = sprintf('The %s weapon was updated in the database.', $this->request['make']);
				}

				// We are adding
				else {

					// Insert the weapon
					$this->DB->insert( $this->settings['perscom_database_weapons'], array( 'make_and_model' => $this->request['make'], 
						'caliber' => $this->request['caliber'],
						'weight' => $this->request['weight'],
						'magazine' => $this->request['magazine'],
						'introduction_date' => $this->request['date'],
						'fire_type' => $this->request['fire_type'],
						'rate_of_fire' => $this->request['fire_rate'],
						'effective_firing_range' => $this->request['firing_range'],
						'image' => $this->request['weapon_image'],
						'barrel_length' => $this->request['length'],
						'action' => $this->request['weapon_action'],
						'muzzle_velocity' => $this->request['velocity'],
						'sights' => $this->request['sights'] ) );

					// Set description
					$description = sprintf('The %s weapon was added to the database.', $this->request['make']);
				}

				// Set the type
				$type = 'Weapon';
				
			break;
		}

		// Add to log
		$this->DB->insert( $this->settings['perscom_database_requests'], array( 
			'member_id' => '---', 
			'members_display_name' => '---',
			'date' => strtotime('now'),
			'description' => $description,
			'type' => $type,
			'administrator_member_id' => $this->memberData['member_id'],
			'administrator_members_display_name' => $this->memberData['members_display_name'],
			'status' => '---',
			'relational_primary_id_field' => NULL ) );

		// Execute
		$this->DB->execute();
	}
}