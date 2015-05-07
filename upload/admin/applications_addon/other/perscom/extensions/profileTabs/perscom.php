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

class profile_perscom extends profile_plugin_parent
{
	public function return_html_block( $member=array() ) 
	{
		// Select the personnel file
		$personnel_file = $this->DB->buildAndFetch( array( 'select' => '*', 
			'from' => $this->settings['perscom_database_personnel_files'], 
			'where' => 'member_id="' . $member['member_id'] . '"' ) );

		// If we get a personnel file back
		if ($personnel_file) {
		
			// Query the DB to get the soldier information
			$soldier = $this->DB->buildAndFetch( array(
						'select'	=> 'p.*',
						'from'		=> array( $this->settings['perscom_database_personnel_files'] => 'p' ),
						'where'		=> 'p.member_id="' . $member['member_id'] . '"',
						'add_join'	=> array(
										array(
											'select'	=> 'r.title AS rank_long, r.abbreviation AS rank_short',
											'from'		=> array( $this->settings['perscom_database_ranks']  => 'r' ),
											'where'		=> 'r.primary_id_field=p.rank',
											'type'		=> 'left',
										),
										array(
											'select'	=> 'u.name AS unit_long, u.unit_position AS unit_short',
											'from'		=> array( $this->settings['perscom_database_units']  => 'u' ),
											'where'		=> 'u.primary_id_field=p.combat_unit',
											'type'		=> 'left',
										),
										array(
											'select'	=> 'w.primary_id_field AS weapon_id, w.make_and_model AS weapon',
											'from'		=> array( $this->settings['perscom_database_weapons']  => 'w' ),
											'where'		=> 'w.primary_id_field=p.weapon',
											'type'		=> 'left',
										),
										array(
											'select'	=> 's.primary_id_field AS status_id, s.status, s.hex_color AS status_hex_color',
											'from'		=> array( $this->settings['perscom_database_status']  => 's' ),
											'where'		=> 's.primary_id_field=p.status',
											'type'		=> 'left',
										),
										array(
											'select'	=> 'GROUP_CONCAT(d.name ORDER BY d.primary_id_field SEPARATOR \', \') dmos',
											'from'		=> array( $this->settings['perscom_database_dmos']  => 'd' ),
											'where'		=> 'FIND_IN_SET(d.primary_id_field, p.admin_unit) > 0',
											'type'		=> 'left',
										),
									)
								)		
							);

			// Set our time in grade and time in service numbers
			$now = new DateTime();
			$now->setTimeStamp(strtotime('now'));
			$induction = new DateTime();
			$induction->setTimeStamp($soldier['induction_date']);
			$promoted = new DateTime();
			$promoted->setTimeStamp($soldier['promotion_date']);

			// Calculate the date interval
			$time_in_service = date_diff($now, $induction);
			$time_in_grade = date_diff($now, $promoted);

			// Set the arrays
			$soldier['time_in_service'] = array('days' => $time_in_service->d, 'months' => $time_in_service->m, 'years' => $time_in_service->y);
			$soldier['time_in_grade'] = array('days' => $time_in_grade->d, 'months' => $time_in_grade->m, 'years' => $time_in_grade->y);
			
			// Set the output
			$output = $this->registry->getClass('output')->getTemplate('perscom')->profileTab( $soldier );	
		}

		// We did not get a personnel file back
		else {

			// Set the output
			$output = $this->registry->getClass('output')->getTemplate('perscom')->profileTabNoContent();
		}

		// Return the output
		return $output;
	}	
}