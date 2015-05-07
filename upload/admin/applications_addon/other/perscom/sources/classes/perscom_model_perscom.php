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

class perscom_model_perscom {	
	
	public function __construct(ipsRegistry $registry) {

		$this->registry = $registry;
		$this->DB = $registry->DB();
		$this->member = $this->registry->member();
		$this->memberData =& $this->registry->member()->fetchMemberData();
		$this->settings = $this->registry->settings();
	}
}