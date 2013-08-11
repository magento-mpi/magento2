<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */

/** @var Magento_Catalog_Model_Product_Attribute_Set_Api $attrSetApi */
$attrSetApi = Mage::getModel('Magento_Catalog_Model_Product_Attribute_Set_Api');
Mage::register(
    'testAttributeSetId',
    $attrSetApi->create('Test Attribute Set Fixture ' . mt_rand(1000, 9999), 4)
);

$attributeSetFixture = simplexml_load_file(__DIR__ . '/_data/xml/AttributeSet.xml');
$data = Magento_Test_Helper_Api::simpleXmlToArray($attributeSetFixture->attributeEntityToCreate);
$data['attribute_code'] = $data['attribute_code'] . '_' . mt_rand(1000, 9999);

$testAttributeSetAttrIdsArray = array();

$attrApi = Mage::getModel('Magento_Catalog_Model_Product_Attribute_Api');
$testAttributeSetAttrIdsArray[0] = $attrApi->create($data);
Mage::register('testAttributeSetAttrIdsArray', $testAttributeSetAttrIdsArray);
