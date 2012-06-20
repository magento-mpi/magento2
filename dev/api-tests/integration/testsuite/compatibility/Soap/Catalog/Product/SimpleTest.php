<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Core
 * @subpackage  compatibility_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test Product methods compatibility between previous and current API versions.
 */
class Compatibility_Soap_Catalog_Product_SimpleTest extends Magento_Test_Webservice_Compatibility
{
    /**
     * Product created at previous API
     * @var int
     */
    protected static $_prevProductId;

    /**
     * Product created at current API
     * @var int
     */
    protected static $_currProductId;

    /**
     * Test product create method compatibility.
     * Store created products IDs for use in later tests.
     *
     * Scenario:
     * 1. Create product with prepared data fixture on previous API.
     * 2. Create product with same data fixture on current API.
     * Expected result:
     * Current API response type is the same as in previous API.
     */
    public function testCreate()
    {
        $request = array(
            'type' => 'simple',
            'set'  => 4,
            'sku'  => 'compatibility-' . uniqid(),
            'productData' => array(
                'name' => 'Compatibility Test ' . uniqid(),
                'description' => 'Test description',
                'short_description' => 'Test short description',
                'status' => 1,
                'visibility' => 4,
                'price' => 9.99,
                'tax_class_id' => 2,
                'weight' => 1,
            )
        );

        self::$_prevProductId = $this->prevCall('catalog_product.create', $request);
        $expectedType = gettype(self::$_prevProductId);
        self::$_currProductId = $this->currCall('catalog_product.create', $request);
        $this->assertInternalType($expectedType, self::$_currProductId,
            'Current API response type expected to be the same as in previous API.');
    }

    /**
     * Test product info method compatibility.
     * Scenario:
     * 1. Get info of the product, created in 'testCreate' at previous API.
     * 2. Get info of the product, created in 'testCreate' at current API.
     * Expected result:
     * Signature of current API is the same as in previous.
     *
     * @depends testCreate
     */
    public function testInfo()
    {
        $prevProductInfo = $this->prevCall('catalog_product.info', array(
            'productId' => self::$_prevProductId,
        ));
        $currProductInfo = $this->currCall('catalog_product.info', array(
            'productId' => self::$_currProductId
        ));

        $prevResponseSignature = array_keys($prevProductInfo);
        $currResponseSignature = array_keys($currProductInfo);

        $this->assertEquals($prevResponseSignature, $currResponseSignature,
            'Current API response signature expected to be the same as in previous API');
    }

    /**
     * Test product update method compatibility.
     * Scenario:
     * 1. Update product, created in 'testCreate' at previous API with prepared data fixture.
     * 2. Update product, created in 'testCreate' at current API with the same data fixture.
     * Expected result:
     * No errors raised and type of current API response is the same as in previous.
     *
     * @depends testCreate
     */
    public function testUpdate()
    {
        $productData = array(
            'name' => 'test',
            'description' => 'description',
            'short_description' => 'short description',
        );

        $prevResponse = $this->prevCall('catalog_product.update', array(
            'productId' => self::$_prevProductId,
            'productData' => $productData
        ));
        $currResponse = $this->currCall('catalog_product.update', array(
            'productId' => self::$_currProductId,
            'productData' => $productData
        ));

        $this->assertInternalType(gettype($prevResponse), $currResponse,
            'Current API response type expected to be the same as in previous API.');
    }

    /**
     * Test product get special price method compatibility.
     * Scenario:
     * 1. Get special price of the product, created in 'testCreate' at previous API.
     * 2. Get special price of the product, created in 'testCreate' at current API.
     * Expected result:
     * Signature of current API is the same as in previous.
     *
     * @depends testCreate
     */

    public function testGetSpecialPrice()
    {
        $prevResponse = gettype($this->prevCall('catalog_product.getSpecialPrice', array(
            'productId' => self::$_prevProductId,
        )));
        $currResponse = gettype($this->currCall('catalog_product.getSpecialPrice', array(
            'productId' => self::$_currProductId
        )));

        $this->assertEquals($prevResponse, $currResponse,
            'Current API response signature expected to be the same as in previous API');
    }

    /**
     * Test list of additional attributes method compatibility.
     * Scenario:
     * 1. Get the list of additional attributes at previous API.
     * 2. Get the list of additional attributes at current API.
     * Expected result:
     * Signature of current API is the same as in previous.
     *
     */

    public function testListOfAdditionalAttributes()
    {
        $prevResponse = gettype($this->prevCall('catalog_product.listOfAdditionalAttributes', array(
            'productType' => 'simple',
            'attributeSetId' => 4
        )));
        $currResponse = gettype($this->currCall('catalog_product.listOfAdditionalAttributes', array(
            'productType' => 'simple',
            'attributeSetId' => 4
        )));

        $this->assertEquals($prevResponse, $currResponse,
            'Current API response signature expected to be the same as in previous API');
    }

    /**
     * Test product current store method compatibility.
     * Scenario:
     * 1. Get the current store at previous API.
     * 2. Get the current store at current API.
     * Expected result:
     * Signature of current API is the same as in previous.
     *
     */

    public function testProductCurrentStore()
    {
        $prevResponse = gettype($this->prevCall('catalog_product.currentStore'));
        $currResponse = gettype($this->currCall('catalog_product.currentStore'));

        $this->assertEquals($prevResponse, $currResponse,
            'Current API response signature expected to be the same as in previous API');
    }

    /**
     * Test product list method compatibility.
     * Scenario:
     * 1. Get the list of products at previous API.
     * 2. Get the list of products at current API.
     * Expected result:
     * Signature of current API is the same as in previous.
     *
     */

    public function testProductList()
    {
        $prevResponse = gettype($this->prevCall('catalog_product.list'));
        $currResponse = gettype($this->currCall('catalog_product.list'));

        $this->assertEquals($prevResponse, $currResponse,
            'Current API response signature expected to be the same as in previous API');
    }

    /**
     * Test product set special price method compatibility.
     * Scenario:
     * 1. Set special price for the product, created in 'testCreate' at previous API.
     * 2. Set special price for the product, created in 'testCreate' at current API.
     * Expected result:
     * Signature of current API is the same as in previous.
     *
     * @depends testCreate
     */

    public function testSetSpecialPrice()
    {
        $prevResponse = gettype($this->prevCall('catalog_product.setSpecialPrice', array(
            'product' => self::$_prevProductId,
            'specialPrice' => '77.5',
            'fromDate' => '2012-03-29 12:30:51',
            'toDate' => '2012-04-29 12:30:51'
        )));
        $currResponse = gettype($this->currCall('catalog_product.setSpecialPrice', array(
            'product' => self::$_currProductId,
            'specialPrice' => '77.5',
            'fromDate' => '2012-03-29 12:30:51',
            'toDate' => '2012-04-29 12:30:51'
        )));

        $this->assertEquals($prevResponse, $currResponse,
            'Current API response signature expected to be the same as in previous API');
    }

    /**
     * Test product delete method compatibility.
     * Scenario:
     * 1. Delete product, created in 'testCreate' at previous API.
     * 2. Delete product, created in 'testCreate' at current API.
     * Expected result:
     * No errors raised and type of current API response is the same as in previous.
     *
     * @depends testGetSpecialPrice
     */
    public function testDelete()
    {
        $prevResponse = $this->prevCall('catalog_product.delete', array(
            'productId' => self::$_prevProductId,
        ));
        $currResponse = $this->currCall('catalog_product.delete', array(
            'productId' => self::$_currProductId,
        ));

        $this->assertInternalType(gettype($prevResponse), $currResponse,
            'Current API response type expected to be the same as in previous API.');
    }

}

