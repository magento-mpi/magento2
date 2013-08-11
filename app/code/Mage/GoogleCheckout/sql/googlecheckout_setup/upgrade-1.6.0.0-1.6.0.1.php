<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_GoogleCheckout
 * @copyright   {copyright}
 * @license     {license_link}
 */

/* @var $installer Mage_GoogleCheckout_Model_Resource_Setup */
$installer = $this;

$installer->updateAttribute(
    Magento_Catalog_Model_Product::ENTITY,
    'enable_googlecheckout',
    'frontend_label',
    'Is Product Available for Purchase with Google Checkout'
);
