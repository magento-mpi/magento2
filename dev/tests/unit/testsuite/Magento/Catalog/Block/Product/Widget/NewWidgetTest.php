<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Block\Product\Widget;

class NewWidgetTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Catalog\Block\Product\ListProduct|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $block;

    protected function setUp()
    {
        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->block = $objectManager->getObject('Magento\Catalog\Block\Product\Widget\NewWidget');

        $contextMock = $this->getMock('Magento\View\Element\Template\Context', [], [], '', false, false);
        $catalogConfigMock = $this->getMock('Magento\Catalog\Model\Config', [], [], '', false, false);
        $registryMock = $this->getMock('Magento\Registry', [], [], '', false, false);
        $taxDataMock = $this->getMock('\Magento\Tax\Helper\Data', [], [], '', false, false);
        $catalogDataMock = $this->getMock('Magento\Catalog\Helper\Data', [], [], '', false, false);
        $mathRandomMock = $this->getMock('Magento\Math\Random', [], [], '', false, false);
        $cartHelperMock = $this->getMock('Magento\Checkout\Helper\Cart', [], [], '', false, false);
        $wishlistHelperMock = $this->getMock('Magento\Wishlist\Helper\Data', [], [], '', false, false);
        $compareMock = $this->getMock('Magento\Catalog\Helper\Product\Compare', [], [], '', false, false);
        $layoutHelperMock = $this->getMock('Magento\Theme\Helper\Layout', [], [], '', false, false);
        $imageHelperMock = $this->getMock('Magento\Catalog\Helper\Image', [], [], '', false, false);
        $productCollectionFactoryMock = $this->getMock(
            'Magento\Catalog\Model\Resource\Product\CollectionFactory',
            [],
            [],
            '',
            false,
            false
        );
        $catalogProductVisibilityMock = $this->getMock(
            'Magento\Catalog\Model\Product\Visibility',
            [],
            [],
            '',
            false,
            false
        );
        $httpContextMock = $this->getMock('Magento\App\Http\Context', [], [], '', false, false);

        $this->block = new \Magento\Catalog\Block\Product\Widget\NewWidget(
            $contextMock,
            $catalogConfigMock,
            $registryMock,
            $taxDataMock,
            $catalogDataMock,
            $mathRandomMock,
            $cartHelperMock,
            $wishlistHelperMock,
            $compareMock,
            $layoutHelperMock,
            $imageHelperMock,
            $productCollectionFactoryMock,
            $catalogProductVisibilityMock,
            $httpContextMock
        );
    }

    protected function tearDown()
    {
        $this->block = null;
    }

    public function testGetProductPrice()
    {
        $id = 6;
        $expectedHtml = '';
        $productMock = $this->getMock('Magento\Catalog\Model\Product', [], [], '', false, false);
        $productMock->setEntityId($id);
        $arguments = [
            'price_id' => 'old-price-' . $productMock->getEntityId() . '-widget-new-list',
            'include_container' => true
        ];
        $layoutMock = $this->getMock('Magento\View\Layout\Element\Layout', ['getBlock'], [], '', false, false);
//        $layoutMock = $this->getMockForAbstractClass(
//            'Magento\View\Layout\Element\Layout',
//            array(),
//            '',
//            true,
//            true,
//            true,
//            array('getBlock')
//        );
        $priceBoxMock = $this->getMock('Magento\Pricing\Render\PriceBox', ['render'], [], '', false, false);
//        $this->block->expects($this->once())
//            ->method('getLayout')
//            ->will($this->returnValue($layoutMock));
        $layoutMock->expects($this->once())
            ->method('getBlock')
            ->with($this->equalTo('product.price.render.default'))
            ->will($this->returnValue($priceBoxMock));
        $priceBoxMock->expects($this->once())
            ->method('render');

        $result = $this->block->getProductPrice($productMock, $arguments);
        $this->assertContains($expectedHtml, $result);
    }
}
