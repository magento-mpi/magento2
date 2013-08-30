<?php
/**
 * Test updating product back-order status through API
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 * @magentoDbIsolation enabled
 */
class Magento_Catalog_Model_Product_Api_BackorderStatusTest extends PHPUnit_Framework_TestCase
{
    /** @var Magento_Catalog_Model_Product */
    protected $_product;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $productData = require dirname(__FILE__) . '/_files/ProductData.php';
        $product = Mage::getModel('Magento_Catalog_Model_Product');

        $product->setData($productData['create_full_fledged']);
        $product->save();

        $this->_product = $product;

        parent::setUp();
    }

    /**
     * Test updating product back-order status
     */
    public function testBackorderStatusUpdate()
    {
        $newProductData = array(
            'use_config_manage_stock' => 0,
            'manage_stock' => 1,
            'is_in_stock' => 0,
            'use_config_backorders' => 0,
            'backorders' => 1,
        );

        $result = Magento_Test_Helper_Api::call(
            $this,
            'catalogInventoryStockItemUpdate',
            array(
                'productId' => $this->_product->getSku(),
                'data' => $newProductData
            )
        );

        $this->assertEquals(1, $result);
        // have to re-load product for stock item set
        $this->_product->load($this->_product->getId());
        $this->assertEquals(1, $this->_product->getStockItem()->getBackorders());
        $this->assertEquals(0, $this->_product->getStockItem()->getUseConfigBackorders());
    }
}
