--
-- Table structure for table `user`
--

CREATE TABLE IF NOT EXISTS `user` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(300) NOT NULL,
  `facebook_id` varchar(300) NOT NULL,
  `access_token` varchar(300) NOT NULL,
  `image` varchar(500) NOT NULL,
  `ios_push_id` varchar(300) NOT NULL,
  `android_push_id` varchar(300) NOT NULL,
  `type` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `status` tinyint(4) NOT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;//

--3-01-2018
CREATE TABLE `master_card` (`master_card_id` int(11) NOT NULL,`title` varchar(100) NOT NULL,`card_type` int(11) NOT NULL,`rarity_type` int(11) NOT NULL,`created_at` datetime NOT NULL,`status` tinyint(2) NOT NULL) ENGINE=InnoDB DEFAULT CHARSET=latin1;INSERT INTO `master_card` (`master_card_id`, `title`, `card_type`, `rarity_type`, `created_at`, `status`) VALUES(1, 'virabhadra', 1, 1, '2018-01-03 00:00:00', 1),(2, 'shakuni', 1, 1, '2018-01-03 00:00:00', 1),(3, 'nakul', 1, 2, '2018-01-03 00:00:00', 1),(4, 'sahadeva', 1, 2, '2018-01-03 00:00:00', 1),(5, 'ghatotkacha', 1, 1, '2018-01-03 00:00:00', 1),(6, 'satyaki', 1, 1, '2018-01-03 00:00:00', 1),(7, 'meghanath', 1, 3, '2018-01-03 00:00:00', 1),(8, 'jatayu', 1, 2, '2018-01-03 00:00:00', 1),(9, 'angad', 1, 2, '2018-01-03 00:00:00', 1),(10, 'jamvanta', 1, 3, '2018-01-03 00:00:00', 1),(11, 'agni astra', 2, 1, '2018-01-03 00:00:00', 1),(12, 'trident', 2, 2, '2018-01-03 00:00:00', 1),(13, 'rock tower', 2, 1, '2018-01-03 00:00:00', 1),(14, 'brahmos building', 2, 2, '2018-01-03 00:00:00', 1),(15, 'tree monkey building', 2, 1, '2018-01-03 00:00:00', 1),(16, 'naga astra', 2, 1, '2018-01-03 00:00:00', 1),(17, 'chakravyuha', 2, 3, '2018-01-03 00:00:00', 1),(18, 'fire hay ball', 2, 2, '2018-01-03 00:00:00', 1),(19, 'vajra', 2, 2, '2018-01-03 00:00:00', 1),(20, 'narayanastra', 2, 1, '2018-01-03 00:00:00', 1);ALTER TABLE `master_card`ADD PRIMARY KEY (`master_card_id`);ALTER TABLE `master_card`MODIFY `master_card_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

ALTER TABLE `master_card` CHANGE `card_type` `card_type` INT(11) NOT NULL COMMENT '1 - character, 2- power';
ALTER TABLE `master_card` CHANGE `rarity_type` `rarity_type` INT(11) NOT NULL COMMENT '1-common, 2-rear,3-ultra rear';

--4-01-2018
ALTER TABLE `user` ADD `google_id` VARCHAR(50) NOT NULL AFTER `facebook_id`;
ALTER TABLE `master_card` ADD `hit_points` INT(11) NOT NULL AFTER `rarity_type`, ADD `damage` INT(11) NOT NULL AFTER `hit_points`, ADD `hit_speed` FLOAT NOT NULL AFTER `damage`, ADD `walk_speed` FLOAT(11) NOT NULL AFTER `hit_speed`;
ALTER TABLE `user` ADD `master_stadium_id` TINYINT(2) NOT NULL AFTER `total_wins`;
ALTER TABLE `master_card` ADD `mana_cost` INT(11) NOT NULL AFTER `walk_speed`;
ALTER TABLE `master_card` CHANGE `hit_points` `hit_points` FLOAT NOT NULL, CHANGE `damage` `damage` FLOAT NOT NULL, CHANGE `mana_cost` `mana_cost` FLOAT NOT NULL;
ALTER TABLE `master_card_level_upgrade` CHANGE `id` `master_card_level_upgrade_id` INT(11) NOT NULL AUTO_INCREMENT;

INSERT INTO `master_card_level_upgrade` (`master_card_level_upgrade_id`, `card_type`, `level_id`, `card_count`, `created_at`, `status`) VALUES (NULL, '2', '2', '9', '2018-01-04 00:00:00', '1');
INSERT INTO `master_card_level_upgrade` (`master_card_level_upgrade_id`, `card_type`, `level_id`, `card_count`, `created_at`, `status`) VALUES (NULL, '2', '3', '15', '2018-01-04 00:00:00', '1');
INSERT INTO `master_card_level_upgrade` (`master_card_level_upgrade_id`, `card_type`, `level_id`, `card_count`, `created_at`, `status`) VALUES (NULL, '2', '4', '22', '2018-01-04 00:00:00', '1');
INSERT INTO `master_card_level_upgrade` (`master_card_level_upgrade_id`, `card_type`, `level_id`, `card_count`, `created_at`, `status`) VALUES (NULL, '2', '5', '30', '2018-01-04 00:00:00', '1');
INSERT INTO `master_card_level_upgrade` (`master_card_level_upgrade_id`, `card_type`, `level_id`, `card_count`, `created_at`, `status`) VALUES (NULL, '2', '6', '39', '2018-01-04 00:00:00', '1');
INSERT INTO `master_card_level_upgrade` (`master_card_level_upgrade_id`, `card_type`, `level_id`, `card_count`, `created_at`, `status`) VALUES (NULL, '2', '7', '50', '2018-01-04 00:00:00', '1');
INSERT INTO `master_card_level_upgrade` (`master_card_level_upgrade_id`, `card_type`, `level_id`, `card_count`, `created_at`, `status`) VALUES (NULL, '2', '8', '62', '2018-01-04 00:00:00', '1');
INSERT INTO `master_card_level_upgrade` (`master_card_level_upgrade_id`, `card_type`, `level_id`, `card_count`, `created_at`, `status`) VALUES (NULL, '2', '9', '75', '2018-01-04 00:00:00', '1');
INSERT INTO `master_card_level_upgrade` (`master_card_level_upgrade_id`, `card_type`, `level_id`, `card_count`, `created_at`, `status`) VALUES (NULL, '2', '10', '90', '2018-01-04 00:00:00', '1');

INSERT INTO `master_card_level_upgrade` (`master_card_level_upgrade_id`, `card_type`, `level_id`, `card_count`, `created_at`, `status`) VALUES (NULL, '3', '2', '12', '2018-01-04 00:00:00', '1');
INSERT INTO `master_card_level_upgrade` (`master_card_level_upgrade_id`, `card_type`, `level_id`, `card_count`, `created_at`, `status`) VALUES (NULL, '3', '3', '20', '2018-01-04 00:00:00', '1');
INSERT INTO `master_card_level_upgrade` (`master_card_level_upgrade_id`, `card_type`, `level_id`, `card_count`, `created_at`, `status`) VALUES (NULL, '3', '4', '31', '2018-01-04 00:00:00', '1');
INSERT INTO `master_card_level_upgrade` (`master_card_level_upgrade_id`, `card_type`, `level_id`, `card_count`, `created_at`, `status`) VALUES (NULL, '3', '5', '45', '2018-01-04 00:00:00', '1');
INSERT INTO `master_card_level_upgrade` (`master_card_level_upgrade_id`, `card_type`, `level_id`, `card_count`, `created_at`, `status`) VALUES (NULL, '3', '6', '60', '2018-01-04 00:00:00', '1');
INSERT INTO `master_card_level_upgrade` (`master_card_level_upgrade_id`, `card_type`, `level_id`, `card_count`, `created_at`, `status`) VALUES (NULL, '3', '7', '78', '2018-01-04 00:00:00', '1');
INSERT INTO `master_card_level_upgrade` (`master_card_level_upgrade_id`, `card_type`, `level_id`, `card_count`, `created_at`, `status`) VALUES (NULL, '3', '8', '90', '2018-01-04 00:00:00', '1');
INSERT INTO `master_card_level_upgrade` (`master_card_level_upgrade_id`, `card_type`, `level_id`, `card_count`, `created_at`, `status`) VALUES (NULL, '3', '9', '118', '2018-01-04 00:00:00', '1');
INSERT INTO `master_card_level_upgrade` (`master_card_level_upgrade_id`, `card_type`, `level_id`, `card_count`, `created_at`, `status`) VALUES (NULL, '3', '10', '140', '2018-01-04 00:00:00', '1');

INSERT INTO `master_card_level_upgrade` (`master_card_level_upgrade_id`, `card_type`, `level_id`, `card_count`, `created_at`, `status`) VALUES (NULL, '1', '1', '3', '2018-01-04 00:00:00', '1');
INSERT INTO `master_card_level_upgrade` (`master_card_level_upgrade_id`, `card_type`, `level_id`, `card_count`, `created_at`, `status`) VALUES (NULL, '2', '1', '4', '2018-01-04 00:00:00', '1');

INSERT INTO `master_card_level_upgrade` (`master_card_level_upgrade_id`, `card_type`, `level_id`, `card_count`, `created_at`, `status`) VALUES (NULL, '3', '1', '5', '2018-01-04 00:00:00', '1');

----------4-1-2018
ALTER TABLE `waiting_room` ADD `relics` INT(11) NOT NULL AFTER `level_id`;

----------------8/01/2018
ALTER TABLE `waiting_room` ADD `cube` TINYINT(2) NOT NULL AFTER `circlet`, ADD `gold` INT(11) NOT NULL AFTER `cube`, ADD `cards` INT(11) NOT NULL AFTER `gold`;
ALTER TABLE `waiting_room` ADD `cube_status` TINYINT(2) NOT NULL AFTER `cards`;


INSERT INTO `master_cube_reward` (`master_cube_reward_id`, `cube_id`, `master_stadium_id`, `gold`, `cards`, `garantee`, `created_at`, `status`) VALUES (NULL, '1', '1', '25', '1', '', '2018-01-08 00:00:00', '1');

---09-01-2018
INSERT INTO `master_cube_probability` (`cube_probability_id`, `cube_id`, `master_stadium_id`, `min_value`, `max_value`, `created_at`, `status`) VALUES ('', '1', '1', '41', '100', '2018-01-09 00:00:00', '1'), ('', '2', '1', '11', '40', '2018-01-09 00:00:00', '1');
INSERT INTO `master_cube_probability` (`cube_probability_id`, `cube_id`, `master_stadium_id`, `min_value`, `max_value`, `created_at`, `status`) VALUES ('', '3', '2', '1', '10', '2018-01-09 00:00:00', '1'), ('', '1', '2', '41', '100', '2018-01-09 00:00:00', '1'),  ('', '2', '2', '11', '40', '2018-01-09 00:00:00', '1'), ('', '3', '2', '1', '10', '2018-01-09 00:00:00', '1');

INSERT INTO `master_cards_probability` (`card_probability_id`, `card_id`, `master_stadium_id`, `min_value`, `max_value`, `created_at`, `status`) VALUES (NULL, '1', '1', '1', '10', '2018-01-08 00:00:00', '1'), (NULL, '2', '1', '11', '20', '2018-01-09 00:00:00', '1');

INSERT INTO `master_cards_probability` (`card_probability_id`, `card_id`, `master_stadium_id`, `min_value`, `max_value`, `created_at`, `status`) VALUES (NULL, '1', '2', '1', '5', '2018-01-08 00:00:00', '1'), (NULL, '2', '2', '6', '10', '2018-01-09 00:00:00', '1'), (NULL, '3', '2', '11', '15', '2018-01-09 00:00:00', '1'), (NULL, '4', '2', '16', '20', '2018-01-09 00:00:00', '1'), (NULL, '5', '2', '21', '25', '2018-01-09 00:00:00', '1'), (NULL, '11', '2', '26', '30', '2018-01-09 00:00:00', '1'), (NULL, '12', '2', '31', '35', '2018-01-09 00:00:00', '1'), (NULL, '13', '2', '36', '40', '2018-01-09 00:00:00', '1'), (NULL, '14', '2', '41', '45', '2018-01-09 00:00:00', '1'), (NULL, '15', '2', '46', '50', '2018-01-08 00:00:00', '1'), (NULL, '6', '2', '51', '55', '2018-01-09 00:00:00', '1'), (NULL, '7', '2', '56', '60', '2018-01-09 00:00:00', '1'), (NULL, '8', '2', '61', '65', '2018-01-09 00:00:00', '1'), (NULL, '9', '2', '66', '70', '2018-01-09 00:00:00', '1'), (NULL, '10', '2', '71', '75', '2018-01-09 00:00:00', '1'), (NULL, '16', '2', '76', '80', '2018-01-09 00:00:00', '1'), (NULL, '17', '2', '81', '85', '2018-01-09 00:00:00', '1'), (NULL, '18', '2', '86', '90', '2018-01-09 00:00:00', '1'), (NULL, '19', '2', '91', '95', '2018-01-09 00:00:00', '1'), (NULL, '20', '2', '96', '100', '2018-01-09 00:00:00', '1');

ALTER TABLE `master_cube_probability` ADD PRIMARY KEY(`cube_probability_id`);
ALTER TABLE `master_cube_probability` CHANGE `cube_probability_id` `cube_probability_id` INT(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `user_reward` ADD `master_stadium_id` INT(11) NOT NULL AFTER `cube_id`;
ALTER TABLE `user_reward` ADD PRIMARY KEY(`user_reward_id`);
ALTER TABLE `user_reward` ADD `claimed_at` DATETIME NOT NULL AFTER `reward_status`;
ALTER TABLE `user_reward` CHANGE `claimed_at` `claimed_at` INT NOT NULL;
ALTER TABLE `master_cube_reward` ADD `ultra_rare` INT NOT NULL AFTER `rare`;
ALTER TABLE `master_card` ADD `master_stadium_id` INT NOT NULL AFTER `title`;
ALTER TABLE `user` ADD `gold` INT NOT NULL AFTER `relics`;



--10/01/2018
ALTER TABLE `master_cube_reward` CHANGE `cards` `card_count` INT(11) NOT NULL;
ALTER TABLE `user` ADD `xp` INT(11) NOT NULL AFTER `gold`;

---11/01/2018
INSERT INTO `master_gold_card_level_up` (`master_gold_card_level_up_id`, `level_id`, `rarity_type`, `gold`, `created_at`, `status`) VALUES (NULL, '2', '1', '20', '2018-01-11 00:00:00', '1'), (NULL, '3', '1', '25', '2018-01-11 00:00:00', '1');
INSERT INTO `master_gold_card_level_up` (`master_gold_card_level_up_id`, `level_id`, `rarity_type`, `gold`, `created_at`, `status`) VALUES (NULL, '4', '1', '30', '2018-01-11 00:00:00', '1'), (NULL, '5', '1', '70', '2018-01-11 00:00:00', '1'),(NULL, '6', '1', '100', '2018-01-11 00:00:00', '1'), (NULL, '7', '1', '120', '2018-01-11 00:00:00', '1'),(NULL, '8', '1', '200', '2018-01-11 00:00:00', '1'), (NULL, '9', '1', '300', '2018-01-11 00:00:00', '1'),(NULL, '10', '1', '500', '2018-01-11 00:00:00', '1');
INSERT INTO `master_gold_card_level_up` (`master_gold_card_level_up_id`, `level_id`, `rarity_type`, `gold`, `created_at`, `status`) VALUES (NULL, '4', '2', '120', '2018-01-11 00:00:00', '1'), (NULL, '5', '2', '200', '2018-01-11 00:00:00', '1'),(NULL, '6', '2', '250', '2018-01-11 00:00:00', '1'), (NULL, '7', '2', '300', '2018-01-11 00:00:00', '1'),(NULL, '8', '2', '450', '2018-01-11 00:00:00', '1'), (NULL, '9', '2', '600', '2018-01-11 00:00:00', '1'),(NULL, '10', '2', '750', '2018-01-11 00:00:00', '1'),(NULL, '2', '2', '50', '2018-01-11 00:00:00', '1'),(NULL, '3', '2', '75', '2018-01-11 00:00:00', '1');
INSERT INTO `master_gold_card_level_up` (`master_gold_card_level_up_id`, `level_id`, `rarity_type`, `gold`, `created_at`, `status`) VALUES (NULL, '4', '3', '500', '2018-01-11 00:00:00', '1'), (NULL, '5', '3', '750', '2018-01-11 00:00:00', '1'),(NULL, '6', '3', '1000', '2018-01-11 00:00:00', '1'), (NULL, '7', '3', '1500', '2018-01-11 00:00:00', '1'),(NULL, '8', '3', '2000', '2018-01-11 00:00:00', '1'), (NULL, '9', '3', '3000', '2018-01-11 00:00:00', '1'),(NULL, '10', '3', '5000', '2018-01-11 00:00:00', '1'),(NULL, '2', '3', '100', '2018-01-11 00:00:00', '1'),(NULL, '3', '3', '250', '2018-01-11 00:00:00', '1');

ALTER TABLE `master_card_level_upgrade` CHANGE `card_type` `rarity_type` INT(1) NOT NULL;

INSERT INTO `master_card_level_up` (`master_card_level_up_id`, `level_id`, `rarity_type`, `xp`, `created_at`, `status`) VALUES (NULL, '1', '1', '100', '2018-01-11 00:00:00', '1'), (NULL, '2', '1', '200', '2018-01-11 00:00:00', '1');

ALTER TABLE `master_level_up` ADD `created_at` DATETIME NOT NULL AFTER `stadium_tower_damage`, ADD `status` TINYINT(2) NOT NULL AFTER `created_at`;

--16/01/2018

ALTER TABLE `master_stadium` ADD `title` VARCHAR(200) NOT NULL AFTER `master_stadium_id`;
ALTER TABLE `user` ADD `last_access_time` INT(11) NOT NULL AFTER `ios_push_token`, ADD `notification_status` TINYINT(2) NOT NULL AFTER `last_access_time`;

--17/01/2018-01
ALTER TABLE `master_card` ADD `deploy_time` INT NOT NULL AFTER `mana_cost`, ADD `dice_count` INT NOT NULL AFTER `deploy_time`, ADD `dice_damage` INT NOT NULL AFTER `dice_count`, ADD `range` INT NOT NULL AFTER `dice_damage`, ADD `jump_distance` INT NOT NULL AFTER `range`, ADD `radius` INT NOT NULL AFTER `jump_distance`, ADD `area_damage` INT NOT NULL AFTER `radius`, ADD `tower_damage` INT NOT NULL AFTER `area_damage`, ADD `life_time` INT NOT NULL AFTER `tower_damage`, ADD `brahmos_damage` INT NOT NULL AFTER `life_time`, ADD `spawn_speed` INT NOT NULL AFTER `brahmos_damage`, ADD `monkey_damage` INT NOT NULL AFTER `spawn_speed`, ADD `monkey_level` INT NOT NULL AFTER `monkey_damage`, ADD `monkey_count` INT NOT NULL AFTER `monkey_level`, ADD `damage_per_second` INT NOT NULL AFTER `monkey_count`, ADD `duration` INT NOT NULL AFTER `damage_per_second`, ADD `travel_distance` INT NOT NULL AFTER `duration`;

ALTER TABLE `master_card` ADD `dice_spawn_time` INT NOT NULL AFTER `dice_count`;

--18/01/2018
ALTER TABLE `master_card` CHANGE `stadium_id` `master_stadium_id` INT(11) NOT NULL;
ALTER TABLE `user` CHANGE `stadium_id` `master_stadium_id` TINYINT(2) NOT NULL;
ALTER TABLE `user_reward` CHANGE `stadium_id` `master_stadium_id` INT(11) NOT NULL;
ALTER TABLE `master_cube_probability` CHANGE `stadium_id` `master_stadium_id` INT(11) NOT NULL;
ALTER TABLE `master_cube_reward` CHANGE `stadium_id` `master_stadium_id` INT(11) NOT NULL;
ALTER TABLE `master_cards_probability` CHANGE `stadium_id` `master_stadium_id` INT(11) NOT NULL;
ALTER TABLE `user_card`  ADD `area_damage` INT NOT NULL  AFTER `is_deck`,  ADD `brahmos_damage` INT NOT NULL  AFTER `area_damage`,  ADD `damage_per_second` INT NOT NULL  AFTER `brahmos_damage`,  ADD `deploy_time` INT NOT NULL  AFTER `damage_per_second`,  ADD `dice_count` INT NOT NULL  AFTER `deploy_time`,  ADD `dice_damage` INT NOT NULL  AFTER `dice_count`,  ADD `dice_spawn_time` INT NOT NULL  AFTER `dice_damage`,  ADD `duration` INT NOT NULL  AFTER `dice_spawn_time`,  ADD `hit_points` INT NOT NULL  AFTER `duration`,  ADD `hit_speed` INT NOT NULL  AFTER `hit_points`,  ADD `jump_distance` INT NOT NULL  AFTER `hit_speed`,  ADD `life_time1` INT NOT NULL  AFTER `jump_distance`,  ADD `life_time` INT NOT NULL  AFTER `life_time1`,  ADD `mana_cost` INT NOT NULL  AFTER `life_time`,  ADD `monkey_count` INT NOT NULL  AFTER `mana_cost`,  ADD `monkey_damage` INT NOT NULL  AFTER `monkey_count`,  ADD `monkey_level` INT NOT NULL  AFTER `monkey_damage`,  ADD `radius` INT NOT NULL  AFTER `monkey_level`,  ADD `range_value` INT NOT NULL  AFTER `radius`,  ADD `spawn_speed` INT NOT NULL  AFTER `range_value`,  ADD `tower_damage` INT NOT NULL  AFTER `spawn_speed`,  ADD `travel_speed` INT NOT NULL  AFTER `tower_damage`,  ADD `walk_speed` INT NOT NULL  AFTER `travel_speed`;
ALTER TABLE `user_card` ADD `damage` INT NOT NULL AFTER `hit_speed`;
ALTER TABLE `user_card` CHANGE `travel_speed` `travel_distance` INT(11) NOT NULL;

--****************************************************************
--19/01/2018-01
ALTER TABLE `user_card` CHANGE `hit_speed` `hit_speed` FLOAT(11) NOT NULL, CHANGE `damage` `damage` FLOAT(11) NOT NULL, CHANGE `mana_cost` `mana_cost` FLOAT(11) NOT NULL, CHANGE `walk_speed` `walk_speed` FLOAT(11) NOT NULL;

--31-01-2018

CREATE TABLE `master_card` (
  `master_card_id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `master_stadium_id` int(11) NOT NULL,
  `card_type` int(11) NOT NULL COMMENT '1 - character, 2- power',
  `rarity_type` int(11) NOT NULL COMMENT '1-common, 2-rear,3-ultra rear',
  `hit_points` float NOT NULL,
  `damage` float NOT NULL,
  `hit_speed` float NOT NULL,
  `walk_speed` float NOT NULL,
  `mana_cost` float NOT NULL,
  `deploy_time` int(11) NOT NULL,
  `dice_count` int(11) NOT NULL,
  `dice_spawn_time` int(11) NOT NULL,
  `dice_damage` int(11) NOT NULL,
  `range_value` int(11) NOT NULL,
  `jump_distance` int(11) NOT NULL,
  `radius` int(11) NOT NULL,
  `area_damage` int(11) NOT NULL,
  `tower_damage` int(11) NOT NULL,
  `life_time` int(11) NOT NULL,
  `brahmos_damage` int(11) NOT NULL,
  `spawn_speed` int(11) NOT NULL,
  `monkey_damage` int(11) NOT NULL,
  `monkey_level` int(11) NOT NULL,
  `monkey_count` int(11) NOT NULL,
  `damage_per_second` int(11) NOT NULL,
  `duration` int(11) NOT NULL,
  `travel_distance` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `status` tinyint(2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `master_card` (`master_card_id`, `title`, `master_stadium_id`, `card_type`, `rarity_type`, `hit_points`, `damage`, `hit_speed`, `walk_speed`, `mana_cost`, `deploy_time`, `dice_count`, `dice_spawn_time`, `dice_damage`, `range_value`, `jump_distance`, `radius`, `area_damage`, `tower_damage`, `life_time`, `brahmos_damage`, `spawn_speed`, `monkey_damage`, `monkey_level`, `monkey_count`, `damage_per_second`, `duration`, `travel_distance`, `created_at`, `status`) VALUES
(1, 'virabhadra', 1, 1, 1, 2000, 200, 1.5, 1, 5, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '2018-01-03 00:00:00', 1),
(2, 'shakuni', 1, 1, 1, 550, 40, 0.7, 1, 5, 1, 3, 3, 20, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '2018-01-03 00:00:00', 1),
(3, 'nakul', 1, 1, 1, 900, 120, 1.1, 1, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '2018-01-03 00:00:00', 1),
(4, 'sahadeva', 1, 1, 2, 750, 270, 1.8, 1, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '2018-01-03 00:00:00', 1),
(5, 'ghatotkacha', 1, 1, 1, 2400, 200, 1.5, 1, 5, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '2018-01-03 00:00:00', 1),
(6, 'satyaki', 2, 1, 1, 400, 250, 1.2, 1, 3, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '2018-01-03 00:00:00', 1),
(7, 'meghanath', 2, 1, 3, 1000, 200, 1.5, 1, 6, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '2018-01-03 00:00:00', 1),
(8, 'jatayu', 2, 1, 2, 950, 30, 0.4, 1, 3, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '2018-01-03 00:00:00', 1),
(9, 'angad', 2, 1, 2, 950, 140, 1.5, 1, 3, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '2018-01-03 00:00:00', 1),
(10, 'jamvanta', 2, 1, 3, 600, 80, 1.5, 1, 3, 1, 0, 0, 0, 0, 2, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '2018-01-03 00:00:00', 1),
(11, 'agni astra', 1, 2, 1, 0, 0, 0, 1, 4, 0, 0, 0, 0, 0, 0, 0, 350, 180, 0, 0, 0, 0, 0, 0, 0, 0, 0, '2018-01-03 00:00:00', 1),
(12, 'trident', 1, 2, 2, 0, 0, 0, 1, 5, 0, 0, 0, 0, 0, 0, 0, 500, 350, 0, 0, 0, 0, 0, 0, 0, 0, 0, '2018-01-03 00:00:00', 1),
(13, 'rock tower', 1, 2, 1, 900, 100, 1.5, 1, 4, 1, 0, 0, 0, 0, 0, 0, 0, 0, 40, 0, 0, 0, 0, 0, 0, 0, 0, '2018-01-03 00:00:00', 1),
(14, 'brahmos building', 1, 2, 2, 1200, 0, 0, 1, 4, 1, 0, 0, 0, 0, 0, 0, 0, 0, 50, 280, 3, 0, 0, 0, 0, 0, 0, '2018-01-03 00:00:00', 1),
(15, 'tree monkey building', 1, 2, 1, 0, 0, 0, 1, 3, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 50, 7, 3, 0, 0, 0, '2018-01-03 00:00:00', 1),
(16, 'naga astra', 2, 2, 1, 0, 0, 0, 1, 3, 0, 0, 0, 0, 0, 0, 2, 0, 30, 0, 0, 0, 0, 0, 0, 50, 0, 0, '2018-01-03 00:00:00', 1),
(17, 'chakravyuha', 2, 2, 3, 0, 0, 0, 1, 5, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '2018-01-03 00:00:00', 1),
(18, 'fire hay ball', 2, 2, 2, 0, 0, 0, 1, 4, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 40, 5, 0, '2018-01-03 00:00:00', 1),
(19, 'vajra', 2, 2, 2, 0, 600, 0, 1, 4, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '2018-01-03 00:00:00', 1),
(20, 'narayanastra', 2, 2, 1, 0, 200, 0, 1, 3, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '2018-01-03 00:00:00', 1);

ALTER TABLE `master_card`
  ADD PRIMARY KEY (`master_card_id`);
ALTER TABLE `master_card`
  MODIFY `master_card_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--master_card_level_upgrade
CREATE TABLE `master_card_level_upgrade` (
  `master_card_level_upgrade_id` int(11) NOT NULL,
  `rarity_type` int(1) NOT NULL,
  `level_id` int(11) NOT NULL,
  `card_count` int(11) NOT NULL,
  `gold` int(11) NOT NULL,
  `xp` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `status` tinyint(2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `master_card_level_upgrade` (`master_card_level_upgrade_id`, `rarity_type`, `level_id`, `card_count`, `gold`, `xp`, `created_at`, `status`) VALUES
(1, 1, 2, 5, 20, 100, '2018-01-04 00:00:00', 1),
(2, 1, 3, 8, 25, 500, '2018-01-04 00:00:00', 1),
(3, 1, 4, 12, 30, 1000, '2018-01-04 00:00:00', 1),
(4, 1, 5, 16, 70, 2500, '2018-01-04 00:00:00', 1),
(5, 1, 6, 21, 100, 4000, '2018-01-04 00:00:00', 1),
(6, 1, 7, 27, 120, 7000, '2018-01-04 00:00:00', 1),
(7, 1, 8, 34, 200, 10000, '2018-01-04 00:00:00', 1),
(8, 1, 9, 42, 300, 13000, '2018-01-04 00:00:00', 1),
(9, 1, 10, 50, 500, 15000, '2018-01-04 00:00:00', 1),
(10, 2, 2, 9, 50, 500, '2018-01-04 00:00:00', 1),
(11, 2, 3, 15, 75, 1600, '2018-01-04 00:00:00', 1),
(12, 2, 4, 22, 120, 3000, '2018-01-04 00:00:00', 1),
(13, 2, 5, 30, 200, 5000, '2018-01-04 00:00:00', 1),
(14, 2, 6, 39, 250, 8000, '2018-01-04 00:00:00', 1),
(15, 2, 7, 50, 300, 11000, '2018-01-04 00:00:00', 1),
(16, 2, 8, 62, 450, 15000, '2018-01-04 00:00:00', 1),
(17, 2, 9, 75, 600, 18000, '2018-01-04 00:00:00', 1),
(18, 2, 10, 90, 750, 20000, '2018-01-04 00:00:00', 1),
(19, 3, 2, 12, 100, 800, '2018-01-04 00:00:00', 1),
(20, 3, 3, 20, 250, 2000, '2018-01-04 00:00:00', 1),
(21, 3, 4, 31, 500, 42000, '2018-01-04 00:00:00', 1),
(22, 3, 5, 45, 750, 7500, '2018-01-04 00:00:00', 1),
(23, 3, 6, 60, 1000, 12000, '2018-01-04 00:00:00', 1),
(24, 3, 7, 78, 1500, 18000, '2018-01-04 00:00:00', 1),
(25, 3, 8, 90, 2000, 24000, '2018-01-04 00:00:00', 1),
(26, 3, 9, 118, 3000, 30000, '2018-01-04 00:00:00', 1),
(27, 3, 10, 140, 5000, 45000, '2018-01-04 00:00:00', 1),
(29, 2, 1, 4, 0, 0, '2018-01-04 00:00:00', 1),
(30, 3, 1, 5, 0, 0, '2018-01-04 00:00:00', 1);

ALTER TABLE `master_card_level_upgrade`
  ADD PRIMARY KEY (`master_card_level_upgrade_id`);

ALTER TABLE `master_card_level_upgrade`
  MODIFY `master_card_level_upgrade_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

-- master_card_probability
CREATE TABLE `master_card_probability` (
  `master_card_probability_id` int(11) NOT NULL,
  `master_card_id` int(11) NOT NULL,
  `master_stadium_id` int(11) NOT NULL,
  `min_value` int(11) NOT NULL,
  `max_value` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `status` tinyint(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `master_card_probability` (`master_card_probability_id`, `master_card_id`, `master_stadium_id`, `min_value`, `max_value`, `created_at`, `status`) VALUES
(1, 1, 1, 1, 10, '2018-01-08 00:00:00', 1),
(2, 2, 1, 11, 20, '2018-01-09 00:00:00', 1),
(3, 3, 1, 21, 30, '2018-01-08 00:00:00', 1),
(4, 4, 1, 31, 40, '2018-01-09 00:00:00', 1),
(5, 5, 1, 41, 50, '2018-01-09 00:00:00', 1),
(6, 11, 1, 51, 60, '2018-01-09 00:00:00', 1),
(7, 12, 1, 61, 70, '2018-01-09 00:00:00', 1),
(8, 13, 1, 71, 80, '2018-01-09 00:00:00', 1),
(9, 14, 1, 81, 90, '2018-01-09 00:00:00', 1),
(10, 15, 1, 91, 100, '2018-01-09 00:00:00', 1),
(11, 1, 2, 1, 5, '2018-01-08 00:00:00', 1),
(12, 2, 2, 6, 10, '2018-01-09 00:00:00', 1),
(13, 3, 2, 11, 15, '2018-01-09 00:00:00', 1),
(14, 4, 2, 16, 20, '2018-01-09 00:00:00', 1),
(15, 5, 2, 21, 25, '2018-01-09 00:00:00', 1),
(16, 11, 2, 26, 30, '2018-01-09 00:00:00', 1),
(17, 12, 2, 31, 35, '2018-01-09 00:00:00', 1),
(18, 13, 2, 36, 40, '2018-01-09 00:00:00', 1),
(19, 14, 2, 41, 45, '2018-01-09 00:00:00', 1),
(20, 15, 2, 46, 50, '2018-01-08 00:00:00', 1),
(21, 6, 2, 51, 55, '2018-01-09 00:00:00', 1),
(22, 7, 2, 56, 60, '2018-01-09 00:00:00', 1),
(23, 8, 2, 61, 65, '2018-01-09 00:00:00', 1),
(24, 9, 2, 66, 70, '2018-01-09 00:00:00', 1),
(25, 10, 2, 71, 75, '2018-01-09 00:00:00', 1),
(26, 16, 2, 76, 80, '2018-01-09 00:00:00', 1),
(27, 17, 2, 81, 85, '2018-01-09 00:00:00', 1),
(28, 18, 2, 86, 90, '2018-01-09 00:00:00', 1),
(29, 19, 2, 91, 95, '2018-01-09 00:00:00', 1),
(30, 20, 2, 96, 100, '2018-01-09 00:00:00', 1);

ALTER TABLE `master_card_probability`
  ADD PRIMARY KEY (`master_card_probability_id`);

ALTER TABLE `master_card_probability`
  MODIFY `master_card_probability_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--7/02/2018-01
ALTER TABLE `card_property` ADD `status` TINYINT(2) NOT NULL AFTER `created_at`;
ALTER TABLE `card_property_level_upgrade` ADD `created_at` DATETIME NOT NULL AFTER `card_property_value`, ADD `status` TINYINT NOT NULL AFTER `created_at`;

ALTER TABLE `user_card_property` CHANGE `updated_at` `created_at` DATETIME NOT NULL;
ALTER TABLE `user_card_property` ADD `status` TINYINT(2) NOT NULL AFTER `created_at`;

--16-02-2018
ALTER TABLE `master_card_probability` ADD `probability` INT NOT NULL AFTER `max_value`;
UPDATE `master_card_probability` SET probability = 10 WHERE master_stadium_id =1
UPDATE `master_card_probability` SET probability = 5 WHERE master_stadium_id =2

--21-02-2018
ALTER TABLE `user` ADD `game_center_id` INT NOT NULL AFTER `google_id`;
ALTER TABLE `user` CHANGE `game_center_id` `game_center_id` VARCHAR(300) NOT NULL;

--19-03-2018
ALTER TABLE `user` ADD `is_tutorial_completed` TINYINT NOT NULL AFTER `notification_status`;

--09-05-2018
ALTER TABLE `user` ADD `is_copper_cube_notification_sent` TINYINT(2) NOT NULL AFTER `is_tutorial_completed`;

ALTER TABLE `card_property_level_upgrade` CHANGE `card_property_value` `card_property_value` FLOAT(11) NOT NULL;
ALTER TABLE `user_card_property` CHANGE `user_card_property_value` `user_card_property_value` FLOAT(11) NOT NULL;

--22-05-2018
ALTER TABLE `waiting_room` ADD `win_streak` INT NOT NULL AFTER `entry_time`;

--10-09-2018
INSERT INTO `master_card_probability` (`master_card_probability_id`, `master_card_id`, `master_stadium_id`, `min_value`, `max_value`, `probability`, `created_at`, `status`) VALUES (NULL, '21', '2', '96', '100', '10', '2018-09-10 00:00:00', '1'), (NULL, '22', '2', '96', '100', '5', '2018-09-10 00:00:00', '1'), (NULL, '23', '2', '96', '100', '10', '2018-09-10 00:00:00', '1');

--27--05-2019
 ALTER TABLE `master_card`  ADD `gold` INT(11) NOT NULL COMMENT 'gold required to purchase the card'  AFTER `card_description`;

 --28-05-2019

 --user_daily_reward
 CREATE TABLE `user_daily_reward` (
  `user_daily_reward_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `daily_reward_id` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `status` tinyint(4) NOT NULL,
PRIMARY KEY (`user_daily_reward_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--master_daily_reward
CREATE TABLE `master_daily_reward` (
  `master_daily_reward_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(2000) NOT NULL,
  `master_stadium_id` int(11) NOT NULL,
  `amount` float NOT NULL,
  `created_at` datetime NOT NULL,
  `status` tinyint(2) NOT NULL,
PRIMARY KEY (`master_daily_reward_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--master_daily_reward_item
CREATE TABLE `master_daily_reward_item` (
  `master_daily_reward_item_id` int(11) NOT NULL AUTO_INCREMENT,
  `master_daily_reward_id` int(11) NOT NULL,
  `reward_item_id` int(11) NOT NULL COMMENT 'here define the inventory id, master_card_id and cube ids',
  `reward_type` int(11) NOT NULL COMMENT '1-inventory;2-card;3-cube',
  `count` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `status` tinyint(2) NOT NULL,
PRIMARY KEY (`master_daily_reward_item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--29-05-2019
--user_deck
CREATE TABLE `user_deck` (
  `user_deck_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `deck_data` varchar(1000) NOT NULL,
  `created_at` datetime NOT NULL,
  `status` tinyint(4) NOT NULL,
PRIMARY KEY (`user_deck_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--04-06-2019
--user_daily_card
CREATE TABLE `user_daily_cards` (
  `user_daily_cards` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `card_id` varchar(50) NOT NULL,
  `created_at` datetime NOT NULL,
  `status` tinyint(4) NOT NULL,
PRIMARY KEY (`user_daily_cards`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--07-10-2019
CREATE TABLE `invite` (
  `invite_id` int(11) NOT NULL NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `invite_token` varchar(1000) NOT NULL,
  `accepted_user_id` int(11) NOT NULL,
  `room_id` int(11) NOT NULL,
  `status` tinyint(4) NOT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`invite_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


--20-01-2020
ALTER TABLE `master_cube_reward`  ADD `common` INT(11) NOT NULL  AFTER `ultra_rare`;