<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Customer
 */

/** @var $installer Mage_Customer_Model_Resource_Setup */
$installer = $this;

$installer->cleanCache();

$installer->updateAttribute(
    'customer_address', 'street', 'backend_model', 'Mage_Eav_Model_Entity_Attribute_Backend_Default'
);
