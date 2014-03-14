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
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @package Magento\Customer\Model\Layout
 */
class DepersonalizePluginTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var DepersonalizePlugin
     */
    protected $plugin;

    /**
     * @var \Magento\Core\Model\Layout|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $layoutMock;

    /**
     * @var \Magento\Core\Model\Session|\PHPUnit_Framework_MockObject_MockObject
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
     * @var \Magento\App\Request\Http|\PHPUnit_Framework_MockObject_MockObject
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
            $this->sessionMock,
            $this->customerSessionMock,
            $this->customerFactoryMock,
            $this->requestMock,
            $this->moduleManagerMock,
            $this->visitorMock,
            $this->httpContext
        );
    }

    /**
     * Test method beforeGenerateXml with enabled module PageCache
     */
    public function testBeforeGenerateXmlPageCacheEnabled()
    {
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
        $this->customerSessionMock->expects($this->once())
            ->method('getCustomerGroupId');
        $this->sessionMock->expects($this->once())
            ->method('getData')
            ->with($this->equalTo(\Magento\Data\Form\FormKey::FORM_KEY));
        $output = $this->plugin->beforeGenerateXml($this->layoutMock);
        $this->assertEquals(array(), $output);
    }

    /**
     * Test method beforeGenerateXml with disabled module PageCache
     */
    public function testBeforeGenerateXmlPageCacheDisabled()
    {
        $this->moduleManagerMock
            ->expects($this->once())
            ->method('isEnabled')
            ->with('Magento_PageCache')
            ->will($this->returnValue(false));
        $this->requestMock
            ->expects($this->never())
            ->method('isAjax');
        $output = $this->plugin->beforeGenerateXml($this->layoutMock);
        $this->assertEquals(array(), $output);
    }

    /**
     * Test beforeGenerateXml method with enabled module PageCache and request is Ajax
     */
    public function testBeforeGenerateXmlRequestIsAjax()
    {
        $this->moduleManagerMock
            ->expects($this->once())
            ->method('isEnabled')
            ->with('Magento_PageCache')
            ->will($this->returnValue(true));
        $this->requestMock
            ->expects($this->once())
            ->method('isAjax')
            ->will($this->returnValue(true));
        $this->layoutMock->expects($this->never())
            ->method('isCacheable');
        $output = $this->plugin->beforeGenerateXml($this->layoutMock);
        $this->assertEquals(array(), $output);
    }

    /**
     * Test beforeGenerateXml method with enabled module PageCache and request is Ajax and Layout is not cacheable
     */
    public function testBeforeGenerateXmlLayoutIsNotCacheable()
    {
        $this->moduleManagerMock
            ->expects($this->once())
            ->method('isEnabled')
            ->with('Magento_PageCache')
            ->will($this->returnValue(true));
        $this->requestMock
            ->expects($this->once())
            ->method('isAjax')
            ->will($this->returnValue(false));
        $this->layoutMock->expects($this->once())
            ->method('isCacheable')
            ->will($this->returnValue(false));
        $this->customerSessionMock->expects($this->never())
            ->method('getCustomerGroupId');
        $output = $this->plugin->beforeGenerateXml($this->layoutMock);
        $this->assertEquals(array(), $output);
    }

//*
    /**
     * Test method afterGenerateXml with enabled module PageCache
     */
    public function testAfterGenerateXmlPageCacheEnabled()
    {
        $expectedResult = 'expected-result-fake';
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
            ->with($this->equalTo(null));
        $this->customerMock
            ->expects($this->once())
            ->method('setGroupId')
            ->with($this->equalTo(null));
        $this->httpContext
            ->expects($this->once())
            ->method('setValue')
            ->with($this->equalTo(\Magento\Customer\Helper\Data::CONTEXT_GROUP),
                $this->equalTo(null));
        $this->sessionMock
            ->expects($this->once())
            ->method('setData')
            ->with(
                $this->equalTo(\Magento\Data\Form\FormKey::FORM_KEY),
                $this->equalTo(null)
            );
        $this->customerSessionMock
            ->expects($this->once())
            ->method('setCustomer')
            ->with($this->equalTo($this->customerMock));
        $actualResult = $this->plugin->afterGenerateXml($this->layoutMock, $expectedResult);
        $this->assertSame($expectedResult, $actualResult);
    }

    /**
     * Test afterGenerateXml method with disabled module PageCache
     */
    public function testAfterGenerateXmlPageCacheDisabled()
    {
        $expectedResult = 'expected-result-fake';
        $this->moduleManagerMock
            ->expects($this->once())
            ->method('isEnabled')
            ->with('Magento_PageCache')
            ->will($this->returnValue(false));
        $this->requestMock
            ->expects($this->never())
            ->method('isAjax');
        $actualResult = $this->plugin->afterGenerateXml($this->layoutMock, $expectedResult);
        $this->assertSame($expectedResult, $actualResult);
    }

    /**
     * Test afterGenerateXml method with enabled module PageCache and request is Ajax
     */
    public function testAfterGenerateXmlRequestIsAjax()
    {
        $expectedResult = 'expected-result-fake';
        $this->moduleManagerMock
            ->expects($this->once())
            ->method('isEnabled')
            ->with('Magento_PageCache')
            ->will($this->returnValue(true));
        $this->requestMock
            ->expects($this->once())
            ->method('isAjax')
            ->will($this->returnValue(true));
        $this->layoutMock->expects($this->never())
            ->method('isCacheable');
        $actualResult = $this->plugin->afterGenerateXml($this->layoutMock, $expectedResult);
        $this->assertSame($expectedResult, $actualResult);
    }

    /**
     * Test afterGenerateXml method with enabled module PageCache and request is Ajax and Layout is not cacheable
     */
    public function testAfterGenerateXmlLayoutIsNotCacheable()
    {
        $expectedResult = 'expected-result-fake';
        $this->moduleManagerMock
            ->expects($this->once())
            ->method('isEnabled')
            ->with('Magento_PageCache')
            ->will($this->returnValue(true));
        $this->requestMock
            ->expects($this->once())
            ->method('isAjax')
            ->will($this->returnValue(false));
        $this->layoutMock->expects($this->once())
            ->method('isCacheable')
            ->will($this->returnValue(false));
        $this->visitorMock
            ->expects($this->never())
            ->method('setSkipRequestLogging');
        $actualResult = $this->plugin->afterGenerateXml($this->layoutMock, $expectedResult);
        $this->assertSame($expectedResult, $actualResult);
    }
}
