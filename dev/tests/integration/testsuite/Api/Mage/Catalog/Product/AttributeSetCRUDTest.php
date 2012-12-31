<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Catalog_Product_AttributeSetCRUDTest extends PHPUnit_Framework_TestCase
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
        $attributeSetFixture = simplexml_load_file(dirname(__FILE__) . '/_fixture/_data/xml/AttributeSet.xml');
        $data = Magento_Test_Helper_Api::simpleXmlToArray($attributeSetFixture->create);
        $data->attributeSetName = $data->attributeSetName . ' ' . mt_rand(1000, 9999);

        // create test
        $createdAttrSetId = Magento_Test_Helper_Api::call(
            $this,
            'catalogProductAttributeSetCreate',
            array($data->attributeSetName, $data->skeletonSetId)
        );
        $this->assertGreaterThan(0, $createdAttrSetId);

        // Duplicate name exception test
        try {
            Magento_Test_Helper_Api::call(
                $this,
                'catalogProductAttributeSetCreate',
                array($data->attributeSetName, $data->skeletonSetId)
            );
            $this->fail("Didn't receive exception!");
        } catch (Exception $e) {
        }

        // items list test
        $attrSetList = Magento_Test_Helper_Api::call($this, 'catalogProductAttributeSetList');
        $completeFlag = false;
        foreach ($attrSetList as $attrSet) {
            if ($attrSet['set_id'] == $createdAttrSetId) {
                $this->assertEquals($data->attributeSetName, $attrSet['name']);
                $completeFlag = true;
                break;
            }
        }
        $this->assertTrue($completeFlag, "Can't find added attribute set in list");

        // Remove AttrSet with related products
        $productData = Magento_Test_Helper_Api::simpleXmlToArray($attributeSetFixture->RelatedProduct);
        $productData->sku = $productData->sku . '_' . mt_rand(1000, 9999);
        $productId = Magento_Test_Helper_Api::call(
            $this,
            'catalogProductCreate',
            array(
                'type' => $productData->typeId,
                'set' => $createdAttrSetId,
                'sku' => $productData->sku,
                'productData' => $productData->productData
            )
        );

        try {
            Magento_Test_Helper_Api::call(
                $this,
                'catalogProductAttributeSetRemove',
                array('attributeSetId' => $createdAttrSetId)
            );
            $this->fail("Didn't receive exception!");
        } catch (Exception $e) {
        }

        Magento_Test_Helper_Api::call($this, 'catalogProductDelete', array('productId' => $productId));

        // delete test
        $attributeSetDelete = Magento_Test_Helper_Api::call(
            $this,
            'catalogProductAttributeSetRemove',
            array('attributeSetId' => $createdAttrSetId)
        );
        $this->assertTrue((bool)$attributeSetDelete, "Can't delete added attribute set");

        // Test delete undefined attribute set and check successful delete in previous call
        try {
            Magento_Test_Helper_Api::call(
                $this,
                'catalogProductAttributeSetRemove',
                array('attributeSetId' => $createdAttrSetId)
            );
            $this->fail("Didn't receive exception!");
        } catch (Exception $e) {
        }

    }

    /**
     * Test attribute CRUD in attribute set
     *
     * @magentoDataFixture Api/Mage/Catalog/Product/_fixture/AttributeSet.php
     * @return void
     */
    public function testAttributeSetAttrCRUD()
    {
        $testAttributeSetId = Mage::registry('testAttributeSetId');
        $testAttributeSetAttrIdsArray = Mage::registry('testAttributeSetAttrIdsArray');

        // add attribute test
        $addResult = Magento_Test_Helper_Api::call(
            $this,
            'catalogProductAttributeSetAttributeAdd',
            array('attributeId' => $testAttributeSetAttrIdsArray[0], 'attributeSetId' => $testAttributeSetId)
        );
        $this->assertTrue((bool)$addResult);

        // delete attribute test
        $removeResult = Magento_Test_Helper_Api::call(
            $this,
            'catalogProductAttributeSetAttributeRemove',
            array('attributeId' => $testAttributeSetAttrIdsArray[0], 'attributeSetId' => $testAttributeSetId)
        );
        $this->assertTrue((bool)$removeResult);
    }

    /**
     * Test group of attribute sets CRUD
     *
     * @magentoDataFixture Api/Mage/Catalog/Product/_fixture/AttributeSet.php
     * @return void
     */
    public function testAttributeSetGroupCRUD()
    {
        $testAttributeSetId = Mage::registry('testAttributeSetId');
        $attributeSetFixture = simplexml_load_file(dirname(__FILE__) . '/_fixture/_data/xml/AttributeSet.xml');
        $data = Magento_Test_Helper_Api::simpleXmlToArray($attributeSetFixture->groupAdd);

        // add group test
        $createdAttributeSetGroupId = Magento_Test_Helper_Api::call(
            $this,
            'catalogProductAttributeSetGroupAdd',
            array('attributeSetId' => $testAttributeSetId, 'groupName' => $data->groupName)
        );
        $this->assertGreaterThan(0, $createdAttributeSetGroupId);

        // add already exist group exception test
        try {
            $createdAttributeSetGroupId = Magento_Test_Helper_Api::call(
                $this,
                'catalogProductAttributeSetGroupAdd',
                array('attributeSetId' => $testAttributeSetId, 'groupName' => $data->existsGroupName)
            );
            $this->fail("Didn't receive exception!");
        } catch (Exception $e) {
        }

        // rename group test
        $groupName = $data->groupName . ' ' . mt_rand(1000, 9999);
        $renameResult = Magento_Test_Helper_Api::call(
            $this,
            'catalogProductAttributeSetGroupRename',
            array('groupId' => $createdAttributeSetGroupId, 'groupName' => $groupName)
        );
        $this->assertTrue((bool)$renameResult);

        // rename group exception test
        try {
            Magento_Test_Helper_Api::call(
                $this,
                'catalogProductAttributeSetGroupRename',
                array('groupId' => $createdAttributeSetGroupId, 'groupName' => $data->existsGroupName)
            );
            $this->fail("Didn't receive exception!");
        } catch (Exception $e) {
        }

        // remove group test
        $removeResult = Magento_Test_Helper_Api::call(
            $this,
            'catalogProductAttributeSetGroupRemove',
            array('attributeGroupId' => $createdAttributeSetGroupId)
        );
        $this->assertTrue((bool)$removeResult);

        $this->_removeAttrSet($testAttributeSetId);
        $this->_removeAttributes(Mage::registry('testAttributeSetAttrIdsArray'));

        // remove undefined group exception test
        $this->setExpectedException(self::DEFAULT_EXCEPTION);
        Magento_Test_Helper_Api::call(
            $this,
            'catalogProductAttributeSetGroupRemove',
            array('attributeGroupId' => $createdAttributeSetGroupId)
        );
    }
}
