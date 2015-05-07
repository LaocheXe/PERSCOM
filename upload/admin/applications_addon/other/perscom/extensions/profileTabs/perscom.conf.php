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

/**
* Plug in name (Default tab name)
*/
ipsRegistry::getClass( 'class_localization' )->loadLanguageFile( array( 'public_lang' ), 'perscom' );
ipsRegistry::getClass( 'class_localization' )->loadLanguageFile( array( 'public_profile' ), 'perscom' );
$CONFIG['plugin_name'] = ipsRegistry::getClass('class_localization')->words['perscom_tab'];

/**
* Language string for the tab
*/
$CONFIG['plugin_lang_bit'] = 'public_profile';

/**
* Plug in key (must be the same as the main {file}.php name
*/
$CONFIG['plugin_key'] = 'perscom';

/**
* Show tab?
*/
$CONFIG['plugin_enabled'] = IPSLib::appIsInstalled('perscom') ? ipsRegistry::$settings['perscom_enable_profile_pfile'] ? 1 : 0 : 0;

/**
* Order
*/
$CONFIG['plugin_order'] = 10;