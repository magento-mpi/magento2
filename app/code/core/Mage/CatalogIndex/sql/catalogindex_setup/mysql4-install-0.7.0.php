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
 * @package    Mage_CatalogIndex
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */

$installer->startSetup();

$installer->run("
CREATE TABLE `{$this->getTable('catalogindex_eav')}` (
`index_id` INT( 10 ) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`store_id` SMALLINT( 5 ) UNSIGNED NOT NULL,
`entity_id` INT( 10 ) UNSIGNED NOT NULL,
`attribute_id` SMALLINT( 5 ) UNSIGNED NOT NULL ,
`value` INT( 11 ) NOT NULL,

INDEX(`value`),

KEY `FK_CATALOGINDEX_EAV_ENTITY` (`entity_id`),
KEY `FK_CATALOGINDEX_EAV_ATTRIBUTE` (`attribute_id`),
KEY `FK_CATALOGINDEX_EAV_STORE` (`store_id`),

CONSTRAINT `FK_CATALOGINDEX_EAV_ENTITY` FOREIGN KEY (`entity_id`) REFERENCES
 {$this->getTable('catalog_product_entity')} (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,

CONSTRAINT `FK_CATALOGINDEX_EAV_ATTRIBUTE` FOREIGN KEY (`attribute_id`)
 REFERENCES {$this->getTable('eav_attribute')} (`attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE,

CONSTRAINT `FK_CATALOGINDEX_EAV_STORE` FOREIGN KEY (`store_id`)
 REFERENCES {$this->getTable('core_store')} (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE

) ENGINE=InnoDB DEFAULT CHARSET=utf8;






CREATE TABLE `{$this->getTable('catalogindex_price')}` (
`index_id` INT( 10 ) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`store_id` SMALLINT( 5 ) UNSIGNED NOT NULL,
`entity_id` INT( 10 ) UNSIGNED NOT NULL,
`attribute_id` SMALLINT( 5 ) UNSIGNED NOT NULL ,
`customer_group_id` SMALLINT( 3 ) UNSIGNED NOT NULL,
`qty` DECIMAL( 12,4 ) UNSIGNED NOT NULL,
`value` DECIMAL( 12,4 ) NOT NULL,

INDEX(`value`),
INDEX(`qty`),

KEY `FK_CATALOGINDEX_PRICE_ENTITY` (`entity_id`),
KEY `FK_CATALOGINDEX_PRICE_ATTRIBUTE` (`attribute_id`),
KEY `FK_CATALOGINDEX_PRICE_STORE` (`store_id`),
KEY `FK_CATALOGINDEX_PRICE_CUSTOMER_GROUP` (`customer_group_id`),

CONSTRAINT `FK_CATALOGINDEX_PRICE_ENTITY` FOREIGN KEY (`entity_id`) REFERENCES
 {$this->getTable('catalog_product_entity')} (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,

CONSTRAINT `FK_CATALOGINDEX_PRICE_ATTRIBUTE` FOREIGN KEY (`attribute_id`)
 REFERENCES {$this->getTable('eav_attribute')} (`attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE,

CONSTRAINT `FK_CATALOGINDEX_PRICE_STORE` FOREIGN KEY (`store_id`)
 REFERENCES {$this->getTable('core_store')} (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE,

CONSTRAINT `FK_CATALOGINDEX_PRICE_CUSTOMER_GROUP` FOREIGN KEY (`customer_group_id`)
 REFERENCES {$this->getTable('customer_group')} (`customer_group_id`) ON DELETE CASCADE ON UPDATE CASCADE

) ENGINE=InnoDB DEFAULT CHARSET=utf8;

");

$installer->endSetup();
