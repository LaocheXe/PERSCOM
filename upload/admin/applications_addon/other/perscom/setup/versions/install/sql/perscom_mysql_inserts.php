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

$INSERT = array();

ipsRegistry::DB()->buildAndFetch(array('select' => '*', 'from' => 'perscom_settings'));
if ( !ipsRegistry::DB()->GetTotalRows() )
{
    $INSERT[] = "INSERT INTO `perscom_settings` (`primary_id_field`, `title`, `key`, `value`, `description`) VALUES(1, 'Accepted Applications', 'accepted_applications', 0, 'A count of all the accepted applications in PERSCOM.');";
    $INSERT[] = "INSERT INTO `perscom_settings` (`primary_id_field`, `title`, `key`, `value`, `description`) VALUES(2, 'Denied Applications', 'denied_applications', 0, 'A count of all the denied applications in PERSCOM.');";
    $INSERT[] = "INSERT INTO `perscom_settings` (`primary_id_field`, `title`, `key`, `value`, `description`) VALUES(3, 'Dropped Applications', 'dropped_applications', 0, 'A count of all the dropped applications in PERSCOM.');";
    $INSERT[] = "INSERT INTO `perscom_settings` (`primary_id_field`, `title`, `key`, `value`, `description`) VALUES(4, 'Total Applications', 'total_applications', 0, 'A count of all applications inputted into PERSCOM.');";
    $INSERT[] = "INSERT INTO `perscom_settings` (`primary_id_field`, `title`, `key`, `value`, `description`) VALUES(5, 'Last Enlistment Statistics Reset', 'enlistment_statistics_reset', 1420099668, 'The date the enlistment statistics were last reset.');";
}

ipsRegistry::DB()->buildAndFetch(array('select' => '*', 'from' => 'perscom_status'));
if ( !ipsRegistry::DB()->GetTotalRows() )
{
    $INSERT[] = "INSERT INTO `perscom_status` (`primary_id_field`, `status`, `hex_color`) VALUES(1, 'Active', '006633');";
    $INSERT[] = "INSERT INTO `perscom_status` (`primary_id_field`, `status`, `hex_color`) VALUES(2, 'Inactive', 'CC0000');";
    $INSERT[] = "INSERT INTO `perscom_status` (`primary_id_field`, `status`, `hex_color`) VALUES(3, 'Away Without Leave', 'CC0000');";
    $INSERT[] = "INSERT INTO `perscom_status` (`primary_id_field`, `status`, `hex_color`) VALUES(4, 'Leave of Absence', '003399');";
    $INSERT[] = "INSERT INTO `perscom_status` (`primary_id_field`, `status`, `hex_color`) VALUES(5, 'New Application', '000000');";
    $INSERT[] = "INSERT INTO `perscom_status` (`primary_id_field`, `status`, `hex_color`) VALUES(9, 'Retired', '003399');";
}

?>