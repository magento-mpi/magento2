<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CustomerSegment
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CustomerSegment\Model\Layout;

/**
 * Class DepersonalizePluginTest
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class DepersonalizePluginTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\CustomerSegment\Model\Layout\DepersonalizePlugin
     */
    protected $plugin;

    /**
     * @var \Magento\Framework\View\LayoutInterface|\PHPUnit_Framework_MockObject_MockObject
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
     * @var \Magento\Framework\App\RequestInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $requestMock;

    /**
     * @var \Magento\Customer\Model\Customer|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $customerMock;

    /**
     * @var \Magento\Framework\App\Http\Context|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $httpContextMock;

    /**
     * @var \Magento\Module\Manager|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $moduleManagerMock;

    /**
     * @var \Magento\PageCache\Model\Config|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $cacheConfig;

    /**
     * SetUp
     */
    public function setUp()
    {
        $this->httpContextMock = $this->getMock('Magento\Framework\App\Http\Context', array(), array(), '', false);
        $this->layoutMock = $this->getMock('Magento\Framework\View\Layout', array(), array(), '', false);
        $this->moduleManagerMock = $this->getMock('Magento\Module\Manager', array(), array(), '', false);
        $this->customerSessionMock = $this->getMock('Magento\Customer\Model\Session',
            array('getCustomerSegmentIds', 'setCustomerSegmentIds'),
            array(),
            '',
            false
        );
        $this->requestMock = $this->getMock('Magento\Framework\App\Request\Http', array(), array(), '', false);

        $this->cacheConfig = $this->getMock('Magento\PageCache\Model\Config', array(), array(), '', false);

        $this->plugin = new \Magento\CustomerSegment\Model\Layout\DepersonalizePlugin(
            $this->customerSessionMock,
            $this->requestMock,
            $this->moduleManagerMock,
            $this->httpContextMock,
            $this->cacheConfig
        );
    }

    /**
     * testDepersonalize
     */
    public function testBeforeGenerateXml()
    {
        $expectedCustomerSegmentIds = array(1, 2, 3);
        $defaultCustomerSegmentIds = array();
        $this->moduleManagerMock->expects($this->exactly(2))
            ->method('isEnabled')
            ->with($this->equalTo('Magento_PageCache'))
            ->will($this->returnValue(true));
        $this->cacheConfig->expects($this->exactly(2))
            ->method('isEnabled')
            ->will($this->returnValue(true));
        $this->requestMock->expects($this->exactly(2))
            ->method('isAjax')
            ->will($this->returnValue(false));
        $this->layoutMock->expects($this->exactly(2))
            ->method('isCacheable')
            ->will($this->returnValue(true));
        $this->customerSessionMock->expects($this->once())
            ->method('getCustomerSegmentIds')
            ->will($this->returnValue($expectedCustomerSegmentIds));
        $this->customerSessionMock->expects($this->once())
            ->method('setCustomerSegmentIds')
            ->with($this->equalTo($expectedCustomerSegmentIds));
        $this->httpContextMock->expects($this->once())
            ->method('setValue')
            ->with(
                $this->equalTo(\Magento\CustomerSegment\Helper\Data::CONTEXT_SEGMENT),
                $this->equalTo($expectedCustomerSegmentIds),
                $this->equalTo($defaultCustomerSegmentIds)
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
        $this->moduleManagerMock->expects($this->exactly(2))
            ->method('isEnabled')
            ->with($this->equalTo('Magento_PageCache'))
            ->will($this->returnValue(true));
        $this->cacheConfig->expects($this->exactly(2))
            ->method('isEnabled')
            ->will($this->returnValue(true));
        $this->requestMock->expects($this->exactly(2))
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
        $this->moduleManagerMock->expects($this->exactly(2))
            ->method('isEnabled')
            ->with($this->equalTo('Magento_PageCache'))
            ->will($this->returnValue(true));
        $this->cacheConfig->expects($this->exactly(2))
            ->method('isEnabled')
            ->will($this->returnValue(true));
        $this->requestMock->expects($this->exactly(2))
            ->method('isAjax')
            ->will($this->returnValue(false));
        $this->layoutMock->expects($this->exactly(2))
            ->method('isCacheable')
            ->will($this->returnValue(false));
        $this->customerSessionMock->expects($this->never())
            ->method('setCustomerSegmentIds');
        $this->plugin->beforeGenerateXml($this->layoutMock);
        $result = 'data';
        $this->assertEquals($result, $this->plugin->afterGenerateXml($this->layoutMock, $result));
    }
}
