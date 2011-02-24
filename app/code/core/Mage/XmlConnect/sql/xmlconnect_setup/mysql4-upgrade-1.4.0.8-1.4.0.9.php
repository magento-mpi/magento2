<?php

$installer = $this;

$installer->startSetup();

$installer->run("CREATE TABLE `{$installer->getTable('xmlconnect_queue')}` (
`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`create_time` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ,
`exec_time` TIMESTAMP NOT NULL ,
`template_id` INT NOT NULL ,
`push_title` VARCHAR( 140 ) NOT NULL ,
`message_title` VARCHAR( 255 ) NOT NULL ,
`content` TEXT NOT NULL ,
`status` TINYINT NOT NULL DEFAULT '0',
`type` VARCHAR( 12 ) NOT NULL ,
`app_code` VARCHAR( 12 ) NOT NULL
) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin");


$installer->run("CREATE TABLE `{$installer->getTable('xmlconnect_notification_template')}` (
`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`app_type` VARCHAR( 32 ) NOT NULL ,
`name` VARCHAR( 255 ) NOT NULL ,
`push_title` VARCHAR( 141 ) NOT NULL ,
`message_title` VARCHAR( 255 ) NOT NULL ,
`content` TEXT NOT NULL ,
`created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ,
`modified_at` TIMESTAMP NOT NULL
) ENGINE = InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_bin");

$installer->run("ALTER TABLE {$installer->getTable('xmlconnect_queue')} ADD FOREIGN KEY (template_id) REFERENCES {$installer->getTable('xmlconnect_notification_template')}(id) ON DELETE CASCADE;");

$installer->endSetup();
