<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Pricing\Render;

/**
 * Class FinalPriceBoxTest
 */
class FinalPriceBoxTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Catalog\Pricing\Render\FinalPriceBox
     */
    protected $object;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $priceType;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $priceInfo;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $priceBox;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $layout;

    /**
     * @var \Magento\Catalog\Model\Product|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $product;

    /**
     * @var \Magento\Logger|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $logger;

    /**
     * @var \Magento\Pricing\Render\RendererPool|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $rendererPool;

    /**
     * @var \Magento\Pricing\Price\PriceInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $price;

    protected function setUp()
    {
        $this->product = $this->getMockForAbstractClass('Magento\Pricing\Object\SaleableInterface', [], '', true, true, true, ['getPriceInfo', '__wakeup']);

        $this->priceType = $this->getMockBuilder('Magento\Catalog\Pricing\Price\MsrpPrice')
            ->disableOriginalConstructor()
            ->setMethods(['isShowPriceOnGesture', 'getMsrpPriceMessage', 'canApplyMsrp'])
            ->getMock();

        $this->priceInfo = $this->getMockBuilder('Magento\Pricing\PriceInfo')
            ->disableOriginalConstructor()
            ->setMethods(['getPrice'])
            ->getMock();

        $this->product->expects($this->any())
            ->method('getPriceInfo')
            ->will($this->returnValue($this->priceInfo));

        $eventManager = $this->getMock('Magento\Event\ManagerStub', [], [], '', false);
        $config = $this->getMock('Magento\Core\Model\Store\Config', [], [], '', false);
        $this->layout = $this->getMock('Magento\Core\Model\Layout', [], [], '', false);

        $this->priceBox = $this->getMock('Magento\Pricing\Render\PriceBox', [], [], '', false);
        $this->logger = $this->getMock('Magento\Logger', [], [], '', false);


        $this->layout->expects($this->any())
            ->method('getBlock')
            ->will($this->returnValue($this->priceBox));

        $context = $this->getMock('Magento\View\Element\Template\Context', [], [], '', false);
        $context->expects($this->any())
            ->method('getEventManager')
            ->will($this->returnValue($eventManager));
        $context->expects($this->any())
            ->method('getStoreConfig')
            ->will($this->returnValue($config));
        $context->expects($this->any())
            ->method('getLayout')
            ->will($this->returnValue($this->layout));
        $context->expects($this->any())
            ->method('getLogger')
            ->will($this->returnValue($this->logger));

        $this->rendererPool = $this->getMockBuilder('Magento\Pricing\Render\RendererPool')
            ->disableOriginalConstructor()
            ->getMock();

        $this->price = $this->getMock('Magento\Pricing\Price\PriceInterface');
        $this->price->expects($this->any())
            ->method('getPriceType')
            ->will($this->returnValue(\Magento\Catalog\Pricing\Price\FinalPriceInterface::PRICE_TYPE_FINAL));

        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->object = $objectManager->getObject('Magento\Catalog\Pricing\Render\FinalPriceBox', array(
            'context' => $context,
            'saleableItem' => $this->product,
            'rendererPool' => $this->rendererPool,
            'price' => $this->price
        ));
    }

    public function testRenderMsrpDisabled()
    {
        $this->priceInfo->expects($this->once())
            ->method('getPrice')
            ->with($this->equalTo('msrp'))
            ->will($this->returnValue($this->priceType));

        $this->priceType->expects($this->any())
            ->method('canApplyMsrp')
            ->with($this->equalTo($this->product))
            ->will($this->returnValue(false));

        $result = $this->object->toHtml();

        //assert price wrapper
        $this->assertStringStartsWith('<div', $result);
        //assert css_selector
        $this->assertRegExp('/[final_price]/', $result);
    }

    public function testRenderMsrpEnabled()
    {
        $this->priceInfo->expects($this->once())
            ->method('getPrice')
            ->with($this->equalTo('msrp'))
            ->will($this->returnValue($this->priceType));

        $this->priceType->expects($this->any())
            ->method('canApplyMsrp')
            ->with($this->equalTo($this->product))
            ->will($this->returnValue(true));

        $priceBoxRender = $this->getMockBuilder('Magento\Pricing\Render\PriceBox')
            ->disableOriginalConstructor()
            ->getMock();
        $priceBoxRender->expects($this->once())
            ->method('toHtml')
            ->will($this->returnValue('test'));

        $this->rendererPool->expects($this->once())
            ->method('createPriceRender')
            ->with('msrp')
            ->will($this->returnValue($priceBoxRender));

        $result = $this->object->toHtml();

        //assert price wrapper
        $this->assertEquals('<div class="price-box price-final_price">test</div>', $result);
    }

    public function testRenderMsrpNotRegisteredException()
    {
        $this->logger->expects($this->once())
            ->method('logException');

        $this->priceInfo->expects($this->once())
            ->method('getPrice')
            ->with($this->equalTo('msrp'))
            ->will($this->throwException(new \InvalidArgumentException()));

        $result = $this->object->toHtml();

        //assert price wrapper
        $this->assertStringStartsWith('<div', $result);
        //assert css_selector
        $this->assertRegExp('/[final_price]/', $result);
    }
}
