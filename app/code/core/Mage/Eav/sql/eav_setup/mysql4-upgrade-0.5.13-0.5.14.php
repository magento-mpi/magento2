<?php

$conn->multi_query(<<<EOT

SET NAMES utf8;

SET SQL_MODE='';

SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO';

alter table `eav_attribute` ,
    add column `apply_to` tinyint (3) UNSIGNED  DEFAULT '0' NOT NULL  after `is_unique`, 
    add column `use_in_super_product` tinyint (1) UNSIGNED  DEFAULT '1' NOT NULL  after `apply_to`

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;

EOT
);