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

class perscom_model_personnel extends perscom_model_perscom {	

	public function loadPersonnel() {

		// Query the DB to get all the personnel
		$this->DB->build( array( 
			'select' => '*', 
			'from' => $this->settings['perscom_database_personnel_files'],
		    'where' => 'status="1" OR status="2" OR status="3" OR status="4" OR status="9"',	
			'order' => 'lastname' ) );

		// Define array
		$rows = array();

		// Execute the DB query
		$result = $this->DB->execute();

		// Loop through the results and create an array of RRO soldiers
		while( $r = $this->DB->fetch( $result ) )
		{
			// Add the members on end of the array
			array_push($rows, $r);
		}

		// Return the result
		return $rows;
	}

	public function loadPersonnelForDisplay() {

		// Query the DB to look for everyone in the defined base unit usergroup
		$this->DB->build( array(
			'select'	=> 'p.*, p.member_id as personnel_member_id',
			'from'		=> array( $this->settings['perscom_database_personnel_files'] => 'p' ),
			'where'		=> 'p.combat_unit != "624"',
			'order'		=> 'u.order ASC, r.order DESC',
			'add_join'	=> array(
							array(
								'select'	=> 'r.*',
								'from'		=> array( $this->settings['perscom_database_ranks']  => 'r' ),
								'where'		=> 'r.primary_id_field=p.rank',
								'type'		=> 'left',
							),
							array(
								'select'	=> 'u.*',
								'from'		=> array( $this->settings['perscom_database_units']  => 'u' ),
								'where'		=> 'u.primary_id_field=p.combat_unit AND u.name!="Civilians"',
								'type'		=> 'left',
							),
							array(
								'select'	=> 's.status as status_name, s.hex_color as status_hex_color',
								'from'		=> array( $this->settings['perscom_database_status']  => 's' ),
								'where'		=> 's.primary_id_field=p.status',
								'type'		=> 'left',
							),
							array(
								'select'	=> 'w.*, w.primary_id_field as weapon_primary_id_field',
								'from'		=> array( $this->settings['perscom_database_weapons']  => 'w' ),
								'where'		=> 'w.primary_id_field=p.weapon',
								'type'		=> 'left',
							),
						)
					)		
			    );

		// Define array
		$rows = array();

		// Execute the DB query
		$result = $this->DB->execute();

		// Loop through the results and create an array of RRO soldiers
		while( $r = $this->DB->fetch( $result ) )
		{
			// Add the members on end of the array
			array_push($rows, $r);
		}

		// Return the results
		return $rows;
	}

	public function loadSoldierInformation($id = '') {

		// Make sure the id is set
		if (isset($id) && $id != '') {

			// Query the DB to get the soldier information
			$soldier = $this->DB->buildAndFetch( array(
						'select'	=> 'p.*',
						'from'		=> array( $this->settings['perscom_database_personnel_files'] => 'p' ),
						'where'		=> 'p.member_id="' . $id . '"',
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

			// Get soldier's image
			$image = $this->DB->buildAndFetch( array( 'select' => 'pp_main_photo AS main_photo, pp_thumb_photo AS thumb_photo', 
				'from' => 'profile_portal', 
				'where' => 'pp_member_id="' . $id . '"' ));

			// Set the image array properties
			$image['main_photo'] = basename($image['main_photo'], '.jpg');
			$image['thumb_photo'] = basename($image['thumb_photo'], '.jpg');
			$soldier['images'] = $image;

			// Return the soldier
			return $soldier;
		}

		// Return nothing
		return NULL;
	}

	public function loadServiceRecord($id = '') {

		// Make sure the id is set
		if (isset($id) && $id != '') {
			
			// Query the database and get all the service record entries for the soldier
			$this->DB->build( array ( 'select' => '*', 
				'from' => $this->settings['perscom_database_records'], 
				'where' => 'member_id="' . $id . '" AND display="Yes" AND type!="Award / Commendation" AND type!="Combat Record Entry"',
				'order' => 'date DESC' ) );

			// Execute the DB query
			$result = $this->DB->execute();

			// Set up the array
			$service_record_entries = array();

			// Set our loop counters
			$promotion_count = 0;
			$demotion_count = 0;
			$assignment_count = 0;

			// Loop over the results and add them to the soldier array
			while( $r = $this->DB->fetch( $result ) )
			{
				// Format the date
				$r['date'] = strftime($this->settings['clock_short2'], $r['date']);	

				// If type is promotion or demotion
				if ($r['type'] == 'Promotion' || $r['type'] == 'Demotion') {

					// Get the rank image
					$rank = $this->DB->buildAndFetch( array ( 'select' => 'abbreviation as rank', 
						'from' => $this->settings['perscom_database_ranks'], 
						'where' => 'title="'.$r['rank'].'"' ) );

					// Set grade
					if ($r['discharge_grade'] == '') {

						// Up the counters
						$promotion_count++;

						// Add the citation to the award record
						$r['html'] = '<a href="#" id="a_promotion_'.$promotion_count.'" rank="'.$r['rank'].'" count="'.$promotion_count.'" class="promotion" image="'.$this->settings['board_url'].'/images/perscom/insignia/large/plain_background/'.$rank['rank'].'.png">Click to view Citation</a>';

						// Set count
						$r['count'] = $promotion_count;
					}
					else {
						// Up the counters
						$demotion_count++;

						// Add the citation to the award record
						$r['html'] = '<a href="#" id="a_demotion_'.$demotion_count.'" grade="'.strtolower($r['discharge_grade']).'" count="'.$demotion_count.'" class="demotion" rank="'.$r['rank'].'" image="'.$this->settings['board_url'].'/images/perscom/insignia/large/plain_background/'.$rank['rank'].'.png">Click to view Citation</a>';

						// Set count
						$r['count'] = $demotion_count;
					}
				}

				// If the type is assignment
				else if ($r['type'] == 'Assignment') {		

					// Up the counters
					$assignment_count++;

					// Add the citation to the award record
					$r['html'] = '<a href="#" id="a_assignment_'.$assignment_count.'" count="'.$assignment_count.'" class="assignment" unit="'.$r['combat_unit'].'" position="'.$r['position'].'">Click to view OPORD</a>';

					// Set count
					$r['count'] = $assignment_count;
				}

				// No specific type
				else {	

					// Set no HTML
					$r['html'] = '---';
				}				
				
				// Add the members on end of the array
				array_push($service_record_entries, $r);
			}

			// Return the service record entries
			return $service_record_entries;
		}

		// Return nothing
		return NULL;
	}

	public function loadAwardRecord($id = '') {

		// Make sure the id is set
		if (isset($id) && $id != '') {
			
			// Query the database and get all the service record entries for the soldier
			$this->DB->build( array ( 'select' => '*', 
				'from' => $this->settings['perscom_database_records'], 
				'where' => 'member_id="' . $id . '" AND display="Yes" AND type="Award / Commendation"',
		       	'order' => 'date DESC' ) );

			// Execute the DB query
			$result = $this->DB->execute();

			// Set up the array
			$award_record_entries = array();

			// Set our loop counters
			$award_count = 0;

			// Loop over the results and add them to the soldier array
			while( $r = $this->DB->fetch( $result ) )
			{
				// Format the date
				$r['date'] = strftime($this->settings['clock_short2'], $r['date']);	

				// Get the award image
				$image = $this->DB->buildAndFetch( array ( 'select' => 'award_image as image', 
					'from' => $this->settings['perscom_database_awards'], 
					'where' => 'title="'.$r['award'].'"' ) );	

				// Up the counter
				$award_count++;

				// Add the citation to the award record
				$r['html'] = '<a href="#" id="a_award_'.$award_count.'" award="'.$r['award'].'" count="'.$award_count.'" class="award" image="'.$this->settings['board_url'].'/images/perscom/medals/medal_ribbon/'.$image['image'].'">Click to view Citation</a>';

				// Set count
				$r['count'] = $award_count;

				// Add the members on end of the array
				array_push($award_record_entries, $r);
			}

			// Return the award record entries
			return $award_record_entries;
		}

		// Return nothing
		return NULL;
	}

	public function loadCombatRecord($id = '') {

		// Make sure the id is set
		if (isset($id) && $id != '') {
			
			// Query the database and get all the service record entries for the soldier
			$this->DB->build( array ( 'select' => '*', 
				'from' => $this->settings['perscom_database_records'], 
				'where' => 'member_id="' . $id . '" AND display="Yes" AND type="Combat Record Entry"',
		       	'order' => 'date DESC' ) );

			// Execute the DB query
			$result = $this->DB->execute();

			// Set up the array
			$combat_record_entries = array();

			// Loop over the results and add them to the soldier array
			while( $r = $this->DB->fetch( $result ) )
			{
				// Format the date
				$r['date'] = strftime($this->settings['clock_short2'], $r['date']);	

				// Add the members on end of the array
				array_push($combat_record_entries, $r);
			}

			// Return the award record entries
			return $combat_record_entries;
		}

		// Return nothing
		return NULL;
	}

	public function loadSoldierForumData($id = '') {

		// Make sure the id is set
		if (isset($id) && $id != '') {

			// Load member
			$member = IPSMember::load( $id );

			// If we get a member
			if ($member) {
			
				// Set the soldier array
				$soldier = array();
				$soldier['forum_name'] = $member['members_display_name'];
				$soldier['title'] = $member['title'];

				// Return the soldier
				return $soldier;
			}

			// Return nothing
			return NULL;
		}

		// Return nothing
		return NULL;
	}

	public function loadCitationSigningSoldier() {

		// Create the signing soldier
		return $this->DB->buildAndFetch( array( 'select' => 'p.firstname, p.lastname, p.rank, p.position',
							'from' =>  array( $this->settings['perscom_database_personnel_files'] => 'p' ),	
							'where' => 'p.member_id="' . $this->settings['perscom_signing_soldier'] . '"',
	       					'add_join' => array(
										array(
											'select'	=> 'r.title AS rank_long',
											'from'		=> array( $this->settings['perscom_database_ranks']  => 'r' ),
											'where'		=> 'r.primary_id_field=p.rank',
											'type'		=> 'left',
										)
									)
								)
							);
	}

	public function loadSupervisor($id = '') {

		// Make sure the id is set
		if (isset($id) && $id != '') {

			// Create the signing soldier
			return $this->DB->buildAndFetch( array( 'select' => 'p.firstname as supervisor_firstname, p.lastname as supervisor_lastname, p.rank',
								'from' =>  array( $this->settings['perscom_database_personnel_files'] => 'p' ),	
								'where' => 'p.member_id="' . $id . '"',
		       					'add_join' => array(
											array(
												'select'	=> 'r.abbreviation AS supervisor_rank_short',
												'from'		=> array( $this->settings['perscom_database_ranks']  => 'r' ),
												'where'		=> 'r.primary_id_field=p.rank',
												'type'		=> 'left',
											)
										)
									)
								);
		}

		// Return nothing
		return NULL;
	}

	public function loadRecruitingMediums() {

		// Get the setting and format into an array, removing all the whitespaces
		$mediums = array_map('trim', array_filter(explode(',', $this->settings['perscom_recruiting_mediums'])));

		// Sort the array
		sort($mediums, SORT_STRING);

		// Return the array
		return $mediums;
	}

	public function loadUniforms() {

		// Create array
		$uniforms = array();

		// Loop through the uniforms folder directory
		foreach(glob(getcwd().'/images/perscom/uniforms/dress_blue/*.*') as $file) {
    	
    		// Add onto the end of the array
    		array_push($uniforms, basename($file));	
		}

		// Return the array
		return $uniforms;
	}
}