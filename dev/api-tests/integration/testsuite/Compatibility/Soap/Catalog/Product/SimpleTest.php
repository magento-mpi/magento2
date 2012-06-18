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
            'product' => self::$_prevProductId,
            'productData' => $productData
        ));
        $currResponse = $this->currCall('catalog_product.update', array(
            'product' => self::$_currProductId,
            'productData' => $productData
        ));

        $this->assertInternalType(gettype($prevResponse), $currResponse,
            'Current API response type expected to be the same as in previous API.');
    }

    /**
     * Test product delete method compatibility.
     * Scenario:
     * 1. Delete product, created in 'testCreate' at previous API.
     * 2. Delete product, created in 'testCreate' at current API.
     * Expected result:
     * No errors raised and type of current API response is the same as in previous.
     *
     * @depends testCreate
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
