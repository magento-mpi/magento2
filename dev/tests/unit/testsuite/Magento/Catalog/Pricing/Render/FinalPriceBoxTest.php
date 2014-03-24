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
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $product;

    /**
     * @var \Magento\Logger|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $logger;

    protected function setUp()
    {
        $this->product = $this->getMock(
            'Magento\Catalog\Model\Product',
            ['getPriceInfo', '__wakeup'],
            [],
            '',
            false
        );


        $this->priceType = $this->getMock(
            'Magento\Catalog\Pricing\Price\MsrpPrice',
            ['isShowPriceOnGesture', 'getMsrpPriceMessage', 'canApplyMsrp'],
            [],
            '',
            false
        );

        $this->priceInfo = $this->getMock(
            'Magento\Pricing\PriceInfo',
            ['getPrice'],
            [],
            '',
            false
        );

        $this->priceInfo->expects($this->at(0))
            ->method('getPrice')
            ->with($this->equalTo('final_price'))
            ->will($this->returnValue($this->priceType));



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

        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->object = $objectManager->getObject(
            'Magento\Catalog\Pricing\Render\FinalPriceBox',
            [
                'context' => $context,
                'data' => array('css_classes' => 'price-final_price')
            ]
        );
    }

    public function testRenderMsrpDisabled()
    {
        $this->priceType->expects($this->any())
            ->method('canApplyMsrp')
            ->with($this->equalTo($this->product))
            ->will($this->returnValue(false));

        $this->priceInfo->expects($this->at(1))
            ->method('getPrice')
            ->with($this->equalTo('msrp'))
            ->will($this->returnValue($this->priceType));

        $result = $this->object->render('final_price', $this->product, []);

        //assert price wrapper
        $this->assertStringStartsWith('<div', $result);
        //assert css_selector
        $this->assertRegExp('/[final_price]/', $result);
    }

    public function testRenderMsrpEnabledChildBlockFalse()
    {
        $this->priceType->expects($this->any())
            ->method('canApplyMsrp')
            ->with($this->equalTo($this->product))
            ->will($this->returnValue(true));

        $this->priceInfo->expects($this->at(1))
            ->method('getPrice')
            ->with($this->equalTo('msrp'))
            ->will($this->returnValue($this->priceType));

        $result = $this->object->render('final_price', $this->product, []);

        //assert price wrapper
        $this->assertStringStartsWith('<div', $result);
        //assert css_selector
        $this->assertRegExp('/[final_price]/', $result);
    }

    public function testRenderMsrpEnabledChildBlockEnabled()
    {
        $this->priceType->expects($this->any())
            ->method('canApplyMsrp')
            ->with($this->equalTo($this->product))
            ->will($this->returnValue(true));

        $this->priceInfo->expects($this->at(1))
            ->method('getPrice')
            ->with($this->equalTo('msrp'))
            ->will($this->returnValue($this->priceType));

        $this->layout->expects($this->any())
            ->method('getChildName')
            ->will($this->returnValue('test_name'));

        $this->priceBox->expects($this->any())
            ->method('render')
            ->with(
                $this->equalTo('msrp'),
                $this->equalTo($this->product),
                $this->equalTo(['real_price_html' => '<div class="price-box price-final_price"></div>'])
            )
            ->will($this->returnValue('test'));

        $result = $this->object->render('final_price', $this->product, []);

        //assert price wrapper
        $this->assertEquals('<div class="price-box price-final_price">test</div>', $result);
    }

    public function testRenderMsrpNotRegisteredException()
    {
        $this->logger->expects($this->once())
            ->method('logException');

        $this->priceType->expects($this->any())
            ->method('canApplyMsrp')
            ->with($this->equalTo($this->product))
            ->will($this->returnValue(false));

        $this->priceInfo->expects($this->at(1))
            ->method('getPrice')
            ->with($this->equalTo('msrp'))
            ->will($this->throwException(new \InvalidArgumentException()));

        $result = $this->object->render('final_price', $this->product, []);

        //assert price wrapper
        $this->assertStringStartsWith('<div', $result);
        //assert css_selector
        $this->assertRegExp('/[final_price]/', $result);
    }
}
