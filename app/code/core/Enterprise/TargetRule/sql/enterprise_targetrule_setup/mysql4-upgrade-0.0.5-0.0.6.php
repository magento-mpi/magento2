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
 * @category   Enterprise
 * @package    Enterprise_TargetRule
 * @copyright  Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://www.magentocommerce.com/license/enterprise-edition
 */


/* @var $installer Enterprise_TargetRule_Model_Mysql4_Setup */
$installer = $this;

$installer->startSetup();

$installer->removeAttribute('catalog_product', 'crosssell_targetrule_rule_based_positions');
$installer->removeAttribute('catalog_product', 'crosssell_targetrule_position_limit');
$installer->updateAttribute('catalog_product', 'related_targetrule_rule_based_positions',
    'attribute_code', 'related_targetrule_position_limit');
$installer->updateAttribute('catalog_product', 'upsell_targetrule_rule_based_positions',
    'attribute_code', 'upsell_targetrule_position_limit');

$installer->run("
CREATE TABLE `{$installer->getTable('enterprise_targetrule/index')}` (
  `entity_id` INT(10) UNSIGNED NOT NULL,
  `store_id` SMALLINT(5) UNSIGNED NOT NULL,
  `customer_group_id` SMALLINT(5) UNSIGNED NOT NULL,
  `type_id` tinyint(1) UNSIGNED NOT NULL,
  `flag` tinyint(1) UNSIGNED NOT NULL DEFAULT 1,
  PRIMARY KEY (`entity_id`,`store_id`,`customer_group_id`, `type_id`),
  KEY `IDX_STORE` (`store_id`),
  KEY `IDX_CUSTOMER_GROUP` (`customer_group_id`),
  KEY `IDX_TYPE` (`type_id`),
  CONSTRAINT `FK_ENTERPRISE_TARGETRULE_INDEX_CUSTOMER_GROUP` FOREIGN KEY (`customer_group_id`)
    REFERENCES `{$installer->getTable('customer/customer_group')}` (`customer_group_id`)
      ON DELETE CASCADE
      ON UPDATE CASCADE,
  CONSTRAINT `FK_ENTERPRISE_TARGETRULE_INDEX_PRODUCT` FOREIGN KEY (`entity_id`)
    REFERENCES `{$installer->getTable('catalog/product')}` (`entity_id`)
      ON DELETE CASCADE
      ON UPDATE CASCADE,
  CONSTRAINT `FK_ENTERPRISE_TARGETRULE_INDEX_STORE` FOREIGN KEY (`store_id`)
    REFERENCES `{$installer->getTable('core/store')}` (`store_id`)
      ON DELETE CASCADE
      ON UPDATE CASCADE
) ENGINE=INNODB DEFAULT CHARSET=utf8;
");

$installer->endSetup();
