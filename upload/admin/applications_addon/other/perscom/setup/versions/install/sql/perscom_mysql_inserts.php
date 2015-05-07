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

ipsRegistry::DB()->buildAndFetch(array('select' => '*', 'from' => 'perscom_awards'));
if ( !ipsRegistry::DB()->GetTotalRows() )
{
    $INSERT[] = "INSERT INTO `perscom_awards` (`primary_id_field`, `title`, `award_image`, `history`, `prerequisites`, `type`, `order`) VALUES(1, 'Army Service Ribbon, ASR', 'ASR.png', 'The Army Service ribbon was established on April 10, 1981, by the Secretary of the Army. The Army Service ribbon is multi-colored to represent the entire spectrum of military specialties in which officers and enlisted soldiers may enter upon completion of their initial training.', 'Enter award requirements here.', 'Ribbon', 1);";
    $INSERT[] = "INSERT INTO `perscom_awards` (`primary_id_field`, `title`, `award_image`, `history`, `prerequisites`, `type`, `order`) VALUES(2, 'Presidential Unit Citation, PUC', 'PUC.png', 'The Distinguished Unit Citation was established as a result of Executive Order No. 9075, dated 26 February 1942. The Executive Order directed the Secretary of War to issue citations in the name of the President of the United States to Army units for outstanding performance of duty after 7 December 1941. The design submitted by the Office of the Quartermaster General was approved by the G1 on 30 May 1942. The Distinguished Unit Citation was redesignated the Presidential Unit Citation (Army) per DF, DCSPER, date 3 November 1966. The Presidential Unit Citation is the highest unit decoration which may be bestowed upon a U.S. Army unit.', 'Enter citation requirements here.', 'Citation', 1);";
    $INSERT[] = "INSERT INTO `perscom_awards` (`primary_id_field`, `title`, `award_image`, `history`, `prerequisites`, `type`, `order`) VALUES(3, 'Combat Infantryman Badge, CIB', 'CIB1.png', 'The Combat Infantryman Badge (CIB) is a United States Army military award. The badge is awarded to infantrymen and Special Forces Soldiers in the rank of Colonel and below, who personally fought in active ground combat while assigned as members of either an infantry, Ranger or Special Forces unit, of brigade size or smaller, any time after 6 December 1941. The CIB and its non-combat contemporary, the Expert Infantryman Badge (EIB) were simultaneously created during World War II to enhance the morale and prestige of service in the infantry. Specifically, it recognizes the inherent sacrifices of all infantrymen, and that, in comparison to all other military occupational specialties, infantrymen face the greatest risk of being wounded or killed in action.', 'Enter badge requirements here.', 'Badge', 1);";
    $INSERT[] = "INSERT INTO `perscom_awards` (`primary_id_field`, `title`, `award_image`, `history`, `prerequisites`, `type`, `order`) VALUES(4, 'Defense Distinguished Service Medal, DDSM', 'DDSM.png', 'The Defense Distinguished Service Medal (DDSM) shall only be awarded to officers of the Armed Forces of the United States whose exceptional performance of duty and contributions to national security or defense have been at the highest levels. Such officers have direct and ultimate responsibility for a major activity or program that significantly influences the policies of the U.S. Government. Only under the most unusual circumstances will the DDSM be awarded as an impact award for outstanding TDY achievement. The DDSM is specifically intended to recognize exceptionally distinguished service and to honor an individual''s accomplishments over a sustained period.', 'Enter medal requirements here.', 'Medal', 3);";
}

ipsRegistry::DB()->buildAndFetch(array('select' => '*', 'from' => 'perscom_ranks'));
if ( !ipsRegistry::DB()->GetTotalRows() )
{
    $INSERT[] = "INSERT INTO `perscom_ranks` (`primary_id_field`, `order`, `title`, `abbreviation`, `pay_grade`, `prerequisites`, `description`) VALUES(1, 1  , 'Private First Class', 'PFC', 'E-3', 'Enter prerequisites of Private First Class promotion rank here.', 'Enter description of Private First Class here.');";
}

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

ipsRegistry::DB()->buildAndFetch(array('select' => '*', 'from' => 'perscom_weapons'));
if ( !ipsRegistry::DB()->GetTotalRows() )
{
    $INSERT[] = "INSERT INTO `perscom_weapons` (`primary_id_field`, `make_and_model`, `caliber`, `weight`, `magazine`, `introduction_date`, `fire_type`, `rate_of_fire`, `effective_firing_range`, `image`, `barrel_length`, `action`, `muzzle_velocity`, `sights`) VALUES(1, 'M4A1 Assault Rifle', '5.56×45mm NATO', '6.36 lb (2.88 kg) empty 7.5 lb (3.4 kg) with 30 rounds', '30 round box magazine or other STANAG magazines.', '757441020', 'Semi, Automatic', '700-950 round/min cyclic', '500m for a point target and 600m for an area target.', 'm4a1.png', '14.5 in (370 mm)', 'Gas-operated, rotating bolt (Direct impingement)', '2,900 ft/s (880 m/s)', 'Iron or various optics');";
    $INSERT[] = "INSERT INTO `perscom_weapons` (`primary_id_field`, `make_and_model`, `caliber`, `weight`, `magazine`, `introduction_date`, `fire_type`, `rate_of_fire`, `effective_firing_range`, `image`, `barrel_length`, `action`, `muzzle_velocity`, `sights`) VALUES(2, 'M16A4 Assault Rifle', '5.56×45mm NATO', '7.18 lb (3.26 kg) (unloaded) 8.79 lb (4.0 kg) (loaded)', '30 rounds', '-347130000', 'Semi, Burst', '12–15 rounds/min sustained 45–60 rounds/min semi-automatic 700–950 rounds/min cyclic', '550 meters (point target) 800 meters (area target)', 'm16a4.png', '20 in (508 mm)', 'Gas-operated, rotating bolt (Direct impingement)', '3,110 ft/s (948 m/s)', 'Iron');";
    $INSERT[] = "INSERT INTO `perscom_weapons` (`primary_id_field`, `make_and_model`, `caliber`, `weight`, `magazine`, `introduction_date`, `fire_type`, `rate_of_fire`, `effective_firing_range`, `image`, `barrel_length`, `action`, `muzzle_velocity`, `sights`) VALUES(3, 'M249 Light Machine Gun', '5.56×45 mm NATO', '7.5 kg (17 lb) empty, 10 kg (22 lb) loaded', 'M27 linked belt, STANAG magazine', '189367500', 'Automatic', 'Sustained rate of fire: 100 RPM Rapid rate of fire: 200 RPM Cyclic rate of fire: 800 RPM', '700 m (770 yd) (point target) 3,600 m (3,940 yd) (maximum range)', 'm249.png', '465 mm (18 in)', 'Gas-operated, open bolt', '915 m/s (3,000 ft/s)', 'Iron or various optics');";
    $INSERT[] = "INSERT INTO `perscom_weapons` (`primary_id_field`, `make_and_model`, `caliber`, `weight`, `magazine`, `introduction_date`, `fire_type`, `rate_of_fire`, `effective_firing_range`, `image`, `barrel_length`, `action`, `muzzle_velocity`, `sights`) VALUES(4, 'M24 SWS', '7.62x51mm NATO (M24A1), .300 Winchester Magnum (M24A2), .338 Lapua Magnum (M24A3)', '5.4 kg (11.88 lbs) empty, w/. sling, without scope (M24) 7.3 kg (16 lbs) max weight with day optical sight, sling swivels, carrying strap, fully loaded magazine[1] 5.6 kg (12.32 lbs) empty, w/. sling, without scope (M24A3).', '5-round internal magazine (M24A1), 10-round detachable box magazine (M24A2), 5-round detachable box magazine (M24A3)', '568058820', 'Semi', '20 rpm', '800 metres (875 yd) (7.62×51mm) 1,500 metres (1,640 yd) (.338 Lapua Magnum)', 'm24.png', '660.4 mm (24 in)(M24A1, M24A2); 685.8 mm (27 in) (M24A3)', 'Bolt-action', '2,580 ft/s (790 m/s) w/M118LR Sniper load (175 gr.)', 'Telescopic; detachable backup iron sights');";
}

ipsRegistry::DB()->buildAndFetch(array('select' => '*', 'from' => 'perscom_combat_units'));
if ( !ipsRegistry::DB()->GetTotalRows() )
{
    $INSERT[] = "INSERT INTO `perscom_combat_units` (`primary_id_field`, `name`, `unit_position`, `nickname`, `order`, `forum_usergroup`) VALUES(1, 'Alpha Company, 1st Platoon, 1st Squad', '1/1/A', 'Scorpions', 1, 4);";
}

?>