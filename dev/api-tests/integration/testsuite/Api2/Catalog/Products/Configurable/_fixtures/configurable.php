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
/** @var $configurableProduct Mage_Catalog_Model_Product */
$configurableProduct = require TESTS_FIXTURES_DIRECTORY . '/_block/Catalog/Product.php';
$taxClassTaxableGoods = 2;
$configurableProduct
    ->setName('Configurable Product')
    ->setSku('configurable-product-' . microtime())
    ->setAttributeSetId($attributeSet->getId())
    ->setTypeId(Mage_Catalog_Model_Product_Type_Configurable::TYPE_CODE)
    ->setWebsiteIds(array(Mage::app()->getDefaultStoreView()->getWebsiteId()))
    ->setTaxClassId($taxClassTaxableGoods)
    ->setStoreId(0);

$configurableAttributesData = array();
for ($attributeCount = 1; $attributeCount <= ATTRIBUTES_COUNT; $attributeCount++) {
    /** @var $attribute Mage_Catalog_Model_Resource_Eav_Attribute */
    $attribute = Magento_Test_Webservice::getFixture("eav_configurable_attribute_$attributeCount");
    $configurableAttributesData[$attribute->getAttributeCode()] = array(
        'attribute_id' => $attribute->getId(),
        'attribute_code' => $attribute->getAttributeCode(),
        'label' => $attribute->getFrontendLabel(),
        'position' => rand(0, 1)
    );
}
$configurableProduct->setConfigurableAttributesData($configurableAttributesData);
$configurableProduct->save();
// remove configurable attributes data from model to avoid MySQL errors during save
$configurableProduct->unsetData('configurable_attributes_data');
Magento_Test_Webservice::setFixture('product_configurable', $configurableProduct);
