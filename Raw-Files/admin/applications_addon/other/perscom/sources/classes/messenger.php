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

class perscom_messenger_wrapper {

	/**
	 * Constructor
	 *
	 * @param       object          ipsRegistry
	 */
	public function __construct( ipsRegistry $registry )
	{
		/* Make object */
		$this->registry = $registry;
		$this->DB       = $this->registry->DB();
		$this->settings =& $this->registry->fetchSettings();
		$this->request  =& $this->registry->fetchRequest();
		$this->lang     = $this->registry->getClass('class_localization');
		$this->member   = $this->registry->member();
		$this->memberData =& $this->registry->member()->fetchMemberData();
		$this->cache    = $this->registry->cache();
		$this->caches   =& $this->registry->cache()->fetchCaches();
	}	

	/**
	 * sendPrivateMessage
	 *
	 * @param       messageData          An array of data for composing the notification.
	 */
	public function sendPrivateMessage( $messageData ) {

		// Make sure we have some message data
		if (!is_array($messageData) || count($messageData) == 0) {

			// Return
			return FALSE;
		}

		//-----------------------------------------
		// Messaging library
		//-----------------------------------------
	   	$classToLoad = IPSLib::loadLibrary( IPSLib::getAppDir( 'members' ) . '/sources/classes/messaging/messengerFunctions.php', 'messengerFunctions', 'members' );
		$this->messengerFunctions = new $classToLoad( $this->registry );

    	// Try sending the message
    	try {

			// Try and send the private message
			return $this->messengerFunctions->sendNewPersonalTopic( 
				$messageData['to'],
				$messageData['from'],
				array(),
				$messageData['title'],
				$messageData['text'],
				array( 'isDraft'  => false,
					'topicID'  => 0,
					'isSystem' => TRUE,
					'sendMode' => 'invite',
		 			'postKey'  => md5( uniqid( microtime(), true ) ) ) );
		}

		// Catch the error if one occurs
		catch( Exception $error ){
			
			// Return the error
			return $error;
		}
	}
}