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
 * Test class for Magento_Catalog_Block_Product_View_Type_Configurable.
 *
 * @magentoDataFixture Magento/Catalog/_files/product_configurable.php
 */
class Magento_Catalog_Block_Product_View_Type_ConfigurableTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Catalog_Block_Product_View_Type_Configurable
     */
    protected $_block;

    /**
     * @var Magento_Catalog_Model_Product
     */
    protected $_product;

    protected function setUp()
    {
        $this->_product = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Catalog_Model_Product');
        $this->_product->load(1);
        $this->_block = Mage::app()->getLayout()->createBlock('Magento_Catalog_Block_Product_View_Type_Configurable');
        $this->_block->setProduct($this->_product);
    }

    public function testGetAllowAttributes()
    {
        $attributes = $this->_block->getAllowAttributes();
        $this->assertInstanceOf(
            'Magento_Catalog_Model_Resource_Product_Type_Configurable_Attribute_Collection',
            $attributes
        );
        $this->assertGreaterThanOrEqual(1, $attributes->getSize());
    }

    public function testHasOptions()
    {
        $this->assertTrue($this->_block->hasOptions());
    }

    public function testGetAllowProducts()
    {
        $products = $this->_block->getAllowProducts();
        $this->assertGreaterThanOrEqual(2, count($products));
        foreach ($products as $product) {
            $this->assertInstanceOf('Magento_Catalog_Model_Product', $product);
        }
    }

    public function testGetJsonConfig()
    {
        $config = (array) json_decode($this->_block->getJsonConfig());
        $this->assertNotEmpty($config);
        $this->assertArrayHasKey('attributes', $config);
        $this->assertArrayHasKey('template', $config);
        $this->assertArrayHasKey('basePrice', $config);
        $this->assertArrayHasKey('productId', $config);
        $this->assertEquals(1, $config['productId']);
    }
}
