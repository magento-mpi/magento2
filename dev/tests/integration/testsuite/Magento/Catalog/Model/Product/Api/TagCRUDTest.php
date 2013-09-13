<?php
/**
 * Product tag API model test.
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 * @magentoDataFixture Magento/Catalog/Model/Product/Api/_files/TagCRUD.php
 * @magentoDbIsolation enabled
 */
class Magento_Catalog_Model_Product_Api_TagCRUDTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->markTestSkipped('Api tests were skipped');
    }

    /**
     * Test tag CRUD
     */
    public function testTagCRUD()
    {
        $tagFixture = simplexml_load_file(dirname(__FILE__) . '/_files/_data/xml/TagCRUD.xml');
        $data = Magento_TestFramework_Helper_Api::simpleXmlToArray($tagFixture->tagData);
        $expected = Magento_TestFramework_Helper_Api::simpleXmlToArray($tagFixture->expected);
        
        /** @var $objectManager Magento_TestFramework_ObjectManager */
        $objectManager = Magento_TestFramework_Helper_Bootstrap::getObjectManager();

        $data['product_id'] = $objectManager->get('Magento_Core_Model_Registry')->registry('productData')->getId();
        $data['customer_id'] = $objectManager->get('Magento_Core_Model_Registry')->registry('customerData')->getId();

        // create test
        $createdTags = Magento_TestFramework_Helper_Api::call($this, 'catalogProductTagAdd', array('data' => $data));

        $this->assertCount(3, $createdTags);

        // Invalid product ID exception test
        $data['product_id'] = mt_rand(10000, 99999);
        Magento_TestFramework_Helper_Api::callWithException($this, 'catalogProductTagAdd',
            array('data' => $data), 'Requested product does not exist.'
        );

        // Invalid customer ID exception test
        $data['product_id'] = $objectManager->get('Magento_Core_Model_Registry')->registry('productData')->getId();
        $data['customer_id'] = mt_rand(10000, 99999);
        Magento_TestFramework_Helper_Api::callWithException($this, 'catalogProductTagAdd',
            array('data' => $data), 'Requested customer does not exist.'
        );

        // Invalid store ID exception test
        $data['product_id'] = $objectManager->get('Magento_Core_Model_Registry')->registry('productData')->getId();
        $data['customer_id'] = $objectManager->get('Magento_Core_Model_Registry')->registry('customerData')->getId();
        $data['store'] = mt_rand(10000, 99999);
        Magento_TestFramework_Helper_Api::callWithException($this, 'catalogProductTagAdd',
            array('data' => $data), 'Requested store does not exist.'
        );

        // items list test
        $tagsList = Magento_TestFramework_Helper_Api::call(
            $this,
            'catalogProductTagList',
            array(
                'productId' => $objectManager->get('Magento_Core_Model_Registry')->registry('productData')->getId(),
                'store' => 0
            )
        );
        $this->assertInternalType('array', $tagsList);
        $this->assertNotEmpty($tagsList, "Can't find added tag in list");
        $this->assertCount((int)$expected['created_tags_count'], $tagsList, "Can't find added tag in list");

        // delete test
        $tagToDelete = (array)array_shift($tagsList);
        $tagDelete = Magento_TestFramework_Helper_Api::call(
            $this,
            'catalogProductTagRemove',
            array('tagId' => $tagToDelete['tag_id'])
        );
        $this->assertTrue((bool)$tagDelete, "Can't delete added tag");

        // Delete exception test
        Magento_TestFramework_Helper_Api::callWithException($this, 'catalogProductTagRemove',
            array('tagId' => $tagToDelete['tag_id']), 'Requested tag does not exist.'
        );
    }
}
