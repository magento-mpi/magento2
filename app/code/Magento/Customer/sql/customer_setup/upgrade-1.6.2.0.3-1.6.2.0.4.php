<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $installer Magento_Customer_Model_Resource_Setup */
$installer = $this;

$installer->cleanCache();

$installer->updateAttribute(
    'customer_address', 'street', 'backend_model', 'Magento_Eav_Model_Entity_Attribute_Backend_Default'
);
