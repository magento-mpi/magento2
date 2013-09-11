<?php
/**
 * Product API tests.
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */
class Magento_Catalog_Model_Product_ApiTest extends PHPUnit_Framework_TestCase
{
    /**
     * @magentoDataFixture Magento/Catalog/_files/product_special_price.php
     */
    public function testGetSpecialPrice()
    {
        /** Retrieve the product data. */
        $productId = 1;
        $actualProductData = Magento_TestFramework_Helper_Api::call(
            $this,
            'catalogProductGetSpecialPrice',
            array('productId' => $productId)
        );
        /** Assert returned product data. */
        $this->assertNotEmpty($actualProductData, 'Missing special price response data.');

        /** @var \Magento\Catalog\Model\Product $expectedProduct */
        $expectedProduct = Mage::getModel('Magento\Catalog\Model\Product');
        $expectedProduct->load($productId);
        $fieldsToCompare = array(
            'special_price',
            'special_from_date'
        );
        /** Assert response product equals to actual product data. */
        Magento_TestFramework_Helper_Api::checkEntityFields(
            $this,
            $expectedProduct->getData(),
            $actualProductData,
            $fieldsToCompare
        );
    }

    /**
     * @magentoDataFixture Magento/Catalog/_files/multiple_products.php
     */
    public function testItems()
    {
        /** Retrieve the list of products. */
        $actualProductsData = Magento_TestFramework_Helper_Api::call(
            $this,
            'catalogProductList'
        );
        /** Assert that products quantity equals to 3. */
        $this->assertCount(3, $actualProductsData, 'Products quantity is invalid.');

        /** Loading expected product from fixture. */
        $expectedProduct = Mage::getModel('Magento\Catalog\Model\Product');
        $expectedProduct->load(10);
        $fieldsToCompare = array(
            'entity_id' => 'product_id',
            'sku',
            'attribute_set_id' => 'set',
            'type_id' => 'type',
            'category_ids',
        );
        /** Assert first product from response equals to actual product data. */
        Magento_TestFramework_Helper_Api::checkEntityFields(
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
        $attributesList = Magento_TestFramework_Helper_Api::call(
            $this,
            'catalogProductListOfAdditionalAttributes',
            array('simple', 4)
        );
        $this->assertGreaterThan(20, count($attributesList), "Attributes quantity seems to be incorrect.");
        $oneAttribute = reset($attributesList);
        $this->assertArrayHasKey('attribute_id', $oneAttribute);
        $this->assertArrayHasKey('code', $oneAttribute);
        $this->assertArrayHasKey('type', $oneAttribute);
        $this->assertArrayHasKey('required', $oneAttribute);
        $this->assertArrayHasKey('scope', $oneAttribute);
        $oldIdExpectedData = array(
            'attribute_id' => '89',
            'code' => 'old_id',
            'type' => 'text',
            'required' => '0',
            'scope' => 'global'
        );
        $this->assertContains($oldIdExpectedData, $attributesList);
    }

    /**
     * Test update multiple products at once.
     *
     * @magentoDataFixture Magento/Catalog/_files/multiple_products.php
     */
    public function testMultiUpdate()
    {
        $this->markTestIncomplete('Unable to test due to MAGETWO-7362');
        $productIds = array(10, 11, 12);

        $productData = array(
            (object)array('description' => 'Item 10'),
            (object)array('description' => 'Item 11'),
            (object)array('description' => 'Item 12'),
        );

        $result = Magento_TestFramework_Helper_Api::call(
            $this,
            'catalogProductMultiUpdate',
            array($productIds, $productData)
        );

        $this->assertTrue($result, 'Failed updating products.');
        /** @var $product \Magento\Catalog\Model\Product */
        $product = Mage::getModel('Magento\Catalog\Model\Product');

        foreach ($productIds as $index => $productId) {
            $description = $product->load($productId)->getDescription();
            $this->assertEquals($productData[$index]->description, $description,
                'Actual description does not match the expected one.');
        }
    }

    /**
     * Test for invalid input: quantity of product IDs and product data don't match.
     *
     * @magentoDataFixture Magento/Catalog/_files/multiple_products.php
     */
    public function testMultiUpdateNotMatch()
    {
        $this->markTestIncomplete('Unable to test due to MAGETWO-7362');
        $productIds = array(1, 2);

        $productData = array(
            (object)array('description' => 'something'),
        );

        $exception = Magento_TestFramework_Helper_Api::callWithException(
            $this,
            'catalogProductMultiUpdate',
            array($productIds, $productData)
        );
        $this->assertEquals(107, (int)$exception->faultcode);
    }

    /**
     * Test for invalid input: second product should not be updated.
     *
     * @magentoDataFixture Magento/Catalog/_files/multiple_products.php
     */
    public function testMultiUpdatePartiallyUpdated()
    {
        $this->markTestIncomplete('Unable to test due to MAGETWO-7362');
        $productIds = array(10, 11);

        $productData = array(
            (object)array('description' => 'Successfully updated'),
            (object)array(
                'description' => 'Failed to update',
                'sku' => str_repeat('a', \Magento\Catalog\Model\Product\Attribute\Backend\Sku::SKU_MAX_LENGTH + 1)
            )
        );

        $exception = Magento_TestFramework_Helper_Api::callWithException(
            $this,
            'catalogProductMultiUpdate',
            array($productIds, $productData)
        );
        $this->assertEquals(108, (int)$exception->faultcode);
        /** @var $product \Magento\Catalog\Model\Product */
        $product = Mage::getModel('Magento\Catalog\Model\Product')->load(10);
        $this->assertEquals($productData[0]->description, $product->getDescription());
        $product->load(11);
        $this->assertNotEquals($productData[1]->description, $product->getDescription());
    }

    /**
     * Test catalogProductInfo with a non-numeric id, identifierType not set.
     *
     * @magentoDataFixture Magento/Catalog/_files/categories.php
     */
    public function testInfoAlphaIdTypeNotSet()
    {
        /** @var \Magento\Catalog\Model\Product $alphaProduct */
        $alphaProduct = Mage::getModel('Magento\Catalog\Model\Product');
        $alphaProduct->load(1);

        $soapResult = Magento_TestFramework_Helper_Api::call(
            $this,
            'catalogProductInfo',
            array(
                'product' => 'simple'
            )
        );

        $this->assertNotEmpty($soapResult, 'Error during catalog product info via API call');
        $this->_verifyProductInfo($alphaProduct->getData(), $soapResult);
    }

    /**
     * Test catalogProductInfo with a numeric id, identifierType null.
     * Should return an error because no product with that productId exists.
     *
     * @magentoDataFixture Magento/Catalog/_files/categories.php
     */
    public function testInfoNumericIdTypeNotSetError()
    {

        /** @var \Magento\Catalog\Model\Product $numericalProduct */
        $numericalProduct = Mage::getModel('Magento\Catalog\Model\Product');
        $numericalProduct->load(2);

        $exception = Magento_TestFramework_Helper_Api::callWithException(
            $this,
            'catalogProductInfo',
            array('product' => '12345')
        );
        $result = array(
            'faultcode' => $exception->faultcode,
            'faultstring' => $exception->faultstring
        );

        $this->assertInternalType('array', $result);
        $this->assertEquals(101, $result['faultcode'], 'Fault code is not right.');
        $this->assertEquals('Product does not exist.', $result['faultstring'], 'Fault code is not right.');
    }

    /**
     * Test catalogProductInfo with a numeric id, identifierType null.
     *
     * @magentoDataFixture Magento/Catalog/_files/categories.php
     */
    public function testInfoNumericIdTypeNotSet()
    {
        /** @var \Magento\Catalog\Model\Product $numericalProduct */
        $numericalProduct = Mage::getModel('Magento\Catalog\Model\Product');
        $numericalProduct->load(2);

        $soapResult = Magento_TestFramework_Helper_Api::call(
            $this,
            'catalogProductInfo',
            array(
                'product' => '2'
            )
        );

        $this->assertNotEmpty($soapResult, 'Error during catalog product info via API call');
        $this->_verifyProductInfo($numericalProduct->getData(), $soapResult);
    }

    /**
     * Test catalogProductInfo with a non-numeric id, identifierType sku.
     *
     * @magentoDataFixture Magento/Catalog/_files/categories.php
     */
    public function testInfoAlphaIdTypeSku()
    {
        /** @var \Magento\Catalog\Model\Product $alphaProduct */
        $alphaProduct = Mage::getModel('Magento\Catalog\Model\Product');
        $alphaProduct->load(1);

        $soapResult = Magento_TestFramework_Helper_Api::call(
            $this,
            'catalogProductInfo',
            array(
                'product' => 'simple',
                'storeView' => null,
                'attributes' => null,
                'productIdentifierType' => 'sku'
            )
        );

        $this->assertNotEmpty($soapResult, 'Error during customer address info via API call');
        $this->_verifyProductInfo($alphaProduct->getData(), $soapResult);
    }

    /**
     * Test catalogProductInfo with a numeric id, identifierType sku.
     *
     * @magentoDataFixture Magento/Catalog/_files/categories.php
     */
    public function testInfoNumericIdTypeSku()
    {
        /** @var \Magento\Catalog\Model\Product $numericalProduct */
        $numericalProduct = Mage::getModel('Magento\Catalog\Model\Product');
        $numericalProduct->load(2);

        $soapResult = Magento_TestFramework_Helper_Api::call(
            $this,
            'catalogProductInfo',
            array(
                'product' => '12345',
                'storeView' => null,
                'attributes' => null,
                'productIdentifierType' => 'sku'
            )
        );

        $this->assertNotEmpty($soapResult, 'Error during customer address info via API call');
        $this->_verifyProductInfo($numericalProduct->getData(), $soapResult);
    }

    /**
     * Verify fields in an address array
     *
     * Compares two arrays containing address data.  Throws assertion error if
     * data does not match.
     *
     * @param array $expectedData Expected values of address array
     * @param array $actualData Values that are to be tested
     */
    protected function _verifyProductInfo($expectedData, $actualData)
    {
        $fieldsToCompare = array(
            'entity_id' => 'product_id',
            'sku',
            'attribute_set_id' => 'set',
            'type_id' => 'type',
            'category_ids',
            'weight',
            'name',
            'price',
            'quantity_and_stock_status'
        );
        Magento_TestFramework_Helper_Api::checkEntityFields(
            $this,
            $expectedData,
            $actualData,
            $fieldsToCompare
        );
    }

    /**
     * Test product creation.
     *
     * @magentoDbIsolation enabled
     */
    public function testCreate()
    {
        $data = require dirname(__FILE__) . DS . 'Api' . DS . '_files' . DS . 'ProductData.php';

        $productId = Magento_TestFramework_Helper_Api::call(
            $this,
            'catalogProductCreate',
            $data['create']
        );

        $this->assertGreaterThan(0, $productId);
    }

    /**
     * Test updating product information.
     *
     * @magentoDataFixture Magento/Catalog/_files/product_simple.php
     */
    public function testUpdate()
    {
        /*
         * Some product attributes (e.g. tier_price) rely on _origData to determine whether attributes are new (thus,
         * should be inserted into the DB) or updated. Real-world requests works fine because same code contained in
         * \Magento\Api\Controller\Action::preDispatch().
         */
        Mage::app()->setCurrentStore('admin');
        $newData = (object)array(
            'name'              => 'New Name',
            'description'       => 'New Description',
            'short_description' => 'New short description',
            'weight'            => 2,
            'price'             => 13.13,
        );

        /** @var $product \Magento\Catalog\Model\Product */
        $product = Mage::getModel('Magento\Catalog\Model\Product');
        $product->load(1);

        $updatedProduct = Magento_TestFramework_Helper_Api::call(
            $this,
            'catalogProductUpdate',
            array($product->getId(), $newData)
        );
        $this->assertTrue($updatedProduct, 'Product was not updated');
        $product->load(1);

        $updatedProductData = (object)array(
            'name' => $product->getName(),
            'description' => $product->getDescription(),
            'short_description' =>$product->getShortDescription(),
            'weight' => $product->getWeight(),
            'price' =>$product->getPrice(),
        );
        $this->assertEquals($newData, $updatedProductData, 'Product was not updated');
    }
}
