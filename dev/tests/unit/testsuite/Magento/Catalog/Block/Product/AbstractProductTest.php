<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Block\Product;

/**
 * Class for testing methods of AbstractProduct
 */
class AbstractProductTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Catalog\Block\Product\View\Type\Simple
     */
    protected $block;

    /**
     * @var \Magento\Catalog\Block\Product\Context
     */
    protected $productContextMock;

    /**
     * @var \Magento\Framework\View\LayoutInterface
     */
    protected $layoutMock;

    /**
     * Set up mocks and tested class
     * Child class is used as the tested class is declared abstract
     */
    public function setUp()
    {
        $this->productContextMock = $this->getMock(
            'Magento\Catalog\Block\Product\Context',
            ['getLayout'],
            [],
            '',
            false
        );
        $arrayUtilsMock = $this->getMock('Magento\Stdlib\ArrayUtils', [], [], '', false);
        $this->layoutMock = $this->getMock('Magento\Framework\View\Layout', ['getBlock'], [], '', false);

        $this->productContextMock->expects($this->once())
            ->method('getLayout')
            ->will($this->returnValue($this->layoutMock));

        $this->block = new \Magento\Catalog\Block\Product\View\Type\Simple(
            $this->productContextMock,
            $arrayUtilsMock
        );
    }

    /**
     * Test for method getProductPrice
     *
     * @covers \Magento\Catalog\Block\Product\AbstractProduct::getProductPriceHtml
     * @covers \Magento\Catalog\Block\Product\AbstractProduct::getProductPrice
     */
    public function testGetProductPrice()
    {
        $expectedPriceHtml = '<html>Expected Price html with price $30</html>';
        $priceRenderBlock = $this->getMock('Magento\Pricing\Render', ['render'], [], '', false);
        $product = $this->getMock('Magento\Catalog\Model\Product', [], [], '', false);

        $this->layoutMock->expects($this->once())
            ->method('getBlock')
            ->with('product.price.render.default')
            ->will($this->returnValue($priceRenderBlock));
        $priceRenderBlock->expects($this->once())
            ->method('render')
            ->will($this->returnValue($expectedPriceHtml));

        $this->assertEquals($expectedPriceHtml, $this->block->getProductPrice($product));

    }

    /**
     * Test testGetProductPriceHtml
     */
    public function testGetProductPriceHtml()
    {
        $expectedPriceHtml = '<html>Expected Price html with price $30</html>';
        $priceRenderBlock = $this->getMock('Magento\Pricing\Render', ['render'], [], '', false);
        $product = $this->getMock('Magento\Catalog\Model\Product', [], [], '', false);

        $this->layoutMock->expects($this->once())
            ->method('getBlock')
            ->with('product.price.render.default')
            ->will($this->returnValue($priceRenderBlock));

        $priceRenderBlock->expects($this->once())
            ->method('render')
            ->will($this->returnValue($expectedPriceHtml));

        $this->assertEquals($expectedPriceHtml, $this->block->getProductPriceHtml(
            $product, 'price_code', 'zone_code'
        ));

    }
}