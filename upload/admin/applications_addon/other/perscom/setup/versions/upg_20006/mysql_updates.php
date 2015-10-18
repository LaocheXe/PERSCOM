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

$SQL[] = "ALTER TABLE perscom_combat_units ADD (retired tinyint(1) NOT NULL DEFAULT '0', loa tinyint(1) NOT NULL DEFAULT '0')";

?>