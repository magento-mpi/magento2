<?php
/**
 * Product API tests.
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */
class Mage_Catalog_Model_Product_ApiTest extends PHPUnit_Framework_TestCase
{
    /**
     * @magentoDataFixture Mage/Catalog/_files/product_special_price.php
     */
    public function testGetSpecialPrice()
    {
        /** Retrieve the product data. */
        $productId = 1;
        $actualProductData = Magento_Test_Helper_Api::call(
            $this,
            'catalogProductGetSpecialPrice',
            array('productId' => $productId)
        );
        /** Assert returned product data. */
        $this->assertNotEmpty($actualProductData, 'Missing special price response data.');

        /** @var Mage_Catalog_Model_Product $expectedProduct */
        $expectedProduct = Mage::getModel('Mage_Catalog_Model_Product');
        $expectedProduct->load($productId);
        $fieldsToCompare = array(
            'special_price',
            'special_price_to',
            'special_price_from'
        );
        /** Assert response product equals to actual product data. */
        Magento_Test_Helper_Api::checkEntityFields(
            $this,
            $expectedProduct->getData(),
            $actualProductData,
            $fieldsToCompare
        );
    }

    /**
     * @magentoDataFixture Mage/Catalog/_files/multiple_products.php
     */
    public function testItems()
    {
        /** Retrieve the list of products. */
        $actualProductsData = Magento_Test_Helper_Api::call(
            $this,
            'catalogProductList'
        );
        /** Assert that products quantity equals to 3. */
        $this->assertCount(3, $actualProductsData, 'Products quantity is invalid.');

        /** Loading expected product from fixture. */
        $expectedProduct = Mage::getModel('Mage_Catalog_Model_Product');
        $expectedProduct->load(10);
        $fieldsToCompare = array(
            'entity_id' => 'product_id',
            'sku',
            'attribute_set_id' => 'set',
            'type_id' => 'type',
            'category_ids',
        );
        /** Assert first product from response equals to actual product data. */
        Magento_Test_Helper_Api::checkEntityFields(
            $this,
            $expectedProduct->getData(),
            reset($actualProductsData),
            $fieldsToCompare
        );
    }

    /**
     * Test retrieving the list of attributes which are not in default create/update list via API.
     */
    public function testGetAdditionalAttributes()
    {
        $attributesList = Magento_Test_Helper_Api::call(
            $this,
            'catalogProductListOfAdditionalAttributes',
            array('simple', 4)
        );
        $this->assertGreaterThan(20, count($attributesList), "Attributes quantity seems to be incorrect.");
        $oldIdAttributeData = reset($attributesList);
        $oldIdExpectedData = array(
            'attribute_id' => '89',
            'code' => 'old_id',
            'type' => 'text',
            'required' => '0',
            'scope' => 'global'
        );
        $this->assertEquals($oldIdExpectedData, $oldIdAttributeData, "Attribute data from the list is incorrect.");
    }

    /**
     * Test update multiple products at once.
     *
     * @magentoDataFixture Mage/Catalog/_files/multiple_products.php
     */
    public function testMultiUpdate()
    {
        $productIds = array(10, 11, 12);

        $productData = array(
            (object)array('description' => 'Item 10'),
            (object)array('description' => 'Item 11'),
            (object)array('description' => 'Item 12'),
        );

        $result = Magento_Test_Helper_Api::call(
            $this,
            'catalogProductMultiUpdate',
            array($productIds, $productData)
        );

        $this->assertTrue($result, 'Failed updating stock items.');
        /** @var $product Mage_Catalog_Model_Product */
        $product = Mage::getModel('Mage_Catalog_Model_Product');

        foreach ($productIds as $index => $productId) {
            $description = $product->load($productId)->getDescription();
            $this->assertEquals($productData[$index]->description, $description, 'Actual description does not match the expected one.');
        }
    }

    /**
     * Test for invalid input: quantity of product IDs and product data don't match.
     *
     * @magentoDataFixture Mage/Catalog/_files/multiple_products.php
     */
    public function testMultiUpdateNotMatch()
    {
        $productIds = array(1, 2);

        $productData = array(
            (object)array('description' => 'something'),
        );

        try {
            Magento_Test_Helper_Api::call(
                $this,
                'catalogProductMultiUpdate',
                array($productIds, $productData)
            );
            $this->fail('Expected exception SoapFault has not been thrown');
        } catch (SoapFault $e) {
            $this->assertEquals(107, (int)$e->faultcode);
        }
    }

    /**
     * Test for invalid input: second product should not be updated.
     *
     * @magentoDataFixture Mage/Catalog/_files/multiple_products.php
     */
    public function testMultiUpdatePartiallyUpdated()
    {
        $this->markTestSkipped('Unable to test due to https://jira.corp.x.com/browse/MAGETWO-7362');
        $productIds = array(10, 11);

        $productData = array(
            (object)array('description' => 'Successfully updated'),
            (object)array(
                'description' => 'Failed to update',
                'sku' => str_repeat('a', Mage_Catalog_Model_Product_Attribute_Backend_Sku::SKU_MAX_LENGTH + 1)
            )
        );

        try {
            Magento_Test_Helper_Api::call(
                $this,
                'catalogProductMultiUpdate',
                array($productIds, $productData)
            );
            $this->fail('Expected exception SoapFault has not been thrown');
        } catch (SoapFault $e) {
            $this->assertEquals(108, (int)$e->faultcode);
            /** @var $product Mage_Catalog_Model_Product */
            $product = Mage::getModel('Mage_Catalog_Model_Product')->load(10);
            $this->assertEquals($productData[0]->description, $product->getDescription());
            $product->load(11);
            $this->assertNotEquals($productData[1]->description, $product->getDescription());
        }
    }
    /**
     * Test information retrieve about product.
     */
    public function testInfo()
    {
        $this->markTestSkipped('Not implemented');
    }

    /**
     * Test product creation.
     */
    public function testCreate()
    {
        $this->markTestSkipped('Not implemented');
    }

    /**
     * Test setting special price.
     */
    public function testSetSpecialPrice()
    {
        $this->markTestSkipped('Not implemented');
    }

    /**
     * Test updating product information.
     */
    public function testUpdate()
    {
        $this->markTestSkipped('Not implemented');
    }
}
