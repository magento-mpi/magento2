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
class Compatibility_Soap_Catalog_Product_SimpleTest extends Compatibility_Soap_SoapAbstract
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
        $apiMethod = 'catalog_product.create';
        $productIds = $this->_createProducts();
        self::$_currProductId = $productIds['currProductId'];
        self::$_prevProductId = $productIds['prevProductId'];
        $this->_checkVersionType(self::$_prevProductId, self::$_currProductId, $apiMethod);
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
        $this->_checkVersionSignature($prevProductInfo, $currProductInfo, $apiMethod);
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
        $this->_checkVersionType($prevResponse, $currResponse, $apiMethod);
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
        $this->_checkVersionType($prevResponse, $currResponse, $apiMethod);
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
        $entityType = Mage::getModel('Mage_Eav_Model_Entity_Type')->loadByCode('catalog_product');
        $apiMethod = 'catalog_product.listOfAdditionalAttributes';
        $requestParams = array('productType' => 'simple', 'attributeSetId' => $entityType->getDefaultAttributeSetId());
        $prevResponse = $this->prevCall($apiMethod, $requestParams);
        $currResponse = $this->currCall($apiMethod, $requestParams);
        $this->_checkVersionType($prevResponse, $currResponse, $apiMethod);
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
        $this->_checkVersionType($prevResponse, $currResponse, $apiMethod);
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
        $prevResponse = $this->prevCall($apiMethod, array('filters' => ''));
        $currResponse = $this->currCall($apiMethod, array('filters' => ''));
        $this->_checkVersionSignature($prevResponse[0], $currResponse[0], $apiMethod);
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
            'productId' => self::$_prevProductId,
            'specialPrice' => '77.5',
            'fromDate' => '2012-03-29 12:30:51',
            'toDate' => '2012-04-29 12:30:51'
        ));
        $currResponse = $this->currCall($apiMethod, array(
            'productId' => self::$_currProductId,
            'specialPrice' => '77.5',
            'fromDate' => '2012-03-29 12:30:51',
            'toDate' => '2012-04-29 12:30:51'
        ));
        $this->_checkVersionType($prevResponse, $currResponse, $apiMethod);
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
        $prevResponse = $this->prevCall($apiMethod, array('productId' => self::$_prevProductId));
        $currResponse = $this->currCall($apiMethod, array('productId' => self::$_currProductId));
        $this->_checkVersionType($prevResponse, $currResponse, $apiMethod);
    }
}
