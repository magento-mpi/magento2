<?php
/**
 * {license_notice}
 *
 * @category    Paas
 * @package     tests
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

include "attribute_set.php";
/** @var $attributeSet Mage_Eav_Model_Entity_Attribute_Set */
$attributeSet = Magento_Test_Webservice::getFixture('attribute_set_with_configurable');
$fixturesDir = realpath(dirname(__FILE__) . '/../../../../../../fixture');
$simpleProduct = require $fixturesDir . '/Catalog/Product.php';
$simpleProduct->setAttributeSetId($attributeSet->getId());
// set configurable attributes values
for ($attributeCount = 1; $attributeCount <= ATTRIBUTES_COUNT; $attributeCount++) {
    /** @var $attribute Mage_Catalog_Model_Resource_Eav_Attribute */
    $attribute = Magento_Test_Webservice::getFixture("eav_configurable_attribute_$attributeCount");
    $lastOption = end($attribute->getSource()->getAllOptions());
    $simpleProduct->setData($attribute->getAttributeCode(), $lastOption['value']);
}
$simpleProduct->save();
Magento_Test_Webservice::setFixture('product_simple', $simpleProduct);
