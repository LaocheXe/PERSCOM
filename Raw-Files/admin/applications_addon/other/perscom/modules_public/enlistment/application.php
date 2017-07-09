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

class public_perscom_enlistment_application extends ipsCommand
{
	public function doExecute( ipsRegistry $registry )
	{
		// Load our language files
		$this->registry->class_localization->loadLanguageFile( array( 'public_enlistment' ) );
		$this->registry->class_localization->loadLanguageFile( array( 'public_notifications' ) );

		// Get perscom classes
		$this->notifications = $this->registry->notifications;
		$this->messenger = $this->registry->messenger;

		// Check to make sure the member is logged in
		$member = $this->registry->member()->fetchMemberData();

		// If the member does not have an ID
		if ($member['member_id'] == '0') {
			
			// Throw login error
			$this->registry->output->showError( 'You must have an account and be logged in to submit an application.', 000123, false, '', 403 );
		}

		// If the tag class is not loaded
		if (!$this->registry->isClassLoaded('tags'))
		{
			// Load the tag class
			require_once( IPS_ROOT_PATH . 'sources/classes/tags/bootstrap.php' );
			$this->registry->setClass( 'tags', classes_tags_bootstrap::run( 'forums', 'topics' ) );
		}
		
		// Handle HTTP request
		switch ( $this->request['do'] )
		{
			case 'submit':

				// Submit the application
				$this->submitEnlistmentApplication();

			break;

			default:

				// Bring  up a new application
				$this->newEnlistmentApplication();

			break;
		}

		// Output the HTML
       	$this->registry->output->sendOutput();
	}

	public function newEnlistmentApplication () {

		// Get our supported games
		$this->getSupportedGames();

		// Set Title
		$this->registry->output->setTitle( $this->lang->words['enlistment_application'] );

		// Add Navigation
		$this->registry->output->addNavigation( $this->lang->words['enlistment_application'], NULL );

		// Output HTML
		$this->registry->output->addContent( $this->registry->output->getTemplate('perscom')->viewEnlistmentApplication( $this->supported_games ) );
	}

	public function submitEnlistmentApplication () {

		// Check to make sure they user submitting the app does not already have an perscom file alredy
		$personnel_file = $this->DB->buildAndFetch( array( 'select' => '*', 
			'from' => $this->settings['perscom_database_personnel_files'], 
			'where' => 'member_id="' . $this->memberData['member_id'] . '"') );

		// If we get a personnel file
		if (!$personnel_file) {
		
			// Post application to forum
			$topic_id = $this->postApplicationToForum();

			// Make sure we get a topic id
			if (!isset($topic_id) || $topic_id == '') {
				
				// Throw error
				$this->registry->output->showError( 'The application was not able to successfully submit. Please make sure you have selected a valid forum in PERSCOM settings.', 000123, false, '', 403 );
			}

			// We got a valid topic id
			else {

				// Send Private Message
				$this->sendPrivateMessage();
				
				// Send Private Message to RRO Staff
				$this->sendPrivateMessageToStaff( $topic_id );

				// Add user to Enlistees group
				$this->addToEnlisteesGroup();

				// Add the personnel file
				$this->createPersonnelFile( $topic_id );

				// Update the enlistment statistics
				$this->updateEnlistmentStatistics();	
			}
		}

		// The user submitting the app already has a personnel file
		else {

			// Throw login error
			$this->registry->output->showError( 'You already have a personnel file in PERSCOM. This could be because you have already submitted an enlistment application or you are a current/retired member of the ' . $this->settings['board_name'] . '.', 000123, false, '', 403 );
		}

		// Set Title
		$this->registry->output->setTitle( $this->lang->words['enlistment_application'] );

		// Add Navigation
		$this->registry->output->addNavigation( $this->lang->words['thank_you'], NULL );

		// Output HTML
		$this->registry->output->addContent( $this->registry->output->getTemplate('perscom')->viewEnlistmentApplicationSubmit() );
	}

	public function updateEnlistmentStatistics () {

		// Update the total applications
		$this->DB->update( $this->settings['perscom_database_settings'], '`value`=`value`+1', '`key`="total_applications"', false, true );
	}

	public function postApplicationToForum () {

		// Post application to Candidate Files
		ipsRegistry::getAppClass( 'forums' );

		// Load the forum posting class
		$classToLoad = IPSLib::loadLibrary( IPSLib::getAppDir( 'forums' ) . '/sources/classes/post/classPost.php', 'classPost', 'forums' );
		$classToLoad = IPSLib::loadLibrary( IPSLib::getAppDir( 'forums' ) . '/sources/classes/post/classPostForms.php', 'classPostForms', 'forums' );
		$this->post = new $classToLoad( $this->registry );

		// If the email is disabled
		if (!$this->settings['perscom_enable_email']) {
			
			// Remove user's email
			$this->memberData['email'] = "Email Disabled";
		}

		// Set Message Data
		$message = "<p style=\"text-align:center;\"><span style=\"font-size:18px;\"><strong>Enlistment Application from " . $this->request['first_name'] . "&nbsp;" . $this->request['last_name'] . "</strong></span></p>
					<p style=\"text-align:center;\">&nbsp;</p>
					<p><strong>Game: </strong>" . $this->request['game'] . "</p>
					<p>&nbsp;</p>
					<p>----- Personal Information -----</p>
					<p>&nbsp;</p>
					<p><strong>First Name: </strong>" . ucfirst(strtolower($this->request['first_name'])) . "</p>
					<p><strong>Last Name: </strong>" . ucfirst(strtolower($this->request['last_name'])) . "</p>
					<p><strong>Email: </strong>" . $this->memberData['email'] . "</p>
					<p><strong>Date of Birth: </strong>" . $this->request['date_of_birth'] . "</p>
					<p><strong>Age: </strong>" . $this->request['age'] . "</p>
					<p><strong>Country: </strong>" . $this->request['country'] . "</p>
					<p><strong>Timezone: </strong> " . $this->request['timezone'] . "</p>
					<p>&nbsp;</p>
					<p>----- Game Information -----</p>
					<p>&nbsp;</p>
					<p><strong>Current In-Game Name: </strong>" . $this->request['in_game_name'] . "</p>
					<p><strong>Steam ID: </strong><a href=\"http://steamidconverter.com/" . $this->request['steam_id'] . "\">" . $this->request['steam_id'] . "</a></p>
					<p><strong>Player ID: </strong>" . $this->request['player_id'] . "</p>
					<p><strong>Previous Clans: </strong>" . $this->request['previous_clans_radio'] . "</p>
					<p><strong>Previous Clans List: </strong>" . $this->request['previous_clans'] . "</p>
					<p>&nbsp;</p>
					<p>----- Communications -----</p>
					<p>&nbsp;</p>
					<p><strong>Teamspeak 3: </strong>" . $this->request['teamspeak3'] . "</p>
					<p><strong>Microphone: </strong>" . $this->request['working_microphone'] . "</p>
					<p>&nbsp;</p>
					<p>----- Unit Related Questions -----</p>
					<p>&nbsp;</p>
					<p><strong>Commitment Time: </strong>" . $this->request['commitment_time'] . "</p>
					<p><strong>Reasons for joining: </strong>" . $this->request['two_reasons'] . "</p>
					<p><strong>How did they hear about us: </strong>" . $this->request['hear_about_us'] . "</p>";

		// Try and post the topic
		try {

			// Set post settings
    		$this->post->setBypassPermissionCheck( true );
   			$this->post->setIsAjax( false );
    		$this->post->setPublished( true );
   			$this->post->setForumID( $this->settings['perscom_application_submission_forum'] );
    		$this->post->setAuthor( $this->memberData['member_id'] );
    		$this->post->setPostContentPreFormatted( $message );
    		$this->post->setTopicTitle( sprintf( 'New %1$s Enlistment Application From %2$s %3$s', $this->request['game'], $this->request['first_name'], $this->request['last_name'] ) );
    		$this->post->setSettings( array( 'enableSignature' => 0,
                              				 'enableEmoticons' => 0,
                              				 'post_htmlstatus' => 0,
                              				 'enableTracker' => 1 ) );

			// Post the topic and print if we get an error
   			if ($this->post->addTopic() === false) {

				// Return false
       			return false;
    		}

			// Get the topic data
			$topic = $this->post->getTopicData();
			
			// Return the topic ID
			return $topic['tid'];
		}
		
		// Catch the exception
		catch( Exception $e ) {

			// Return false
    		return false;
		}
	}

	public function sendPrivateMessage () {

		// Load the author
		$author = IPSMember::load( $this->settings['perscom_application_submission_author'], 'all', 'username'  );

		// Send the private message
		$this->messenger->sendPrivateMessage( array (
			'to' => $this->memberData['member_id'], 
			'from' => $author['member_id'], 
			'title' => 'Thank you for submitting an application with the' . $this->settings['board_name'], 
			'text' => sprintf( $this->lang->words['notification_application_submission_private_message'], $this->request['first_name'] ) ) );
	}
	
	public function sendPrivateMessageToStaff( $topic_id ) {
		
		// Query the DB to look for everyone in the defined RRO group
		$this->DB->build( array( 'select' => 'members_display_name, member_id', 
			'from' => 'members', 
			'where' => 'find_in_set(' . $this->settings['perscom_recruiting_and_retention_group'] . ',mgroup_others)' ) );

		// Execute the DB query
		$result = $this->DB->execute();

		// If we get more than 0 results
		if ($result->num_rows > 0) {

			// Loop through the results and create an array of RRO soldiers
			while( $r = $this->DB->fetch( $result ) )
			{
				// Send the notification
				$this->notifications->sendNotification( array (
					'to' => $r['member_id'], 
					'from' => $this->settings['perscom_application_submission_author'], 
					'title' => 'PERSCOM: New Application', 
					'text' => sprintf( $this->lang->words['notification_application_submission_rro'], $this->settings['base_url'] . 'showtopic=' . $topic_id ) ) );
			}
		}
	}

	public function addToEnlisteesGroup () {

		// Get current mgroups
		$groups = explode(',', $this->memberData['mgroup_others']);

		// Add the enlistees group id onto the end
		array_push($groups, $this->settings['perscom_application_submission_group']);

		// Save the new data
		IPSMember::save( $this->memberData['member_id'], array( 'core' => array ( 
			'mgroup_others' =>  implode(',', $groups) ) ) );
	}

	public function getSupportedGames () {

		// Get our supported games from PERSCOM settings to populate the game select field in the application
		$this->supported_games = array_map('trim', array_filter(explode(',' , $this->settings['perscom_supported_games'])));
	}

	public function createPersonnelFile( $topic_id ) {

		// Build query
		$this->DB->insert( $this->settings['perscom_database_personnel_files'], array( 
			'member_id' => $this->memberData['member_id'], 
			'firstname' => ucfirst($this->request['first_name']),
			'lastname' => ucfirst($this->request['last_name']),
			'rank' => '128',
			'position' => 'New Applicant',
			'mos' => '',
			'admin_unit' => '',
			'enlistment_date' => strtotime('now'),
			'timezone' => $this->request['timezone'],
			'combat_unit' => '624',
			'weapon' => '0',
			'steam' => $this->request['steam_id'],
			'country' => $this->request['country'],
			'email' => $this->memberData['email'],
			'status' => '5',
			'induction_date' => '',
			'promotion_date' => '',
			'recruiter' => '0',
			'recruiting_medium' => '',
			'application_topic_id' => $topic_id ) );
	}
}