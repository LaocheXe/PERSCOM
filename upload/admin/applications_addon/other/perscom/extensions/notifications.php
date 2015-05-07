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

/* Load a language file to define the strings we will need */
ipsRegistry::getClass('class_localization')->loadLanguageFile( array( 'public_notifications' ), 'perscom' );

class perscom_notifications
{
	public function getConfiguration()
	{
		/**
		 * Notification types
		 */
		$_NOTIFY = array(
						array( 'key' => 'perscom_notification', 'default' => array( 'inline' ), 'disabled' => array(), 'icon' => 'notify_newtopic' ),
						array( 'key' => 'perscom_notification_muster', 'default' => array( 'inline' ), 'disabled' => array(), 'icon' => 'notify_newtopic' ),	
						array( 'key' => 'perscom_notification_tpr', 'default' => array( 'inline' ), 'disabled' => array(), 'icon' => 'notify_newtopic' ),									
					);

		// Return the notification data array
		return $_NOTIFY;
	}
}