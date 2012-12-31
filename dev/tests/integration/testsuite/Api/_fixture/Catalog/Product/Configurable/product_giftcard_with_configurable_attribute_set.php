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
$giftcard = require '_fixture/_block/Catalog/Product.php';
$giftcard->setAttributeSetId($attributeSet->getId())
    ->setName('Giftcard Product')
    ->setSku('giftcard-product-' . microtime())
    ->setTypeId(Enterprise_GiftCard_Model_Catalog_Product_Type_Giftcard::TYPE_GIFTCARD);
// set configurable attributes values
for ($attributeCount = 1; $attributeCount <= ATTRIBUTES_COUNT; $attributeCount++) {
    /** @var $attribute Mage_Catalog_Model_Resource_Eav_Attribute */
    $attribute = Mage::registry("eav_configurable_attribute_$attributeCount");
    $lastOption = end($attribute->getSource()->getAllOptions());
    $giftcard->setData($attribute->getAttributeCode(), $lastOption['value']);
}
$giftcard->save();

Mage::register('product_giftcard', $giftcard);
