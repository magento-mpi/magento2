<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */

$taxClasses = Mage::getResourceModel('Magento_Tax_Model_Resource_TaxClass_Collection')->toArray();
$taxClass = reset($taxClasses['items']);

return array(
    'type_id' => Magento_Catalog_Model_Product_Type::TYPE_CONFIGURABLE,
    'sku' => 'configurable_' . uniqid(),
    'name' => 'Test Configurable ' . uniqid(),
    'description' => 'Test description',
    'short_description' => 'Test short description',
    'status' => Magento_Catalog_Model_Product_Status::STATUS_ENABLED,
    'visibility' => Magento_Catalog_Model_Product_Visibility::VISIBILITY_BOTH,
    'price' => 25.50,
    'tax_class_id' => $taxClass['class_id']
);
