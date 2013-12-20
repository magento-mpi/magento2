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

use Magento\Checkout\Block\Cart\Item\Renderer as Renderer;

class RendererTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\App\Helper\HelperFactory|\PHPUnit_Framework_MockObject_MockObject */
    protected $_helperFactory;

    /** @var Renderer */
    protected $_renderer;

    protected function setUp()
    {
        parent::setUp();
        $objectManagerHelper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->_helperFactory = $this->getMock('Magento\App\Helper\HelperFactory', array(), array(), '', false);
        $this->_renderer = $objectManagerHelper->getObject(
            'Magento\Checkout\Block\Cart\Item\Renderer',
            ['helperFactory' => $this->_helperFactory]
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

        /** @var \Magento\Catalog\Helper\Image|\PHPUnit_Framework_MockObject_MockObject $imageHelper */
        $imageHelper = $this->getMock('Magento\Catalog\Helper\Image', array(), array(), '', false);
        /** Ensure that image helper was initialized with correct arguments */
        $imageHelper->expects($this->once())
            ->method('init')
            ->with($productForThumbnail, 'thumbnail')
            ->will($this->returnSelf());

        $this->_helperFactory
            ->expects($this->any())
            ->method('get')
            ->with('Magento\Catalog\Helper\Image')
            ->will($this->returnValue($imageHelper));

        $productThumbnail = $this->_renderer->getProductThumbnail();
        $this->assertSame($imageHelper, $productThumbnail, 'Invalid product thumbnail is returned.');
    }

    /**
     * Initialize product.
     *
     * @return \Magento\Catalog\Model\Product|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function _initProduct()
    {
        /** @var \Magento\Catalog\Model\Product|\PHPUnit_Framework_MockObject_MockObject $product */
        $product = $this->getMock('Magento\Catalog\Model\Product', array(), array(), '', false);
        $product->expects($this->any())->method('getName')->will($this->returnValue('Parent Product'));

        /** @var \Magento\Sales\Model\Quote\Item|\PHPUnit_Framework_MockObject_MockObject $item */
        $item = $this->getMock('Magento\Sales\Model\Quote\Item', array(), array(), '', false);
        $item->expects($this->any())->method('getProduct')->will($this->returnValue($product));

        $this->_renderer->setItem($item);
        return $product;
    }
}
