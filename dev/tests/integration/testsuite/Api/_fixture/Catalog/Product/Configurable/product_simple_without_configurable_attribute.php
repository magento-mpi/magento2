<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

include "attribute_set.php";
/** @var $attributeSet Mage_Eav_Model_Entity_Attribute_Set */
$attributeSet = Magento_Test_TestCase_ApiAbstract::getFixture('attribute_set_with_configurable');
$simpleProduct = require '_fixture/_block/Catalog/Product.php';
$simpleProduct->setAttributeSetId($attributeSet->getId());
$simpleProduct->save();
Magento_Test_TestCase_ApiAbstract::setFixture('product_simple', $simpleProduct);
