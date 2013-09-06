<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftWrapping
 * @copyright   {copyright}
 * @license     {license_link}
 */

/* @var $installer Magento_GiftWrapping_Model_Resource_Setup */
$installer = $this;

$installer->updateAttribute(
    Magento_Catalog_Model_Product::ENTITY,
    'gift_wrapping_available',
    'frontend_class',
    'hidden-for-virtual'
);

$installer->updateAttribute(
    Magento_Catalog_Model_Product::ENTITY,
    'gift_wrapping_price',
    'frontend_class',
    'hidden-for-virtual'
);
