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
        $apiMethod = 'catalog_product.create';
        self::$_prevProductId = $this->prevCall($apiMethod, $request);
        self::$_currProductId = $this->currCall($apiMethod, $request);
        $this->_checkVersionCompatibility(self::$_prevProductId, self::$_currProductId, $apiMethod);
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
        $apiMethod = 'catalog_product.info';
        $prevProductInfo = $this->prevCall($apiMethod, array(
            'productId' => self::$_prevProductId,
        ));
        $currProductInfo = $this->currCall($apiMethod, array(
            'productId' => self::$_currProductId
        ));
        $prevResponseSignature = array_keys($prevProductInfo);
        $currResponseSignature = array_keys($currProductInfo);
        $this->assertEquals($prevResponseSignature, $currResponseSignature,
            "The signature of $apiMethod has changed in the new API version.");
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
        $apiMethod = 'catalog_product.update';
        $prevResponse = $this->prevCall($apiMethod, array(
            'productId' => self::$_prevProductId,
            'productData' => $productData
        ));
        $currResponse = $this->currCall($apiMethod, array(
            'productId' => self::$_currProductId,
            'productData' => $productData
        ));
        $this->_checkVersionCompatibility($prevResponse, $currResponse, $apiMethod);
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
        $apiMethod = 'catalog_product.getSpecialPrice';
        $prevResponse = $this->prevCall($apiMethod, array('productId' => self::$_prevProductId));
        $currResponse = $this->currCall($apiMethod, array('productId' => self::$_currProductId));
        $this->_checkVersionCompatibility($prevResponse, $currResponse, $apiMethod);
    }

    /**
     * Test list of additional attributes method compatibility.
     * Scenario:
     * 1. Get the list of additional attributes at previous API.
     * 2. Get the list of additional attributes at current API.
     * Expected result:
     * Signature of current API is the same as in previous.
     */
    public function testListOfAdditionalAttributes()
    {
        $apiMethod = 'catalog_product.listOfAdditionalAttributes';
        $requestParams = array('productType' => 'simple', 'attributeSetId' => 4);
        $prevResponse = $this->prevCall($apiMethod, $requestParams);
        $currResponse = $this->currCall($apiMethod, $requestParams);
        $this->_checkVersionCompatibility($prevResponse, $currResponse, $apiMethod);
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
        $apiMethod = 'catalog_product.currentStore';
        $prevResponse = $this->prevCall($apiMethod);
        $currResponse = $this->currCall($apiMethod);
        $this->_checkVersionCompatibility($prevResponse, $currResponse, $apiMethod);
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
        $apiMethod = 'catalog_product.list';
        $prevResponse = $this->prevCall($apiMethod);
        $currResponse = $this->currCall($apiMethod);
        $this->_checkVersionCompatibility($prevResponse, $currResponse, $apiMethod);
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
        $apiMethod = 'catalog_product.setSpecialPrice';
        $prevResponse = $this->prevCall($apiMethod, array(
            'product' => self::$_prevProductId,
            'specialPrice' => '77.5',
            'fromDate' => '2012-03-29 12:30:51',
            'toDate' => '2012-04-29 12:30:51'
        ));
        $currResponse = $this->currCall($apiMethod, array(
            'product' => self::$_currProductId,
            'specialPrice' => '77.5',
            'fromDate' => '2012-03-29 12:30:51',
            'toDate' => '2012-04-29 12:30:51'
        ));
        $this->_checkVersionCompatibility($prevResponse, $currResponse, $apiMethod);
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
        $apiMethod = 'catalog_product.delete';
        $prevResponse = $this->prevCall($apiMethod, array(
            'productId' => self::$_prevProductId,
        ));
        $currResponse = $this->currCall($apiMethod, array(
            'productId' => self::$_currProductId,
        ));
        $this->_checkVersionCompatibility($prevResponse, $currResponse, $apiMethod);
    }

    /**
     * Compare types of API responses (current and previous versions)
     *
     * @param mixed $prevResponse
     * @param mixed $currResponse
     * @param string $apiMethod
     */
    protected function _checkVersionCompatibility($prevResponse, $currResponse, $apiMethod)
    {
        $this->assertInternalType(gettype($prevResponse), $currResponse,
            "The signature of $apiMethod has changed in the new API version.");
    }
}
