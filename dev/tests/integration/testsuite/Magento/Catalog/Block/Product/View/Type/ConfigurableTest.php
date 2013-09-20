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
 * Test class for \Magento\Catalog\Block\Product\View\Type\Configurable.
 *
 * @magentoDataFixture Magento/Catalog/_files/product_configurable.php
 */
namespace Magento\Catalog\Block\Product\View\Type;

class ConfigurableTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Catalog\Block\Product\View\Type\Configurable
     */
    protected $_block;

    /**
     * @var \Magento\Catalog\Model\Product
     */
    protected $_product;

    protected function setUp()
    {
        $this->_product = \Mage::getModel('Magento\Catalog\Model\Product');
        $this->_product->load(1);
        $this->_block = \Mage::app()->getLayout()->createBlock('Magento\Catalog\Block\Product\View\Type\Configurable');
        $this->_block->setProduct($this->_product);
    }

    public function testGetAllowAttributes()
    {
        $attributes = $this->_block->getAllowAttributes();
        $this->assertInstanceOf(
            'Magento\Catalog\Model\Resource\Product\Type\Configurable\Attribute\Collection',
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
            $this->assertInstanceOf('Magento\Catalog\Model\Product', $product);
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
