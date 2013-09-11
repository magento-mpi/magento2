<?php
/**
 * Product attribute set API model test.
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */
class Magento_Catalog_Model_Product_Api_AttributeSetCRUDTest extends PHPUnit_Framework_TestCase
{
    /**
     * Remove attribute set
     *
     * @param int $attrSetId
     */
    protected function _removeAttrSet($attrSetId)
    {
        /** @var $attrSet Magento_Eav_Model_Entity_Attribute_Set */
        $attrSet = Mage::getModel('Magento_Eav_Model_Entity_Attribute_Set');

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
        /** @var $attr Magento_Eav_Model_Entity_Attribute */
        $attr = Mage::getModel('Magento_Eav_Model_Entity_Attribute');

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
     * @magentoDbIsolation enabled
     */
    public function testAttributeSetCRUD()
    {
        $attributeSetFixture = simplexml_load_file(dirname(__FILE__) . '/_files/_data/xml/AttributeSet.xml');
        $data = Magento_TestFramework_Helper_Api::simpleXmlToArray($attributeSetFixture->create);
        $data['attributeSetName'] = $data['attributeSetName'] . ' ' . mt_rand(1000, 9999);

        // create test
        $createdAttrSetId = Magento_TestFramework_Helper_Api::call(
            $this,
            'catalogProductAttributeSetCreate',
            array($data['attributeSetName'], $data['skeletonSetId'])
        );
        $this->assertGreaterThan(0, $createdAttrSetId);

        // Duplicate name exception test
        Magento_TestFramework_Helper_Api::callWithException(
            $this,
            'catalogProductAttributeSetCreate',
            array($data['attributeSetName'], $data['skeletonSetId'])
        );

        // items list test
        $attrSetList = Magento_TestFramework_Helper_Api::call($this, 'catalogProductAttributeSetList');
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
        $productData = Magento_TestFramework_Helper_Api::simpleXmlToArray($attributeSetFixture->relatedProduct);
        $productData['sku'] = $productData['sku'] . '_' . mt_rand(1000, 9999);
        $productId = Magento_TestFramework_Helper_Api::call(
            $this,
            'catalogProductCreate',
            array(
                'type' => $productData['typeId'],
                'set' => $createdAttrSetId,
                'sku' => $productData['sku'],
                'productData' => $productData['productData']
            )
        );

        Magento_TestFramework_Helper_Api::callWithException(
            $this,
            'catalogProductAttributeSetRemove',
            array('attributeSetId' => $createdAttrSetId)
        );

        Magento_TestFramework_Helper_Api::call($this, 'catalogProductDelete', array('productId' => $productId));

        // delete test
        $attributeSetDelete = Magento_TestFramework_Helper_Api::call(
            $this,
            'catalogProductAttributeSetRemove',
            array('attributeSetId' => $createdAttrSetId)
        );
        $this->assertTrue((bool)$attributeSetDelete, "Can't delete added attribute set");

        // Test delete undefined attribute set and check successful delete in previous call
        Magento_TestFramework_Helper_Api::callWithException(
            $this,
            'catalogProductAttributeSetRemove',
            array('attributeSetId' => $createdAttrSetId)
        );
    }

    /**
     * Test attribute CRUD in attribute set
     *
     * @magentoDataFixture Magento/Catalog/Model/Product/Api/_files/AttributeSet.php
     */
    public function testAttributeSetAttrCRUD()
    {
        /** @var $objectManager Magento_TestFramework_ObjectManager */
        $objectManager = Magento_TestFramework_Helper_Bootstrap::getObjectManager();

        $testAttributeSetId = $objectManager->get('Magento_Core_Model_Registry')->registry('testAttributeSetId');
        $attrIdsArray = $objectManager->get('Magento_Core_Model_Registry')->registry('testAttributeSetAttrIdsArray');

        // add attribute test
        $addResult = Magento_TestFramework_Helper_Api::call(
            $this,
            'catalogProductAttributeSetAttributeAdd',
            array('attributeId' => $attrIdsArray[0], 'attributeSetId' => $testAttributeSetId)
        );
        $this->assertTrue((bool)$addResult);

        // delete attribute test
        $removeResult = Magento_TestFramework_Helper_Api::call(
            $this,
            'catalogProductAttributeSetAttributeRemove',
            array('attributeId' => $attrIdsArray[0], 'attributeSetId' => $testAttributeSetId)
        );
        $this->assertTrue((bool)$removeResult);
    }

    /**
     * Test group of attribute sets CRUD
     *
     * @magentoDataFixture Magento/Catalog/Model/Product/Api/_files/AttributeSet.php
     */
    public function testAttributeSetGroupCRUD()
    {
        /** @var $objectManager Magento_TestFramework_ObjectManager */
        $objectManager = Magento_TestFramework_Helper_Bootstrap::getObjectManager();

        $testAttributeSetId = $objectManager->get('Magento_Core_Model_Registry')->registry('testAttributeSetId');
        $attributeSetFixture = simplexml_load_file(dirname(__FILE__) . '/_files/_data/xml/AttributeSet.xml');
        $data = Magento_TestFramework_Helper_Api::simpleXmlToArray($attributeSetFixture->groupAdd);

        // add group test
        $attrSetGroupId = Magento_TestFramework_Helper_Api::call(
            $this,
            'catalogProductAttributeSetGroupAdd',
            array('attributeSetId' => $testAttributeSetId, 'groupName' => $data['groupName'])
        );
        $this->assertGreaterThan(0, $attrSetGroupId);

        // add already exist group exception test
        try {
            $attrSetGroupId = Magento_TestFramework_Helper_Api::call(
                $this,
                'catalogProductAttributeSetGroupAdd',
                array('attributeSetId' => $testAttributeSetId, 'groupName' => $data['existsGroupName'])
            );
            Magento_TestFramework_Helper_Api::restoreErrorHandler();
            $this->fail("Didn't receive exception!");
        } catch (Exception $exception) {
            Magento_TestFramework_Helper_Api::restoreErrorHandler();
        }

        // rename group test
        $groupName = $data['groupName'] . ' ' . mt_rand(1000, 9999);
        $renameResult = Magento_TestFramework_Helper_Api::call(
            $this,
            'catalogProductAttributeSetGroupRename',
            array('groupId' => $attrSetGroupId, 'groupName' => $groupName)
        );
        $this->assertTrue((bool)$renameResult);

        // remove group test
        $removeResult = Magento_TestFramework_Helper_Api::call(
            $this,
            'catalogProductAttributeSetGroupRemove',
            array('attributeGroupId' => $attrSetGroupId)
        );
        $this->assertTrue((bool)$removeResult);

        $this->_removeAttrSet($testAttributeSetId);
        $this->_removeAttributes(
            $objectManager->get('Magento_Core_Model_Registry')->registry('testAttributeSetAttrIdsArray')
        );

        // remove undefined group exception test
        Magento_TestFramework_Helper_Api::callWithException(
            $this,
            'catalogProductAttributeSetGroupRemove',
            array('attributeGroupId' => $attrSetGroupId)
        );
    }
}
