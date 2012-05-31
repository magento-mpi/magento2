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
include "configurable.php";
define('CONFIGURABLE_ASSIGNED_PRODUCTS_COUNT', 2);
/** @var $configurableProduct Mage_Catalog_Model_Product */
$configurableProduct = Magento_Test_Webservice::getFixture('product_configurable');

$fixturesDir = realpath(dirname(__FILE__) . '/../../../../../../fixture');
$productsToAssignIds = array();
for ($i = 0; $i < CONFIGURABLE_ASSIGNED_PRODUCTS_COUNT; $i++) {
    /* @var $product Mage_Catalog_Model_Product */
    $product = require $fixturesDir . '/Catalog/Product.php';
    $product->setName("Assigned product #$i")
        ->setAttributeSetId($configurableProduct->getAttributeSetId())
        ->setWebsiteIds($configurableProduct->getWebsiteIds());
    // set configurable attributes values
    for ($attributeCount = 1; $attributeCount <= ATTRIBUTES_COUNT; $attributeCount++) {
        /** @var $attribute Mage_Catalog_Model_Resource_Eav_Attribute */
        $attribute = Magento_Test_Webservice::getFixture("eav_configurable_attribute_$attributeCount");
        $lastOption = end($attribute->getSource()->getAllOptions());
        $product->setData($attribute->getAttributeCode(), $lastOption['value']);
    }
    $product->save();
    $productsToAssignIds[$product->getId()] = $product->getId();
    Magento_Test_Webservice::setFixture("configurable_assigned_product_$i", $product);
}
$configurableProduct->setConfigurableProductsData($productsToAssignIds)->save();
$configurableProduct->save();
// reload configurable product data after adding of associated products
$configurableProduct = Mage::getModel('Mage_Catalog_Model_Product')->load($configurableProduct->getId());

// set option prices
/** @var $configurableType Mage_Catalog_Model_Product_Type_Configurable */
$configurableType = $configurableProduct->setStoreId(0)->getTypeInstance();
$configurableAttributes = $configurableType->getConfigurableAttributesAsArray($configurableProduct);
foreach ($configurableAttributes as &$configurableAttribute) {
    foreach ($configurableAttribute['values'] as &$value) {
        // generate price from 1.00 to 100.00
        $value['pricing_value'] = rand(100, 10000) / 100;
        $value['is_percent'] = rand(0, 1);
    }
}
$configurableProduct->setConfigurableAttributesData($configurableAttributes)
    ->setCanSaveConfigurableAttributes(true)
    ->save();

// reload configurable product data after adding prices
$configurableProduct = Mage::getModel('Mage_Catalog_Model_Product')->load($configurableProduct->getId());
Magento_Test_Webservice::setFixture("product_configurable", $configurableProduct);

