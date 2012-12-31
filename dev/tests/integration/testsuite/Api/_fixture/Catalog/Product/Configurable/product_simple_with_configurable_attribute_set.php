<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

include "attribute_set.php";
/** @var $attributeSet Mage_Eav_Model_Entity_Attribute_Set */
$attributeSet = Mage::registry('attribute_set_with_configurable');
$simpleProduct = require '_fixture/_block/Catalog/Product.php';
$simpleProduct->setAttributeSetId($attributeSet->getId());
// set configurable attributes values
for ($attributeCount = 1; $attributeCount <= ATTRIBUTES_COUNT; $attributeCount++) {
    /** @var $attribute Mage_Catalog_Model_Resource_Eav_Attribute */
    $attribute = Mage::registry("eav_configurable_attribute_$attributeCount");
    $lastOption = end($attribute->getSource()->getAllOptions());
    $simpleProduct->setData($attribute->getAttributeCode(), $lastOption['value']);
}
$simpleProduct->save();
Mage::register('product_simple', $simpleProduct);
