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
namespace Magento\Customer\Model\Layout;

/**
 * Class DepersonalizePluginTest
 *
 * @package Magento\Customer\Model\Layout
 */
class DepersonalizePluginTest extends \PHPUnit_Framework_TestCase
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
     * @var \Magento\Module\Manager|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $moduleManagerMock;

    /**
     * @var \Magento\App\Http\Context|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $httpContext;

    /**
     * @var \Magento\Log\Model\Visitor|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $visitorMock;

    /**
     * SetUp
     */
    public function setUp()
    {
        $this->httpContext = $this->getMock('Magento\App\Http\Context', array(), array(), '', false);
        $this->layoutMock = $this->getMock('Magento\Core\Model\Layout', array(), array(), '', false);
        $this->sessionMock = $this->getMock(
            'Magento\Core\Model\Session',
            array('clearStorage', 'setData', 'getData'),
            array(),
            '',
            false
        );
        $this->customerSessionMock = $this->getMock(
            'Magento\Customer\Model\Session',
            array('getCustomerGroupId', 'setCustomerGroupId', 'clearStorage', 'setCustomer'),
            array(),
            '',
            false
        );
        $this->customerFactoryMock = $this->getMock(
            'Magento\Customer\Model\CustomerFactory',
            array('create'),
            array(),
            '',
            false
        );
        $this->eventManagerMock = $this->getMock('Magento\Event\Manager', array(), array(), '', false);
        $this->requestMock = $this->getMock('Magento\App\Request\Http', array(), array(), '', false);
        $this->customerMock = $this->getMock(
            'Magento\Customer\Model\Customer',
            array('setGroupId', '__wakeup'),
            array(),
            '',
            false
        );
        $this->moduleManagerMock = $this->getMock('Magento\Module\Manager', array(), array(), '', false);
        $this->visitorMock = $this->getMock('Magento\Log\Model\Visitor', array(), array(), '', false);
        $this->customerFactoryMock->expects($this->once())
            ->method('create')
            ->will($this->returnValue($this->customerMock));

        $this->plugin = new DepersonalizePlugin(
            $this->layoutMock,
            $this->sessionMock,
            $this->customerSessionMock,
            $this->customerFactoryMock,
            $this->eventManagerMock,
            $this->requestMock,
            $this->moduleManagerMock,
            $this->visitorMock,
            $this->httpContext
        );
    }

    /**
     * testDepersonalize
     */
    public function testDepersonalize()
    {
        $formKey = md5('form_key');
        $expectedCustomerGroupId = 3;
        $result = 'test';

        $this->moduleManagerMock
            ->expects($this->once())
            ->method('isEnabled')
            ->with('Magento_PageCache')
            ->will($this->returnValue(true));
        $this->requestMock
            ->expects($this->once())
            ->method('isAjax')
            ->will($this->returnValue(false));
        $this->layoutMock
            ->expects($this->once())
            ->method('isCacheable')
            ->will($this->returnValue(true));
        $this->sessionMock
            ->expects($this->any())
            ->method('getData')
            ->with($this->equalTo(\Magento\Data\Form\FormKey::FORM_KEY))
            ->will($this->returnValue($formKey));
        $this->customerSessionMock
            ->expects($this->once())
            ->method('getCustomerGroupId')
            ->will($this->returnValue($expectedCustomerGroupId));
        $this->eventManagerMock
            ->expects($this->once())
            ->method('dispatch')
            ->with($this->equalTo('before_session_write_close'));
        $this->visitorMock
            ->expects($this->once())
            ->method('setSkipRequestLogging')
            ->with($this->equalTo(true));
        $this->visitorMock
            ->expects($this->once())
            ->method('unsetData');
        $this->sessionMock
            ->expects($this->once())
            ->method('clearStorage');
        $this->customerSessionMock
            ->expects($this->once())
            ->method('clearStorage');
        $this->customerSessionMock
            ->expects($this->once())
            ->method('setCustomerGroupId')
            ->with($this->equalTo($expectedCustomerGroupId));
        $this->customerMock
            ->expects($this->once())
            ->method('setGroupId')
            ->with($this->equalTo($expectedCustomerGroupId));
        $this->httpContext
            ->expects($this->once())
            ->method('setValue')
            ->with($this->equalTo(\Magento\Customer\Helper\Data::CONTEXT_GROUP),
                $this->equalTo($expectedCustomerGroupId));
        $this->sessionMock
            ->expects($this->once())
            ->method('setData')
            ->with(
                $this->equalTo(\Magento\Data\Form\FormKey::FORM_KEY),
                $this->equalTo($formKey)
            );
        $this->customerSessionMock
            ->expects($this->once())
            ->method('setCustomer')
            ->with($this->equalTo($this->customerMock));
        $output = $this->plugin->afterGenerateXml($this->layoutMock, $result);
        $this->assertSame($result, $output);
    }

    /**
     * testUsualBehaviorIsAjax
     */
    public function testUsualBehaviorIsAjax()
    {
        $result = 'test';

        $this->moduleManagerMock
            ->expects($this->once())
            ->method('isEnabled')
            ->with('Magento_PageCache')
            ->will($this->returnValue(true));
        $this->requestMock
            ->expects($this->once())
            ->method('isAjax')
            ->will($this->returnValue(true));
        $this->layoutMock
            ->expects($this->never())
            ->method('isCacheable');
        $output = $this->plugin->afterGenerateXml($this->layoutMock, $result);
        $this->assertSame($result, $output);
    }

    /**
     * testUsualBehaviorNonCacheable
     */
    public function testUsualBehaviorNonCacheable()
    {
        $result = 'test';

        $this->moduleManagerMock
            ->expects($this->once())
            ->method('isEnabled')
            ->with('Magento_PageCache')
            ->will($this->returnValue(true));
        $this->requestMock
            ->expects($this->once())
            ->method('isAjax')
            ->will($this->returnValue(false));
        $this->layoutMock
            ->expects($this->once())
            ->method('isCacheable')
            ->will($this->returnValue(false));
        $this->eventManagerMock
            ->expects($this->never())
            ->method('dispatch');
        $output = $this->plugin->afterGenerateXml($this->layoutMock, $result);
        $this->assertSame($result, $output);
    }

    /**
     * testUsualBehaviorPageCacheInNotEnabled
     */
    public function testUsualBehaviorPageCacheInNotEnabled()
    {
        $result = 'test';

        $this->moduleManagerMock
            ->expects($this->once())
            ->method('isEnabled')
            ->with('Magento_PageCache')
            ->will($this->returnValue(false));
        $this->eventManagerMock
            ->expects($this->never())
            ->method('dispatch');
        $output = $this->plugin->afterGenerateXml($this->layoutMock, $result);
        $this->assertSame($result, $output);
    }
}
