<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_PageCache
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\PageCache\Model\Layout;

class SessionPluginTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\PageCache\Model\Layout\SessionPlugin
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
     * SetUp
     */
    public function setUp()
    {
        $this->layoutMock = $this->getMock('Magento\Core\Model\Layout', array(), array(), '', false);
        $this->sessionMock = $this->getMock('Magento\Core\Model\Session', array('clearStorage'), array(), '', false);
        $this->customerSessionMock = $this->getMock('Magento\Customer\Model\Session',
            array('getCustomerSegmentIds', 'getCustomerGroupId', 'setCustomerSegmentIds',
                'setCustomerGroupId', 'clearStorage', 'setCustomer'),
            array(), '', false);
        $this->customerFactoryMock = $this->getMock('Magento\Customer\Model\CustomerFactory',
            array('create'), array(), '', false);
        $this->eventManagerMock = $this->getMock('Magento\Event\Manager', array(), array(), '', false);

        $this->requestMock = $this->getMock('Magento\App\Request\Http', array(), array(), '', false);
        $this->customerMock = $this->getMock('Magento\Customer\Model\Customer',
            array('setGroupId', '__wakeup'), array(), '', false);

        $this->customerFactoryMock->expects($this->once())
            ->method('create')
            ->will($this->returnValue($this->customerMock));

        $this->plugin = new \Magento\PageCache\Model\Layout\SessionPlugin(
            $this->layoutMock,
            $this->sessionMock,
            $this->customerSessionMock,
            $this->customerFactoryMock,
            $this->eventManagerMock,
            $this->requestMock
        );
    }

    /**
     * testDepersonalize
     */
    public function testDepersonalize()
    {
        $expectedCustomerSegmentIds = array(1, 2, 3);
        $expectedCustomerGroupId = 3;
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
            ->method('getCustomerGroupId')
            ->will($this->returnValue($expectedCustomerGroupId));
        $this->eventManagerMock->expects($this->once())
            ->method('dispatch')
            ->with($this->equalTo('before_session_write_close'));
        $this->sessionMock->expects($this->once())
            ->method('clearStorage');
        $this->customerSessionMock->expects($this->once())
            ->method('clearStorage');
        $this->customerSessionMock->expects($this->once())
            ->method('setCustomerSegmentIds')
            ->with($this->equalTo($expectedCustomerSegmentIds));
        $this->customerSessionMock->expects($this->once())
            ->method('setCustomerGroupId')
            ->with($this->equalTo($expectedCustomerGroupId));
        $this->customerMock->expects($this->once())
            ->method('setGroupId')
            ->with($this->equalTo($expectedCustomerGroupId));
        $this->customerSessionMock->expects($this->once())
            ->method('setCustomer')
            ->with($this->equalTo($this->customerMock));
        $this->plugin->beforeGenerateXml(array());
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
        $this->plugin->beforeGenerateXml(array());
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
        $this->eventManagerMock->expects($this->never())
            ->method('dispatch');
        $this->plugin->beforeGenerateXml(array());
    }
}
