<?php

$conn->multi_query(<<<EOT

SET NAMES utf8;

SET SQL_MODE='';

SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO';

alter table `eav_attribute` add column `is_visible_on_front` tinyint(1) unsigned NOT NULL default '0' after `is_comparable`;
alter table `eav_attribute` add column `is_unique` tinyint(1) unsigned NOT NULL default '0' after `is_visible_on_front`;

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;

EOT
);