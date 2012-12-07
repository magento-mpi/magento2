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

/**
 * Test updating product back-order status through API
 *
 * @category    Magento
 * @package     Magento_Test
 * @author      Magento Api Team <api-team@magento.com>
 */
class Api_Catalog_Product_BackorderStatusTest extends Magento_Test_Webservice
{
    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $productData = require dirname(__FILE__) . '/_fixture/ProductData.php';
        $product     = new Mage_Catalog_Model_Product;

        $product->setData($productData['create_full_fledged']);
        $product->save();

        $this->setFixture('product', $product);

        parent::setUp();
    }

    /**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        $this->deleteFixture('product', true);
        parent::tearDown();
    }

    /**
     * Test updating product back-order status
     *
     * @return void
     */
    public function testBackorderStatusUpdate()
    {
        /** @var $product Mage_Catalog_Model_Product */
        $product = $this->getFixture('product');

        $newProductData = new stdClass();
        $newProductData->use_config_manage_stock = 0;
        $newProductData->manage_stock = 1;
        $newProductData->is_in_stock = 0;
        $newProductData->use_config_backorders = 0;
        $newProductData->backorders = 1;

        $result = $this->call(
            'cataloginventory_stock_item.update',
            array(
                'productId' => $this->getFixture('product')->getSku(),
                'data' => (array)$newProductData
            )
        );

        if (Magento_Test_Webservice::TYPE_SOAPV2 === TESTS_WEBSERVICE_TYPE
            || Magento_Test_Webservice::TYPE_SOAPV2_WSI === TESTS_WEBSERVICE_TYPE) {
            $this->assertEquals(1, $result);
        } else {
            $this->assertTrue($result);
        }
        // have to re-load product for stock item set
        $product->load($product->getId());
        $this->assertEquals(1, $product->getStockItem()->getBackorders());
        $this->assertEquals(0, $product->getStockItem()->getUseConfigBackorders());
    }
}
