<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Checkout
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Checkout\Block\Cart\Item;

use Magento\Checkout\Block\Cart\Item\Renderer;

class RendererTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Catalog\Helper\Image|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_imageHelper;

    /**
     * @var Renderer
     */
    protected $_renderer;

    /**
     * @var \Magento\View\LayoutInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $layout;

    protected function setUp()
    {
        parent::setUp();
        $objectManagerHelper = new \Magento\TestFramework\Helper\ObjectManager($this);

        $this->_imageHelper = $this->getMock('Magento\Catalog\Helper\Image', array(), array(), '', false);
        $this->layout = $this->getMock('Magento\View\LayoutInterface');

        $context = $this->getMockBuilder('Magento\View\Element\Template\Context')
            ->disableOriginalConstructor()
            ->getMock();
        $context->expects($this->once())
            ->method('getLayout')
            ->will($this->returnValue($this->layout));

        $this->_renderer = $objectManagerHelper->getObject(
            'Magento\Checkout\Block\Cart\Item\Renderer',
            array('imageHelper' => $this->_imageHelper, 'context' => $context)
        );
    }

    public function testGetProductForThumbnail()
    {
        $product = $this->_initProduct();
        $productForThumbnail = $this->_renderer->getProductForThumbnail();
        $this->assertEquals($product->getName(), $productForThumbnail->getName(), 'Invalid product was returned.');
    }

    public function testGetProductThumbnail()
    {
        $productForThumbnail = $this->_initProduct();
        /** Ensure that image helper was initialized with correct arguments */
        $this->_imageHelper->expects(
            $this->once()
        )->method(
            'init'
        )->with(
            $productForThumbnail,
            'thumbnail'
        )->will(
            $this->returnSelf()
        );
        $productThumbnail = $this->_renderer->getProductThumbnail();
        $this->assertSame($this->_imageHelper, $productThumbnail, 'Invalid product thumbnail is returned.');
    }

    /**
     * Initialize product.
     *
     * @return \Magento\Catalog\Model\Product|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function _initProduct()
    {
        /** @var \Magento\Catalog\Model\Product|\PHPUnit_Framework_MockObject_MockObject $product */
        $product = $this->getMock(
            'Magento\Catalog\Model\Product',
            array('getName', '__wakeup', 'getIdentities'),
            array(), '', false
        );
        $product->expects($this->any())->method('getName')->will($this->returnValue('Parent Product'));

        /** @var \Magento\Sales\Model\Quote\Item|\PHPUnit_Framework_MockObject_MockObject $item */
        $item = $this->getMock('Magento\Sales\Model\Quote\Item', array(), array(), '', false);
        $item->expects($this->any())->method('getProduct')->will($this->returnValue($product));

        $this->_renderer->setItem($item);
        return $product;
    }

    public function testGetIdentities()
    {
        $product = $this->_initProduct();
        $identities = [1 => 1, 2 => 2, 3 => 3];
        $product->expects($this->exactly(2))
            ->method('getIdentities')
            ->will($this->returnValue($identities));

        $this->assertEquals($product->getIdentities(), $this->_renderer->getIdentities());
    }

    public function testGetIdentitiesFromEmptyItem()
    {
        $this->assertEmpty($this->_renderer->getIdentities());
    }

    /**
     * @covers \Magento\Checkout\Block\Cart\Item\Renderer::getProductPriceHtml
     * @covers \Magento\Checkout\Block\Cart\Item\Renderer::getPriceRender
     */
    public function testGetProductPriceHtml()
    {
        $priceHtml = 'some price html';
        $product = $this->getMockBuilder('Magento\Catalog\Model\Product')
            ->disableOriginalConstructor()
            ->getMock();

        $priceRender = $this->getMockBuilder('Magento\Pricing\Render')
            ->disableOriginalConstructor()
            ->getMock();

        $this->layout->expects($this->atLeastOnce())
            ->method('getBlock')
            ->with('product.price.render.default')
            ->will($this->returnValue($priceRender));

        $priceRender->expects($this->once())
            ->method('render')
            ->with(
                \Magento\Catalog\Pricing\Price\FinalPrice::PRICE_TYPE_CODE,
                $product,
                [
                    'include_container' => true,
                    'display_minimal_price' => true,
                    'zone' => \Magento\Pricing\Render::ZONE_ITEM_LIST
                ]
            )->will($this->returnValue($priceHtml));

        $this->assertEquals($priceHtml, $this->_renderer->getProductPriceHtml($product));
    }
}
