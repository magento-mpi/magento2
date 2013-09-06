<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */

$installer = $this;
/** @var $installer Magento_Catalog_Model_Resource_Setup */

$installer->updateAttribute(
    Magento_Catalog_Model_Product::ENTITY,
    'msrp_enabled',
    'source_model',
    'Magento_Catalog_Model_Product_Attribute_Source_Msrp_Type_Enabled'
);

$installer->updateAttribute(
    Magento_Catalog_Model_Product::ENTITY,
    'msrp_enabled',
    'default_value',
    Magento_Catalog_Model_Product_Attribute_Source_Msrp_Type_Enabled::MSRP_ENABLE_USE_CONFIG
);

$installer->updateAttribute(
    Magento_Catalog_Model_Product::ENTITY,
    'msrp_display_actual_price_type',
    'source_model',
    'Magento_Catalog_Model_Product_Attribute_Source_Msrp_Type_Price'
);

$installer->updateAttribute(
    Magento_Catalog_Model_Product::ENTITY,
    'msrp_display_actual_price_type',
    'default_value',
    Magento_Catalog_Model_Product_Attribute_Source_Msrp_Type_Price::TYPE_USE_CONFIG
);
