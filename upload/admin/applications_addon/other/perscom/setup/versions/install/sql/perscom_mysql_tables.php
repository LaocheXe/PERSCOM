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


include 'conf_global.php';

$TABLE[] = "CREATE TABLE `perscom_admin_units` (
  `primary_id_field` int(11) NOT NULL auto_increment,
  `name` text,
  `mos` mediumtext,
  `image` text,
  `responsibilities` mediumtext,
  `prerequisites` mediumtext,
  `forum_usergroup` text,
  PRIMARY KEY(primary_id_field)
);";

$TABLE[] = "CREATE TABLE `perscom_awards` (
  `primary_id_field` int(11) NOT NULL auto_increment,
  `title` text,
  `award_image` text,
  `history` mediumtext,
  `prerequisites` mediumtext,
  `type` text,
  `order` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY(primary_id_field)
);";

$TABLE[] = "CREATE TABLE `perscom_combat_units` (
  `primary_id_field` int(11) NOT NULL auto_increment,
  `name` text,
  `unit_position` text,
  `nickname` text,
  `order` int(11) NOT NULL DEFAULT '0',
  `forum_usergroup` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY(primary_id_field)
);";

$TABLE[] = "CREATE TABLE `perscom_leave_of_absences` (
  `primary_id_field` int(11) NOT NULL auto_increment,
  `member_id` int(11) NOT NULL DEFAULT '0',
  `display_name` text,
  `created_date` text,
  `start_date` text,
  `end_date` text,
  `explanation` text,
  `status` text,
  `approved_member_id` int(11) NOT NULL DEFAULT '0',
  `approved_display_name` text,
  `approved_date` text,
  `combat_unit_id` text,
  `returned` text,
  PRIMARY KEY(primary_id_field)
);";

$TABLE[] = "CREATE TABLE `perscom_muster_logs` (
  `primary_id_field` int(11) NOT NULL auto_increment,
  `member_id` int(11) NOT NULL DEFAULT '0',
  `display_name` text,
  `date` text,
  PRIMARY KEY(primary_id_field)
);";

$TABLE[] = "CREATE TABLE `perscom_personnel` (
  `primary_id_field` int(11) NOT NULL auto_increment,
  `member_id` int(11) DEFAULT '0',
  `firstname` text,
  `lastname` text,
  `status` int(11) NOT NULL DEFAULT '0',
  `rank` int(11) NOT NULL DEFAULT '0',
  `position` text,
  `mos` text,
  `combat_unit` int(11) NOT NULL DEFAULT '0',
  `supervisor` int(11) NOT NULL DEFAULT '0',
  `admin_unit` text,
  `timezone` text,
  `weapon` int(11) NOT NULL DEFAULT '0',
  `steam` text,
  `country` text,
  `email` text,
  `enlistment_date` text,
  `induction_date` text,
  `promotion_date` text,
  `recruiter` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY(primary_id_field)
);";

$TABLE[] = "CREATE TABLE `perscom_ranks` (
  `primary_id_field` int(11) NOT NULL auto_increment,
  `order` int(11) NOT NULL DEFAULT '0',
  `title` text,
  `abbreviation` text,
  `pay_grade` text,
  `prerequisites` mediumtext,
  `description` mediumtext,
  PRIMARY KEY(primary_id_field)
);";

$TABLE[] = "CREATE TABLE `perscom_requests` (
  `primary_id_field` int(11) NOT NULL auto_increment,
  `member_id` int(11) NOT NULL DEFAULT '0',
  `members_display_name` text,
  `date` int(10) NOT NULL DEFAULT '0',
  `description` text,
  `type` text,
  `administrator_member_id` int(11) NOT NULL DEFAULT '0',
  `administrator_members_display_name` text,
  `status` text,
  `relational_primary_id_field` text,
  PRIMARY KEY(primary_id_field)
);";

$TABLE[] = "CREATE TABLE `perscom_service_records` (
  `primary_id_field` int(11) NOT NULL auto_increment,
  `member_id` int(11) NOT NULL DEFAULT '0',
  `members_display_name` text,
  `date` text,
  `entry` text,
  `type` text,
  `award` text,
  `rank` text,
  `citation` text,
  `discharge_grade` text,
  `position` text,
  `combat_unit` text,
  `display` text,
  PRIMARY KEY(primary_id_field)
);";

$TABLE[] = "CREATE TABLE `perscom_settings` (
  `primary_id_field` int(11) NOT NULL auto_increment,
  `title` text CHARACTER SET latin1,
  `key` text CHARACTER SET latin1,
  `value` int(11) NOT NULL DEFAULT '0',
  `description` text CHARACTER SET latin1,
  PRIMARY KEY(primary_id_field)
);";

$TABLE[] = "CREATE TABLE `perscom_status` (
  `primary_id_field` int(11) NOT NULL auto_increment,
  `status` text,
  `hex_color` text,
  PRIMARY KEY(primary_id_field)
);";

$TABLE[] = "CREATE TABLE `perscom_temporary_pass_requests` (
  `primary_id_field` int(11) NOT NULL auto_increment,
  `member_id` int(11) NOT NULL DEFAULT '0',
  `display_name` text,
  `explanation` text,
  `expiration` text,
  PRIMARY KEY(primary_id_field)
);";

$TABLE[] = "CREATE TABLE `perscom_weapons` (
  `primary_id_field` int(11) NOT NULL auto_increment,
  `make_and_model` text,
  `caliber` text,
  `weight` text,
  `magazine` text,
  `introduction_date` text,
  `fire_type` text,
  `rate_of_fire` text,
  `effective_firing_range` text,
  `image` text,
  `barrel_length` text,
  `action` text,
  `muzzle_velocity` text,
  `sights` text,
  PRIMARY KEY(primary_id_field)
);";

?>
