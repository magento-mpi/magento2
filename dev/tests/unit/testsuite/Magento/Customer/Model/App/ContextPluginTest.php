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

namespace Magento\Customer\Model\App;

/**
 * Class ContextPluginTest
 */
class ContextPluginTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Customer\Model\App\ContextPlugin
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
     * @var \Magento\App\FrontController
     */
    protected $frontControllerMock;

    /**
     * Set up
     */
    public function setUp()
    {
        $this->customerSessionMock = $this->getMock('Magento\Customer\Model\Session',
            array(), array(), '', false);
        $this->httpContextMock = $this->getMock('Magento\App\Http\Context',
            array(), array(), '', false);
        $this->frontControllerMock = $this->getMock('Magento\App\FrontController',
            array(), array(), '', false);
        $this->plugin = new \Magento\Customer\Model\App\ContextPlugin(
            $this->customerSessionMock,
            $this->httpContextMock
        );
    }

    /**
     * Test beforeLaunch
     */
    public function testBeforeDispatch()
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
        $this->assertNull($this->plugin->beforeDispatch($this->frontControllerMock));
    }
}
