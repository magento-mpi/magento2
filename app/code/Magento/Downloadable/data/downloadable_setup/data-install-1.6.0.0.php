<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Downloadable
 * @copyright   {copyright}
 * @license     {license_link}
 */
/** @var $installer Magento_Downloadable_Model_Resource_Setup */
$installer = $this;

$fieldList = array(
    'price',
    'special_price',
    'special_from_date',
    'special_to_date',
    'minimal_price',
    'cost',
    'tier_price'
);

// make these attributes applicable to downloadable products
foreach ($fieldList as $field) {
    $applyTo = explode(',', $installer->getAttribute(Magento_Catalog_Model_Product::ENTITY, $field, 'apply_to'));
    if (!in_array('downloadable', $applyTo)) {
        $applyTo[] = 'downloadable';
        $installer->updateAttribute(Magento_Catalog_Model_Product::ENTITY, $field, 'apply_to', implode(',', $applyTo));
    }
}
 
