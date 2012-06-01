<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Core
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Api_Catalog_Product_AttributeSetCRUDTest extends Magento_Test_Webservice
{
    /**
     * Remove attribute set
     *
     * @param int $attrSetId
     */
    protected function _removeAttrSet($attrSetId)
    {
        /** @var $attrSet Mage_Eav_Model_Entity_Attribute_Set */
        $attrSet = Mage::getModel('Mage_Eav_Model_Entity_Attribute_Set');

        $attrSet->setId($attrSetId);
        $attrSet->delete();
    }

    /**
     * Remove attributes
     *
     * @param array $attrIds
     */
    protected function _removeAttributes($attrIds)
    {
        /** @var $attr Mage_Eav_Model_Entity_Attribute */
        $attr = Mage::getModel('Mage_Eav_Model_Entity_Attribute');

        if (!is_array($attrIds)) {
            $attrIds = array($attrIds);
        }
        foreach ($attrIds as $attrId) {
            $attr->setId($attrId);
            $attr->delete();
        }
    }

    /**
     * Test Attribute set CRUD
     *
     * @return void
     */
    public function testAttributeSetCRUD()
    {
        $attributeSetFixture = simplexml_load_file(dirname(__FILE__) . '/_fixture/xml/AttributeSet.xml');
        $data = self::simpleXmlToArray($attributeSetFixture->create);
        $data['attributeSetName'] = $data['attributeSetName'] . ' ' . mt_rand(1000, 9999);

        // create test
        $createdAttrSetId = $this->call('product_attribute_set.create', $data);
        $this->assertGreaterThan(0, $createdAttrSetId);

        // Duplicate name exception test
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
            'type' => $productData['typeId'],
            'set' => $createdAttrSetId,
            'sku' => $productData['sku'],
            'productData' => $productData['productData']
        ));

        try {
            $this->call('product_attribute_set.remove', array('attributeSetId' => $createdAttrSetId));
            $this->fail("Didn't receive exception!");
        } catch (Exception $e) { }

        $this->call('product.delete', array('productId' => $productId));

        // delete test
        $attributeSetDelete = $this->call('product_attribute_set.remove', array('attributeSetId' => $createdAttrSetId));
        $this->assertTrue((bool)$attributeSetDelete, "Can't delete added attribute set");

        // Test delete undefined attribute set and check successful delete in previous call
        try {
            $this->call('product_attribute_set.remove', array('attributeSetId' => $createdAttrSetId));
            $this->fail("Didn't receive exception!");
        } catch (Exception $e) { }

    }

    /**
     * Test attribute CRUD in attribute set
     *
     * @magentoDataFixture Api/Catalog/Product/_fixture/AttributeSet.php
     * @return void
     */
    public function testAttributeSetAttrCRUD()
    {
        $testAttributeSetId = self::getFixture('testAttributeSetId');
        $testAttributeSetAttrIdsArray = self::getFixture('testAttributeSetAttrIdsArray');

        // add attribute test
        $addResult = $this->call('product_attribute_set.attributeAdd',
            array('attributeId' => $testAttributeSetAttrIdsArray[0], 'attributeSetId' => $testAttributeSetId));
        $this->assertTrue((bool)$addResult);

        // delete attribute test
        $removeResult = $this->call('product_attribute_set.attributeRemove',
            array('attributeId' => $testAttributeSetAttrIdsArray[0], 'attributeSetId' => $testAttributeSetId));
        $this->assertTrue((bool)$removeResult);

        $this->_removeAttrSet(self::getFixture('testAttributeSetId'));
        $this->_removeAttributes(self::getFixture('testAttributeSetAttrIdsArray'));
    }

    /**
     * Test group of attribute sets CRUD
     *
     * @magentoDataFixture Api/Catalog/Product/_fixture/AttributeSet.php
     * @return void
     */
    public function testAttributeSetGroupCRUD()
    {
        $testAttributeSetId = self::getFixture('testAttributeSetId');
        $attributeSetFixture = simplexml_load_file(dirname(__FILE__) . '/_fixture/xml/AttributeSet.xml');
        $data = self::simpleXmlToArray($attributeSetFixture->groupAdd);

        // add group test
        $createdAttributeSetGroupId = $this->call('product_attribute_set.groupAdd',
            array('attributeSetId' => $testAttributeSetId, 'groupName' => $data['groupName']));
        $this->assertGreaterThan(0, $createdAttributeSetGroupId);

        // add already exist group exception test
        try {
            $createdAttributeSetGroupId = $this->call('product_attribute_set.groupAdd',
                array('attributeSetId' => $testAttributeSetId, 'groupName' => $data['existsGroupName']));
            $this->fail("Didn't receive exception!");
        } catch (Exception $e) { }

        // rename group test
        $groupName = $data['groupName'] . ' ' . mt_rand(1000, 9999);
        $renameResult = $this->call('product_attribute_set.groupRename',
            array('groupId' => $createdAttributeSetGroupId, 'groupName' => $groupName));
        $this->assertTrue((bool)$renameResult);

        // rename group exception test
        try {
            $this->call('product_attribute_set.groupRename',
                array('groupId' => $createdAttributeSetGroupId, 'groupName' => $data['existsGroupName']));
            $this->fail("Didn't receive exception!");
        } catch (Exception $e) { }

        // remove group test
        $removeResult = $this->call('product_attribute_set.groupRemove',
            array('attributeGroupId' => $createdAttributeSetGroupId));
        $this->assertTrue((bool)$removeResult);

        $this->_removeAttrSet($testAttributeSetId);
        $this->_removeAttributes(self::getFixture('testAttributeSetAttrIdsArray'));

        // remove undefined group exception test
        $this->setExpectedException(self::DEFAULT_EXCEPTION);
        $this->call('product_attribute_set.groupRemove', array('attributeGroupId' => $createdAttributeSetGroupId));
    }
}
