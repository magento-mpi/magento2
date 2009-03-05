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
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Enterprise
 * @package    Enterprise_CatalogEvent
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */

$installer->startSetup();

$fkName = strtoupper($installer->getTable('enterprise_catalogevent/event'));

$installer->run("
-- DROP TABLE IF EXISTS `{$installer->getTable('enterprise_catalogevent/event')}`;
CREATE TABLE `{$installer->getTable('enterprise_catalogevent/event')}` (
    `event_id` int(10) unsigned NOT NULL auto_increment,
    `category_id` int(10) unsigned default NULL,
    `date_start` datetime default NULL,
    `date_end` datetime default NULL,
    `status` enum('upcoming','open','closed') NOT NULL default 'upcoming',
    `display_state` tinyint(3) unsigned default 0,
    PRIMARY KEY  (`event_id`),
    UNIQUE KEY `category_id` (`category_id`),
    KEY `sort_order` (`date_start`,`date_end`,`status`),
    CONSTRAINT `FK_{$fkName}_CATEGORY` FOREIGN KEY (`category_id`) REFERENCES `{$this->getTable('catalog/category')}` (`entity_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Catalog Events';
");

$installer->addAttribute('quote_item', 'event_id', array('type' => 'int'));
$installer->addAttribute('quote_item', 'event_name', array());
$installer->addAttribute('order_item', 'event_id', array('type'=>'int'));
$installer->addAttribute('order_item', 'event_name', array());


$installer->endSetup();
