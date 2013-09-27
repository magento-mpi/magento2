<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GoogleCheckout
 * @copyright   {copyright}
 * @license     {license_link}
 */

/* @var $installer Magento_Catalog_Model_Resource_Setup */
$installer = $this;

$installer->updateAttribute(
    Magento_Catalog_Model_Product::ENTITY,
    'enable_googlecheckout',
    'frontend_label',
    'Is Product Available for Purchase with Google Checkout'
);
