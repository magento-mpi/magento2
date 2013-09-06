<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Downloadable
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $installer Magento_Catalog_Model_Resource_Setup */
$installer = $this;

$msrpEnabled = $installer->getAttribute('catalog_product', 'msrp_enabled', 'apply_to');
if ($msrpEnabled && strstr($msrpEnabled, Magento_Downloadable_Model_Product_Type::TYPE_DOWNLOADABLE) == false) {
    $installer->updateAttribute('catalog_product', 'msrp_enabled', array(
        'apply_to'      => $msrpEnabled . ',' . Magento_Downloadable_Model_Product_Type::TYPE_DOWNLOADABLE,
    ));
}

$msrpDisplay = $installer->getAttribute('catalog_product', 'msrp_display_actual_price_type', 'apply_to');
if ($msrpDisplay && strstr($msrpEnabled, Magento_Downloadable_Model_Product_Type::TYPE_DOWNLOADABLE) == false) {
    $installer->updateAttribute('catalog_product', 'msrp_display_actual_price_type', array(
        'apply_to'      => $msrpDisplay . ',' . Magento_Downloadable_Model_Product_Type::TYPE_DOWNLOADABLE,
    ));
}

$msrp = $installer->getAttribute('catalog_product', 'msrp', 'apply_to');
if ($msrp && strstr($msrpEnabled, Magento_Downloadable_Model_Product_Type::TYPE_DOWNLOADABLE) == false) {
    $installer->updateAttribute('catalog_product', 'msrp', array(
        'apply_to'      => $msrp . ',' . Magento_Downloadable_Model_Product_Type::TYPE_DOWNLOADABLE,
    ));
}
