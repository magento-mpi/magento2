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
$downloadable = require $fixturesDir . '/Catalog/Product.php';
$downloadable->setAttributeSetId($attributeSet->getId())
    ->setName('Downloadable Product')
    ->setSku('downloadable-product-' . microtime())
    ->setTypeId(Mage_Downloadable_Model_Product_Type::TYPE_DOWNLOADABLE);
// set configurable attributes values
for ($attributeCount = 1; $attributeCount <= ATTRIBUTES_COUNT; $attributeCount++) {
    /** @var $attribute Mage_Catalog_Model_Resource_Eav_Attribute */
    $attribute = Magento_Test_Webservice::getFixture("eav_configurable_attribute_$attributeCount");
    $lastOption = end($attribute->getSource()->getAllOptions());
    $downloadable->setData($attribute->getAttributeCode(), $lastOption['value']);
}
$downloadable->save();
Magento_Test_Webservice::setFixture('product_downloadable', $downloadable);
