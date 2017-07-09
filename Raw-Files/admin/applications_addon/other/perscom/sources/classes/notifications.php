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

class perscom_notifications_wrapper {

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
	 * sendNotification
	 *
	 * @param       notificationData          An array of data for composing the notification.
	 */
	public function sendNotification( $notificationData ) {

		// Make sure we have some notification data
		if (!is_array($notificationData) || count($notificationData) == 0) {

			// Return
			return FALSE;
		}

		//-----------------------------------------
		// Notifications library
		//-----------------------------------------
		$classToLoad = IPSLib::loadLibrary( IPS_ROOT_PATH . '/sources/classes/member/notifications.php', 'notifications' );
		$notifyLibrary = new $classToLoad( $this->registry );

		// Set the notification properties
		$notifyLibrary->setMember( $notificationData['to'] );
		$notifyLibrary->setFrom( $notificationData['from'] );
		$notifyLibrary->setNotificationKey( isset($notificationData['key']) && $notificationData['key'] != '' ? $notificationData['key'] : 'perscom_notification' );
		$notifyLibrary->setNotificationText( $notificationData['text'] );
		$notifyLibrary->setNotificationTitle( $notificationData['title'] );

		// Try and send the notification
		try {
			
			// Send the notifications and return the bool value
			return $notifyLibrary->sendNotification();
		}

		// Catch the exception
		catch( Exception $e ){

			// Return the exception
			return $e;
		}
	}
}