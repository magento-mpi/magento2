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
 * Test class for \Magento\Catalog\Block\Product\View\Options.
 *
 * @magentoDataFixture Magento/Catalog/_files/product_simple.php
 */
class Magento_Catalog_Block_Product_View_OptionsTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Catalog\Block\Product\View\Options
     */
    protected $_block;

    /**
     * @var \Magento\Catalog\Model\Product
     */
    protected $_product;

    protected function setUp()
    {
        $this->_product = Mage::getModel('\Magento\Catalog\Model\Product');
        $this->_product->load(1);
        Mage::unregister('current_product');
        Mage::register('current_product', $this->_product);
        $this->_block = Mage::app()->getLayout()->createBlock('\Magento\Catalog\Block\Product\View\Options');
    }

    public function testSetGetProduct()
    {
        $this->assertSame($this->_product, $this->_block->getProduct());

        $product = Mage::getModel('\Magento\Catalog\Model\Product');
        $this->_block->setProduct($product);
        $this->assertSame($product, $this->_block->getProduct());
    }

    public function testAddAndGetOptionRenderer()
    {
        $this->_block->addOptionRenderer('test', 'test/test', 'test.phtml');
        $this->assertEquals(
            array(
                'block'     => 'test/test',
                'template'  => 'test.phtml',
                'renderer'  => null,
            ),
            $this->_block->getOptionRender('test')
        );

        $this->assertEquals(
            array(
                'block'     => '\Magento\Catalog\Block\Product\View\Options\Type\DefaultType',
                'template'  => 'product/view/options/type/default.phtml',
                'renderer'  => null,
            ),
            $this->_block->getOptionRender('not_exists')
        );

    }

    public function testGetGroupOfOption()
    {
        $this->assertEquals('default', $this->_block->getGroupOfOption('test'));
    }

    public function testGetOptions()
    {
        $options = $this->_block->getOptions();
        $this->assertNotEmpty($options);
        foreach ($options as $option) {
            $this->assertInstanceOf('\Magento\Catalog\Model\Product\Option', $option);
        }
    }

    public function testHasOptions()
    {
        $this->assertTrue($this->_block->hasOptions());
    }

    public function testGetJsonConfig()
    {
        $config = json_decode($this->_block->getJsonConfig());
        $this->assertNotNull($config);
        $this->assertNotEmpty($config);
    }

    public function testGetOptionHtml()
    {
        $this->_block->addOptionRenderer(
            'select',
            '\Magento\Catalog\Block\Product\View\Options\Type\Select',
            'product/view/options/type/select.phtml'
        );
        $this->_block->addOptionRenderer(
            'date',
            '\Magento\Catalog\Block\Product\View\Options\Type\Date',
            'product/view/options/type/date.phtml'
        );
        $this->_block->setLayout(Mage::app()->getLayout());
        $html = false;
        foreach ($this->_block->getOptions() as $option) {
            $html = $this->_block->getOptionHtml($option);
            $this->assertContains('Test', $html); /* contain Test in option title */
        }
        if (!$html) {
            $this->fail('Product with options is required for test');
        }
    }
}
