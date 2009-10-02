<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
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
 * @category    Enterprise
 * @package     Enterprise_TargetRule
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */


/* @var $installer Enterprise_TargetRule_Model_Mysql4_Setup */
$installer = $this;

$installer->startSetup();

// update backend model for target rule product attributes
$attributeCodes = array(
    'related_targetrule_rule_based_positions',
    'related_targetrule_position_behavior',
    'upsell_targetrule_rule_based_positions',
    'upsell_targetrule_position_behavior',
    'crosssell_targetrule_rule_based_positions',
    'crosssell_targetrule_position_behavior',
);
$backendModel   = 'enterprise_targetrule/catalog_product_attribute_backend_rule';

foreach ($attributeCodes as $attributeCode) {
    $installer->updateAttribute('catalog_product', $attributeCode, 'backend_model', $backendModel);
}

// fixed target rule primary table
$installer->getConnection()->modifyColumn($installer->getTable('enterprise_targetrule/rule'), 'rule_id',
    'INT(10) UNSIGNED NOT NULL AUTO_INCREMENT');
$installer->getConnection()->modifyColumn($installer->getTable('enterprise_targetrule/rule'), 'apply_to',
    'TINYINT UNSIGNED NOT NULL');
//$installer->getConnection()->changeColumn($installer->getTable('enterprise_targetrule/rule'), 'positions_limit',
//    'result_limit', 'TINYINT(3) UNSIGNED NOT NULL DEFAULT 0', true);
$installer->getConnection()->addColumn($installer->getTable('enterprise_targetrule/rule'), 'use_customer_segment',
    'TINYINT(1) UNSIGNED NOT NULL DEFAULT 0');
$installer->getConnection()->addKey($installer->getTable('enterprise_targetrule/rule'),
    'IDX_IS_ACTIVE', 'is_active');
$installer->getConnection()->addKey($installer->getTable('enterprise_targetrule/rule'),
    'IDX_APPLY_TO', 'apply_to');
$installer->getConnection()->addKey($installer->getTable('enterprise_targetrule/rule'),
    'IDX_SORT_ORDER', 'sort_order');
$installer->getConnection()->addKey($installer->getTable('enterprise_targetrule/rule'),
    'IDX_USE_CUSTOMER_SEGMENT', 'use_customer_segment');
$installer->getConnection()->addKey($installer->getTable('enterprise_targetrule/rule'),
    'IDX_DATE', array('from_date', 'to_date'));

// create target rule and customer segment relation table
$installer->run("
CREATE TABLE `{$installer->getTable('enterprise_targetrule/customersegment')}` (
  `rule_id` INT(10) UNSIGNED NOT NULL,
  `segment_id` INT(10) UNSIGNED NOT NULL,
  PRIMARY KEY  (`rule_id`,`segment_id`),
  KEY `IDX_SEGMENT` (`segment_id`),
  CONSTRAINT `FK_ENTERPRISE_TARGETRULE_CUSTOMERSEGMENT_RULE` FOREIGN KEY (`rule_id`)
    REFERENCES `{$installer->getTable('enterprise_targetrule/rule')}` (`rule_id`)
      ON DELETE CASCADE
      ON UPDATE CASCADE,
  CONSTRAINT `FK_ENTERPRISE_TARGETRULE_CUSTOMERSEGMENT_SEGMENT` FOREIGN KEY (`segment_id`)
    REFERENCES `{$installer->getTable('enterprise_customersegment/segment')}` (`segment_id`)
      ON DELETE CASCADE
      ON UPDATE CASCADE
) ENGINE=INNODB DEFAULT CHARSET=utf8;
");

// create target rule matched product cache table
$installer->run("
CREATE TABLE `{$installer->getTable('enterprise_targetrule/product')}` (
  `rule_id` INT(10) UNSIGNED NOT NULL,
  `product_id` INT(10) UNSIGNED NOT NULL,
  `store_id` SMALLINT(5) UNSIGNED NOT NULL,
  PRIMARY KEY  (`rule_id`,`product_id`,`store_id`),
  KEY `IDX_PRODUCT` (`product_id`),
  KEY `IDX_STORE` (`store_id`),
  CONSTRAINT `FK_ENTERPRISE_TARGETRULE_PRODUCT_STORE` FOREIGN KEY (`store_id`)
    REFERENCES `{$installer->getTable('core/store')}` (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_ENTERPRISE_TARGETRULE_PRODUCT_PRODUCT` FOREIGN KEY (`product_id`)
    REFERENCES `{$installer->getTable('catalog/product')}` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_ENTERPRISE_TARGETRULE_PRODUCT_RULE` FOREIGN KEY (`rule_id`)
    REFERENCES `{$installer->getTable('enterprise_targetrule/rule')}` (`rule_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=INNODB DEFAULT CHARSET=utf8;
");

// create target rule frontend index tables
$installer->run("
CREATE TABLE `{$installer->getTable('enterprise_targetrule/index_related')}` (
  `entity_id` INT(10) UNSIGNED NOT NULL,
  `store_id` SMALLINT(5) UNSIGNED NOT NULL,
  `customer_group_id` SMALLINT(5) UNSIGNED NOT NULL,
  `value` CHAR(255) NOT NULL,
  PRIMARY KEY  (`entity_id`,`store_id`,`customer_group_id`),
  KEY `IDX_STORE` (`store_id`),
  KEY `IDX_CUSTOMER_GROUP` (`customer_group_id`),
  CONSTRAINT `FK_ENTERPRISE_TARGETRULE_INDEX_RELATED_CUSTOMER_GROUP` FOREIGN KEY (`customer_group_id`)
    REFERENCES `{$installer->getTable('customer/customer_group')}` (`customer_group_id`)
      ON DELETE CASCADE
      ON UPDATE CASCADE,
  CONSTRAINT `FK_ENTERPRISE_TARGETRULE_INDEX_RELATED_PRODUCT` FOREIGN KEY (`entity_id`)
    REFERENCES `{$installer->getTable('catalog/product')}` (`entity_id`)
      ON DELETE CASCADE
      ON UPDATE CASCADE,
  CONSTRAINT `FK_ENTERPRISE_TARGETRULE_INDEX_RELATED_STORE` FOREIGN KEY (`store_id`)
    REFERENCES `{$installer->getTable('core/store')}` (`store_id`)
      ON DELETE CASCADE
      ON UPDATE CASCADE
) ENGINE=INNODB DEFAULT CHARSET=utf8;

CREATE TABLE `{$installer->getTable('enterprise_targetrule/index_upsell')}` (
  `entity_id` INT(10) UNSIGNED NOT NULL,
  `store_id` SMALLINT(5) UNSIGNED NOT NULL,
  `customer_group_id` SMALLINT(5) UNSIGNED NOT NULL,
  `value` CHAR(255) NOT NULL,
  PRIMARY KEY  (`entity_id`,`store_id`,`customer_group_id`),
  KEY `IDX_STORE` (`store_id`),
  KEY `IDX_CUSTOMER_GROUP` (`customer_group_id`),
  CONSTRAINT `FK_ENTERPRISE_TARGETRULE_INDEX_UPSELL_CUSTOMER_GROUP` FOREIGN KEY (`customer_group_id`)
    REFERENCES `{$installer->getTable('customer/customer_group')}` (`customer_group_id`)
      ON DELETE CASCADE
      ON UPDATE CASCADE,
  CONSTRAINT `FK_ENTERPRISE_TARGETRULE_INDEX_UPSELL_PRODUCT` FOREIGN KEY (`entity_id`)
    REFERENCES `{$installer->getTable('catalog/product')}` (`entity_id`)
      ON DELETE CASCADE
      ON UPDATE CASCADE,
  CONSTRAINT `FK_ENTERPRISE_TARGETRULE_INDEX_UPSELL_STORE` FOREIGN KEY (`store_id`)
    REFERENCES `{$installer->getTable('core/store')}` (`store_id`)
      ON DELETE CASCADE
      ON UPDATE CASCADE
) ENGINE=INNODB DEFAULT CHARSET=utf8;

CREATE TABLE `{$installer->getTable('enterprise_targetrule/index_crosssell')}` (
  `entity_id` INT(10) UNSIGNED NOT NULL,
  `store_id` SMALLINT(5) UNSIGNED NOT NULL,
  `customer_group_id` SMALLINT(5) UNSIGNED NOT NULL,
  `value` CHAR(255) NOT NULL,
  PRIMARY KEY  (`entity_id`,`store_id`,`customer_group_id`),
  KEY `IDX_STORE` (`store_id`),
  KEY `IDX_CUSTOMER_GROUP` (`customer_group_id`),
  CONSTRAINT `FK_ENTERPRISE_TARGETRULE_INDEX_CROSSSELL_CUSTOMER_GROUP` FOREIGN KEY (`customer_group_id`)
    REFERENCES `{$installer->getTable('customer/customer_group')}` (`customer_group_id`)
      ON DELETE CASCADE
      ON UPDATE CASCADE,
  CONSTRAINT `FK_ENTERPRISE_TARGETRULE_INDEX_CROSSSELL_PRODUCT` FOREIGN KEY (`entity_id`)
    REFERENCES `{$installer->getTable('catalog/product')}` (`entity_id`)
      ON DELETE CASCADE
      ON UPDATE CASCADE,
  CONSTRAINT `FK_ENTERPRISE_TARGETRULE_INDEX_CROSSSELL_STORE` FOREIGN KEY (`store_id`)
    REFERENCES `{$installer->getTable('core/store')}` (`store_id`)
      ON DELETE CASCADE
      ON UPDATE CASCADE
) ENGINE=INNODB DEFAULT CHARSET=utf8;
");

$installer->endSetup();
