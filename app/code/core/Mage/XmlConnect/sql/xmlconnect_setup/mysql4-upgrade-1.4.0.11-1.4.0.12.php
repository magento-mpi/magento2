<?php

$installer = $this;

$installer->startSetup();

$installer->run("ALTER TABLE {$installer->getTable('xmlconnect_notification_template')} ADD `app_code` VARCHAR( 32 ) NOT NULL AFTER `id` , ADD INDEX ( `app_code` );");
$installer->run("ALTER TABLE {$installer->getTable('xmlconnect_notification_template')} DROP `app_type`;");
$installer->run("SET foreign_key_checks = 0;");
$installer->run("ALTER TABLE {$installer->getTable('xmlconnect_notification_template')} ADD FOREIGN KEY (`app_code`) REFERENCES {$installer->getTable('xmlconnect_application')} (`code`) ON DELETE CASCADE ON UPDATE CASCADE;");
$installer->run("SET foreign_key_checks = 1;");

$installer->endSetup();
