<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Magento_Catalog_Model_Product_Attribute_Tierprice_Api
 *
 * @magentoDataFixture Magento/Catalog/_files/product_simple.php
 */
class Magento_Catalog_Model_Product_Attribute_Tierprice_ApiTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Catalog_Model_Product_Attribute_Tierprice_Api
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model = Mage::getModel('Magento_Catalog_Model_Product_Attribute_Tierprice_Api');
    }

    public function testInfo()
    {
        $info = $this->_model->info(1);
        $this->assertInternalType('array', $info);
        $this->assertEquals(2, count($info));
        $element = current($info);
        $this->assertArrayHasKey('customer_group_id', $element);
        $this->assertArrayHasKey('website', $element);
        $this->assertArrayHasKey('qty', $element);
        $this->assertArrayHasKey('price', $element);
    }

    public function testUpdate()
    {
        Mage::app()->setCurrentStore(Mage::app()->getStore(Magento_Core_Model_App::ADMIN_STORE_ID));
        $this->_model->update(1, array(array('qty' => 3, 'price' => 8)));
        $info = $this->_model->info(1);
        $this->assertEquals(1, count($info));
    }

    /**
     * @expectedException Magento_Api_Exception
     */
    public function testPrepareTierPricesInvalidData()
    {
        $product = Mage::getModel('Magento_Catalog_Model_Product');
        $this->_model->prepareTierPrices($product, array(1));
    }

    public function testPrepareTierPricesInvalidWebsite()
    {
        $product = Mage::getModel('Magento_Catalog_Model_Product');
        $data = $this->_model->prepareTierPrices($product, array(array('qty' => 3, 'price' => 8, 'website' => 100)));
        $this->assertEquals(
            array(array('website_id' => 0, 'cust_group' => 32000, 'price_qty' => 3, 'price' => 8)),
            $data
        );
    }

    public function testPrepareTierPrices()
    {
        $product = Mage::getModel('Magento_Catalog_Model_Product');

        $this->assertNull($this->_model->prepareTierPrices($product));

        $data = $this->_model->prepareTierPrices($product,
            array(array('qty' => 3, 'price' => 8))
        );
        $this->assertEquals(
            array(array('website_id' => 0, 'cust_group' => 32000, 'price_qty' => 3, 'price' => 8)),
            $data
        );
    }
}
