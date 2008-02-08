<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Core
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

$installer = $this;
/* @var $installer Mage_Customer_Model_Entity_Setup */

$installer->startSetup();

$installer->run("
DROP TABLE IF EXISTS {$this->getTable('core_store_group')};
CREATE TABLE {$this->getTable('core_store_group')} (
  `group_id` smallint(5) unsigned NOT NULL auto_increment,
  `website_id` smallint(5) unsigned NOT NULL default '0',
  `name` varchar(32) NOT NULL default '',
  `root_category_id` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`group_id`),
  KEY `FK_STORE_GROUP_WEBSITE` (`website_id`),
  CONSTRAINT `FK_STORE_GROUP_WEBSITE` FOREIGN KEY (`website_id`) REFERENCES {$this->getTable('core_website')} (`website_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
INSERT INTO {$this->getTable('core_store_group')} VALUES (1,1,'Main group',2);
ALTER TABLE {$this->getTable('core_store')} DROP FOREIGN KEY `FK_STORE_LANGUAGE`;
ALTER TABLE {$this->getTable('core_store')} DROP INDEX `FK_STORE_LANGUAGE`;
DROP TABLE IF EXISTS {$this->getTable('core_language')};
ALTER TABLE {$this->getTable('core_store')} CHANGE `language_code` `group_id` SMALLINT UNSIGNED NOT NULL;
UPDATE {$this->getTable('core_store')} SET `group_id`=1;
ALTER TABLE {$this->getTable('core_store')} ADD INDEX `FK_STORE_GROUP` (`group_id`);
ALTER TABLE {$this->getTable('core_store')} ADD CONSTRAINT `FK_STORE_GROUP_STORE` FOREIGN KEY (`group_id`) REFERENCES {$this->getTable('core_store_group')} (`group_id`) ON DELETE CASCADE ON UPDATE CASCADE;

");

$installer->endSetup();
