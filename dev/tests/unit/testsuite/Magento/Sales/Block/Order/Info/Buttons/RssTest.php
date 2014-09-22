<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Block\Order\Info\Buttons;

use \Magento\TestFramework\Helper\ObjectManager as ObjectManagerHelper;

/**
 * Class RssTest
 * @package Magento\Sales\Block\Order\Info\Buttons
 */
class RssTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Sales\Block\Order\Info\Buttons\Rss
     */
    protected $rss;

    /**
     * @var ObjectManagerHelper
     */
    protected $objectManagerHelper;

    /**
     * @var \Magento\Framework\View\Element\Template\Context|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $context;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $orderFactory;

    /**
     * @var \Magento\Framework\App\Rss\UrlBuilderInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $urlBuilderInterface;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $scopeConfigInterface;

    protected function setUp()
    {
        $this->context = $this->getMock('Magento\Framework\View\Element\Template\Context', [], [], '', false);
        $this->orderFactory = $this->getMock('Magento\Sales\Model\OrderFactory', ['create'], [], '', false);
        $this->urlBuilderInterface = $this->getMock('Magento\Framework\App\Rss\UrlBuilderInterface');
        $this->scopeConfigInterface = $this->getMock('Magento\Framework\App\Config\ScopeConfigInterface');
        $request = $this->getMock('Magento\Framework\App\RequestInterface');

        $this->objectManagerHelper = new ObjectManagerHelper($this);
        $this->rss = $this->objectManagerHelper->getObject(
            'Magento\Sales\Block\Order\Info\Buttons\Rss',
            [
                'request' => $request,
                'orderFactory' => $this->orderFactory,
                'rssUrlBuilder' => $this->urlBuilderInterface,
                'scopeConfig' => $this->scopeConfigInterface
            ]
        );
    }

    public function testGetLink()
    {
        $order = $this->getMockBuilder('Magento\Sales\Model\Order')
            ->setMethods(array('getId', 'getCustomerId', 'getIncrementId', 'load', '__wakeup', '__sleep'))
            ->disableOriginalConstructor()
            ->getMock();
        $order->expects($this->once())->method('load')->will($this->returnSelf());
        $order->expects($this->once())->method('getId')->will($this->returnValue(1));
        $order->expects($this->once())->method('getCustomerId')->will($this->returnValue(1));
        $order->expects($this->once())->method('getIncrementId')->will($this->returnValue('100000001'));

        $this->orderFactory->expects($this->once())->method('create')->will($this->returnValue($order));

        $data = base64_encode(json_encode(array('order_id' => 1, 'increment_id' => '100000001', 'customer_id' => 1, )));
        $link = 'http://magento.com/rss/feed/index/type/order_status?data=' . $data;
        $this->urlBuilderInterface->expects($this->once())->method('getUrl')
            ->with(array(
                'type' => 'order_status',
                '_secure' => true,
                '_query' => array('data' => $data)
            ))->will($this->returnValue($link));
        $this->assertEquals($link, $this->rss->getLink());
    }

    public function testGetLabel()
    {
        $this->assertEquals('Subscribe to Order Status', $this->rss->getLabel());
    }

    public function testIsRssAllowed()
    {
        $this->scopeConfigInterface->expects($this->once())->method('isSetFlag')
            ->with('rss/order/status', \Magento\Store\Model\ScopeInterface::SCOPE_STORE)
            ->will($this->returnValue(true));
        $this->assertTrue($this->rss->isRssAllowed());
    }
}
