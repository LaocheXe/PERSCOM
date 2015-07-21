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

// Remove all the previous weapons due to some of them having bad characters
$SQL[] = "TRUNCATE TABLE `perscom_weapons`";

// Add the weapons back without the bad characters
$SQL[] = "INSERT INTO `perscom_weapons` (`primary_id_field`, `make_and_model`, `caliber`, `weight`, `magazine`, `introduction_date`, `fire_type`, `rate_of_fire`, `effective_firing_range`, `image`, `barrel_length`, `action`, `muzzle_velocity`, `sights`) VALUES(1, 'M4A1 Assault Rifle', '5.56x45mm NATO', '6.36 lb (2.88 kg) empty 7.5 lb (3.4 kg) with 30 rounds', '30 round box magazine or other STANAG magazines.', '757441020', 'Semi, Automatic', '700-950 round/min cyclic', '500m for a point target and 600m for an area target.', 'm4a1.png', '14.5 in (370 mm)', 'Gas-operated, rotating bolt (Direct impingement)', '2,900 ft/s (880 m/s)', 'Iron or various optics');";
$SQL[] = "INSERT INTO `perscom_weapons` (`primary_id_field`, `make_and_model`, `caliber`, `weight`, `magazine`, `introduction_date`, `fire_type`, `rate_of_fire`, `effective_firing_range`, `image`, `barrel_length`, `action`, `muzzle_velocity`, `sights`) VALUES(2, 'M16A4 Assault Rifle', '5.56x45mm NATO', '7.18 lb (3.26 kg) (unloaded) 8.79 lb (4.0 kg) (loaded)', '30 rounds', '-347130000', 'Semi, Burst', '12-15 rounds/min sustained 45-60 rounds/min semi-automatic 700-950 rounds/min cyclic', '550 meters (point target) 800 meters (area target)', 'm16a4.png', '20 in (508 mm)', 'Gas-operated, rotating bolt (Direct impingement)', '3,110 ft/s (948 m/s)', 'Iron');";
$SQL[] = "INSERT INTO `perscom_weapons` (`primary_id_field`, `make_and_model`, `caliber`, `weight`, `magazine`, `introduction_date`, `fire_type`, `rate_of_fire`, `effective_firing_range`, `image`, `barrel_length`, `action`, `muzzle_velocity`, `sights`) VALUES(3, 'M249 Light Machine Gun', '5.56x45 mm NATO', '7.5 kg (17 lb) empty, 10 kg (22 lb) loaded', 'M27 linked belt, STANAG magazine', '189367500', 'Automatic', 'Sustained rate of fire: 100 RPM Rapid rate of fire: 200 RPM Cyclic rate of fire: 800 RPM', '700 m (770 yd) (point target) 3,600 m (3,940 yd) (maximum range)', 'm249.png', '465 mm (18 in)', 'Gas-operated, open bolt', '915 m/s (3,000 ft/s)', 'Iron or various optics');";
$SQL[] = "INSERT INTO `perscom_weapons` (`primary_id_field`, `make_and_model`, `caliber`, `weight`, `magazine`, `introduction_date`, `fire_type`, `rate_of_fire`, `effective_firing_range`, `image`, `barrel_length`, `action`, `muzzle_velocity`, `sights`) VALUES(4, 'M24 SWS', '7.62x51mm NATO (M24A1), .300 Winchester Magnum (M24A2)', '5.4 kg (11.88 lbs) empty, w/. sling, without scope (M24) 7.3 kg (16 lbs) max weight with day optical sight, sling swivels, carrying strap, fully loaded magazine[1] 5.6 kg (12.32 lbs) empty, w/. sling, without scope (M24A3).', '5-round internal magazine (M24A1), 10-round detachable box magazine (M24A2), 5-round detachable box magazine (M24A3)', '568058820', 'Automatic', '20 rpm', '800 metres (875 yd) (7.62x51mm) 1,500 metres (1,640 yd) (.338 Lapua Magnum)', 'm24.png', '660.4 mm (24 in)(M24A1, M24A2); 685.8 mm (27 in) (M24A3)', 'Bolt-action', '2,580 ft/s (790 m/s) w/M118LR Sniper load (175 gr.)', 'Telescopic; detachable backup iron sights');";

?>