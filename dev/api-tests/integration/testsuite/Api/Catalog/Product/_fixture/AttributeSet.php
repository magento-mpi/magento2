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

$attrSetApi = new Mage_Catalog_Model_Product_Attribute_Set_Api();
Magento_Test_Webservice::setFixture('testAttributeSetId', $attrSetApi->create('Test Attribute Set Fixture ' . mt_rand(1000, 9999), 4));

$attributeSetFixture = simplexml_load_file(dirname(__FILE__).'/xml/AttributeSet.xml');
$data = Magento_Test_Webservice::simpleXmlToArray($attributeSetFixture->AttributeEntityToCreate);
$data['attribute_code'] = $data['attribute_code'] . '_' . mt_rand(1000, 9999);

$testAttributeSetAttrIdsArray = array();

$attrApi = new Mage_Catalog_Model_Product_Attribute_Api();
$testAttributeSetAttrIdsArray[0] = $attrApi->create($data);
Magento_Test_Webservice::setFixture('testAttributeSetAttrIdsArray', $testAttributeSetAttrIdsArray);
