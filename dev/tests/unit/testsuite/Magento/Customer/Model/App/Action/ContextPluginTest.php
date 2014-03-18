<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Model\App\Action;

/**
 * Class ContextPluginTest
 */
class ContextPluginTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Customer\Model\App\Action\ContextPlugin
     */
    protected $plugin;

    /**
     * @var \Magento\Customer\Model\Session|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $customerSessionMock;

    /**
     * @var \Magento\App\Http\Context $httpContext|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $httpContextMock;

    /**
     * @var \Closure
     */
    protected $closureMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $subjectMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $requestMock;

    /**
     * Set up
     */
    public function setUp()
    {
        $this->customerSessionMock = $this->getMock('Magento\Customer\Model\Session',
            array(), array(), '', false);
        $this->httpContextMock = $this->getMock('Magento\App\Http\Context',
            array(), array(), '', false);
        $this->closureMock = function () {
            return 'ExpectedValue';
        };
        $this->subjectMock = $this->getMock('Magento\App\Action\Action', array(), array(), '', false);
        $this->requestMock = $this->getMock('Magento\App\RequestInterface');
        $this->plugin = new \Magento\Customer\Model\App\Action\ContextPlugin(
            $this->customerSessionMock,
            $this->httpContextMock
        );
    }

    /**
     * Test aroundDispatch
     */
    public function testAroundDispatch()
    {
        $this->customerSessionMock->expects($this->once())
            ->method('getCustomerGroupId')
            ->will($this->returnValue(1));
        $this->customerSessionMock->expects($this->once())
            ->method('isLoggedIn')
            ->will($this->returnValue(true));
        $this->httpContextMock->expects($this->atLeastOnce())
            ->method('setValue')
            ->will($this->returnValueMap(array(
                array(\Magento\Customer\Helper\Data::CONTEXT_GROUP, 'UAH', $this->httpContextMock),
                array(\Magento\Customer\Helper\Data::CONTEXT_AUTH, 0, $this->httpContextMock),
            )));
        $this->assertEquals(
            'ExpectedValue',
            $this->plugin->aroundDispatch($this->subjectMock, $this->closureMock, $this->requestMock)
        );
    }
}
