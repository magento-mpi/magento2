<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Paas
 * @package     tests
 * @subpackage  integration_tests
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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
