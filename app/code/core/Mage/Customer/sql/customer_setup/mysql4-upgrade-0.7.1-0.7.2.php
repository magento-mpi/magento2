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
 * @package    Mage_Customer
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * FOREIGN KEY update
 *
 * @category   Mage
 * @package    Mage_Customer
 * @author     Victor Tihonchuk <victor@varien.com>
 */

$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */

$installer->startSetup();

$installer->run("
ALTER TABLE {$this->getTable('customer_address_entity')}
    DROP `store_id`;
");
$installer->run("
ALTER TABLE {$this->getTable('customer_address_entity_datetime')}
    DROP FOREIGN KEY `FK_CUSTOMER_ADDRESS_DATETIME_STORE`;
ALTER TABLE {$this->getTable('customer_address_entity_datetime')}
    DROP INDEX `FK_CUSTOMER_ADDRESS_DATETIME_STORE`;
ALTER TABLE {$this->getTable('customer_address_entity_datetime')}
    DROP `store_id`;
");
$installer->run("
ALTER TABLE {$this->getTable('customer_address_entity_decimal')}
    DROP FOREIGN KEY `FK_CUSTOMER_ADDRESS_DECIMAL_STORE`;
ALTER TABLE {$this->getTable('customer_address_entity_decimal')}
    DROP INDEX `FK_CUSTOMER_ADDRESS_DECIMAL_STORE`;
ALTER TABLE {$this->getTable('customer_address_entity_decimal')}
    DROP `store_id`;
");
$installer->run("
ALTER TABLE {$this->getTable('customer_address_entity_int')}
    DROP FOREIGN KEY `FK_CUSTOMER_ADDRESS_INT_STORE`;
ALTER TABLE {$this->getTable('customer_address_entity_int')}
    DROP INDEX `FK_CUSTOMER_ADDRESS_INT_STORE`;
ALTER TABLE {$this->getTable('customer_address_entity_int')}
    DROP `store_id`;
");
$installer->run("
ALTER TABLE {$this->getTable('customer_address_entity_text')}
    DROP FOREIGN KEY `FK_CUSTOMER_ADDRESS_TEXT_STORE`;
ALTER TABLE {$this->getTable('customer_address_entity_text')}
    DROP INDEX `FK_CUSTOMER_ADDRESS_TEXT_STORE`;
ALTER TABLE {$this->getTable('customer_address_entity_text')}
    DROP `store_id`;
");
$installer->run("
ALTER TABLE {$this->getTable('customer_address_entity_varchar')}
    DROP FOREIGN KEY `FK_CUSTOMER_ADDRESS_VARCHAR_STORE`;
ALTER TABLE {$this->getTable('customer_address_entity_varchar')}
    DROP INDEX `FK_CUSTOMER_ADDRESS_VARCHAR_STORE`;
ALTER TABLE {$this->getTable('customer_address_entity_varchar')}
    DROP `store_id`;
");
$installer->run("
ALTER TABLE {$this->getTable('customer_entity')}
    DROP INDEX `FK_CUSTOMER_ENTITY_STORE`;
ALTER TABLE {$this->getTable('customer_entity')}
    CHANGE `store_id` `store_id` smallint(5) unsigned NULL DEFAULT '0';
ALTER TABLE {$this->getTable('customer_entity')}
    ADD CONSTRAINT `FK_CUSTOMER_ENTITY_STORE` FOREIGN KEY (`store_id`)
    REFERENCES {$this->getTable('core_store')} (`store_id`)
        ON UPDATE CASCADE
        ON DELETE SET NULL;
");
$installer->run("
ALTER TABLE {$this->getTable('customer_entity')}
    DROP INDEX `FK_CUSTOMER_ENTITY_ENTITY_TYPE`,
    ADD INDEX `IDX_ENTITY_TYPE` (`entity_type_id`);
");
$installer->run("
ALTER TABLE {$this->getTable('customer_entity')}
    DROP INDEX `FK_CUSTOMER_ENTITY_PARENT_ENTITY`,
    ADD INDEX `IDX_PARENT_ENTITY` (`parent_id`);
");
$installer->run("
ALTER TABLE {$this->getTable('customer_entity_datetime')}
    DROP FOREIGN KEY `FK_CUSTOMER_DATETIME_STORE`;
ALTER TABLE {$this->getTable('customer_entity_datetime')}
    DROP INDEX `FK_CUSTOMER_DATETIME_STORE`;
ALTER TABLE {$this->getTable('customer_entity_datetime')}
    DROP `store_id`;
");
$installer->run("
ALTER TABLE {$this->getTable('customer_entity_decimal')}
    DROP FOREIGN KEY `FK_CUSTOMER_DECIMAL_STORE`;
ALTER TABLE {$this->getTable('customer_entity_decimal')}
    DROP INDEX `FK_CUSTOMER_DECIMAL_STORE`;
ALTER TABLE {$this->getTable('customer_entity_decimal')}
    DROP `store_id`;
");
$installer->run("
ALTER TABLE {$this->getTable('customer_entity_int')}
    DROP FOREIGN KEY `FK_CUSTOMER_INT_STORE`;
ALTER TABLE {$this->getTable('customer_entity_int')}
    DROP INDEX `FK_CUSTOMER_INT_STORE`;
ALTER TABLE {$this->getTable('customer_entity_int')}
    DROP `store_id`;
");
$installer->run("
ALTER TABLE {$this->getTable('customer_entity_text')}
    DROP FOREIGN KEY `FK_CUSTOMER_TEXT_STORE`;
ALTER TABLE {$this->getTable('customer_entity_text')}
    DROP INDEX `FK_CUSTOMER_TEXT_STORE`;
ALTER TABLE {$this->getTable('customer_entity_text')}
    DROP `store_id`;
");
$installer->run("
ALTER TABLE {$this->getTable('customer_entity_varchar')}
    DROP FOREIGN KEY `FK_CUSTOMER_VARCHAR_STORE`;
ALTER TABLE {$this->getTable('customer_entity_varchar')}
    DROP INDEX `FK_CUSTOMER_VARCHAR_STORE`;
ALTER TABLE {$this->getTable('customer_entity_varchar')}
    DROP `store_id`;
");

$installer->endSetup();