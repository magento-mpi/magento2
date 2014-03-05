<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Customer
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CustomerSegment\Model\Layout;

/**
 * Class DepersonalizePluginTest
 * @package Magento\CustomerSegment\Model\Layout
 */
class DepersonalizePluginTest  extends \PHPUnit_Framework_TestCase
{
    /**
     * @var DepersonalizePlugin
     */
    protected $plugin;

    /**
     * @var \Magento\View\LayoutInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $layoutMock;

    /**
     * @var \Magento\Session\SessionManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $sessionMock;

    /**
     * @var \Magento\Customer\Model\Session|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $customerSessionMock;

    /**
     * @var \Magento\Customer\Model\CustomerFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $customerFactoryMock;

    /**
     * @var \Magento\Event\Manager|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $eventManagerMock;

    /**
     * @var \Magento\App\RequestInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $requestMock;

    /**
     * @var \Magento\Customer\Model\Customer|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $customerMock;

    /**
     * @var \Magento\App\Http\Context|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $httpContext;

    /**
     * SetUp
     */
    public function setUp()
    {
        $this->httpContext = $this->getMock('Magento\App\Http\Context', array(), array(), '', false);
        $this->layoutMock = $this->getMock('Magento\Core\Model\Layout', array(), array(), '', false);
        $this->customerSessionMock = $this->getMock(
            'Magento\Customer\Model\Session',
            array('getCustomerSegmentIds', 'setCustomerSegmentIds'),
            array(),
            '',
            false
        );
        $this->requestMock = $this->getMock('Magento\App\Request\Http', array(), array(), '', false);

        $this->plugin = new DepersonalizePlugin(
            $this->layoutMock,
            $this->customerSessionMock,
            $this->requestMock,
            $this->httpContext
        );
    }

    /**
     * testDepersonalize
     */
    public function testDepersonalize()
    {
        $expectedCustomerSegmentIds = array(1, 2, 3);
        $this->requestMock->expects($this->once())
            ->method('isAjax')
            ->will($this->returnValue(false));
        $this->layoutMock->expects($this->once())
            ->method('isCacheable')
            ->will($this->returnValue(true));
        $this->customerSessionMock->expects($this->once())
            ->method('getCustomerSegmentIds')
            ->will($this->returnValue($expectedCustomerSegmentIds));
        $this->customerSessionMock->expects($this->once())
            ->method('setCustomerSegmentIds')
            ->with($this->equalTo($expectedCustomerSegmentIds));

        $this->httpContext->expects($this->once())
            ->method('setValue')
            ->with($this->equalTo(\Magento\CustomerSegment\Helper\Data::CONTEXT_SEGMENT),
                $this->equalTo($expectedCustomerSegmentIds)
            );

        $this->plugin->beforeGenerateXml($this->layoutMock);
        $result = 'data';
        $this->assertEquals($result, $this->plugin->afterGenerateXml($this->layoutMock, $result));
    }

    /**
     * testUsualBehaviorIsAjax
     */
    public function testUsualBehaviorIsAjax()
    {
        $this->requestMock->expects($this->once())
            ->method('isAjax')
            ->will($this->returnValue(true));
        $this->layoutMock->expects($this->never())
            ->method('isCacheable');
        $this->plugin->beforeGenerateXml($this->layoutMock);
        $result = 'data';
        $this->assertEquals($result, $this->plugin->afterGenerateXml($this->layoutMock, $result));
    }

    /**
     * testUsualBehaviorNonCacheable
     */
    public function testUsualBehaviorNonCacheable()
    {
        $this->requestMock->expects($this->once())
            ->method('isAjax')
            ->will($this->returnValue(false));
        $this->layoutMock->expects($this->once())
            ->method('isCacheable')
            ->will($this->returnValue(false));
        $this->customerSessionMock->expects($this->never())
            ->method('setCustomerSegmentIds');
        $this->plugin->beforeGenerateXml($this->layoutMock);
        $result = 'data';
        $this->assertEquals($result, $this->plugin->afterGenerateXml($this->layoutMock, $result));
    }
}
