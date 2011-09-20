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
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Magento
 * @package     Mage_Core
 * @subpackage  integration_tests
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magento.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * @magentoDataFixture Catalog/Product/_fixtures/AttributeSet.php
 */
class Catalog_Product_AttributeSetCRUDTest extends Magento_Test_Webservice
{
    public function testAttributeSetCRUD()
    {
        $attributeSetFixture = simplexml_load_file(dirname(__FILE__).'/_fixtures/xml/AttributeSet.xml');
        $data = self::simpleXmlToArray($attributeSetFixture->create);
        $data['attributeSetName'] = $data['attributeSetName'] . ' ' . mt_rand(1000, 9999);

        // create test
        $createdAttrSetId = $this->call('product_attribute_set.create', $data);
        $this->assertGreaterThan(0, $createdAttrSetId);

        // Dublicate name exception test
        try {
            $this->call('product_attribute_set.create', $data);
            $this->fail("Didn't receive exception!");
        } catch (Exception $e) { }

        // items list test
        $attrSetList = $this->call('product_attribute_set.list');

        $completeFlag = false;
        foreach ($attrSetList as $attrSet) {
            if ($attrSet['set_id'] == $createdAttrSetId) {
                $this->assertEquals($data['attributeSetName'], $attrSet['name']);
                $completeFlag = true;
                break;
            }
        }
        $this->assertTrue($completeFlag, "Can't find added attribute set in list");

        // Remove AttrSet with related products 
        $productData = self::simpleXmlToArray($attributeSetFixture->RelatedProduct);
        $productData['sku'] = $productData['sku'] . '_' . mt_rand(1000, 9999);
        $productId = $this->call('product.create', array(
                     $productData['typeId'],
                     $createdAttrSetId,
                     $productData['sku'],
                     $productData['productData']
                ));
        // Dublicate name exception test
        try {
            $this->call('product_attribute_set.remove', array($createdAttrSetId));
            $this->fail("Didn't receive exception!");
        } catch (Exception $e) { }
        $this->call('product.delete', array($productId));

        // delete test
        $attributeSetDelete = $this->call('product_attribute_set.remove', array($createdAttrSetId));
        $this->assertTrue($attributeSetDelete, "Can't delete added attribute set");

        // Delete exception test
        try {
            $this->call('product_attribute_set.remove', array($createdAttrSetId));
            $this->fail("Didn't receive exception!");
        } catch (Exception $e) { }

    }

    public function testAttributeSetAttrCRUD()
    {
        $testAttributeSetId = self::getFixture('testAttributeSetId');
        $testAttributeSetAttrIdsArray = self::getFixture('testAttributeSetAttrIdsArray');

        // attributeAdd test
        $addResult = $this->call('product_attribute_set.attributeAdd',
            array($testAttributeSetAttrIdsArray[0], $testAttributeSetId));
        $this->assertTrue($addResult);

        // attributeRemove test
        $removeResult = $this->call('product_attribute_set.attributeRemove',
            array($testAttributeSetAttrIdsArray[0], $testAttributeSetId));
        $this->assertTrue($removeResult);
    }

    public function testAttributeSetGroupCRUD()
    {
        $testAttributeSetId = self::getFixture('testAttributeSetId');
        $attributeSetFixture = simplexml_load_file(dirname(__FILE__).'/_fixtures/xml/AttributeSet.xml');
        $data = self::simpleXmlToArray($attributeSetFixture->groupAdd);

        // groupAdd test
        $createdAttributeSetGroupId = $this->call('product_attribute_set.groupAdd',
            array($testAttributeSetId, $data['groupName']));
        $this->assertGreaterThan(0, $createdAttributeSetGroupId);

        // groupAdd exception test
        try {
            $createdAttributeSetGroupId = $this->call('product_attribute_set.groupAdd',
                array($testAttributeSetId, $data['existsGroupName']));
            $this->fail("Didn't receive exception!");
        } catch (Exception $e) { }

        // groupRename test
        $groupName = $data['groupName'] . ' ' . mt_rand(1000, 9999);
        $renameResult = $this->call('product_attribute_set.groupRename',
            array($createdAttributeSetGroupId, $groupName));
        $this->assertTrue($renameResult);

        // groupRename exception test
        try {
            $renameResult = $this->call('product_attribute_set.groupRename',
                array($createdAttributeSetGroupId, $data['existsGroupName']));
            $this->fail("Didn't receive exception!");
        } catch (Exception $e) { }

        // groupRemove test
        $removeResult = $this->call('product_attribute_set.groupRemove',
            array($createdAttributeSetGroupId));
        $this->assertTrue($removeResult);

        // groupRemove exception test
        try {
            $removeResult = $this->call('product_attribute_set.groupRemove',
                array($createdAttributeSetGroupId));
            $this->fail("Didn't receive exception!");
        } catch (Exception $e) { }
    }

}
