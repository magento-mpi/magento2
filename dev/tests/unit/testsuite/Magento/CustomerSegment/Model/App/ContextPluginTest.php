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

namespace Magento\CustomerSegment\Model\App;

/**
 * Class ContextPluginTest
 */
class ContextPluginTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\CustomerSegment\Model\App\ContextPlugin
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
     * @var \Magento\CustomerSegment\Model\Customer|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $customerSegmentMock;

    /**
     * @var \Magento\Core\Model\StoreManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $storeManagerMock;

    /**
     * @var \Magento\App\FrontController|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $frontControllerMock;

    /**
     * @var \Magento\Core\Model\Website|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $websiteMock;

    /**
     * Set up
     */
    public function setUp()
    {
        $this->customerSessionMock = $this->getMock('Magento\Customer\Model\Session',
            array('getCustomerId', '__wakeup'),
            array(),
            '',
            false
        );
        $this->httpContextMock = $this->getMock('Magento\App\Http\Context', array(), array(), '', false);
        $this->customerSegmentMock = $this->getMock('Magento\CustomerSegment\Model\Customer',
            array('getCustomerId', '__wakeup', 'getCustomerSegmentIdsForWebsite'),
            array(),
            '',
            false
        );
        $this->storeManagerMock = $this->getMockForAbstractClass('Magento\Core\Model\StoreManagerInterface',
            array(),
            '',
            false
        );
        $this->frontControllerMock = $this->getMock('Magento\App\FrontController', array(), array(), '', false);
        $this->websiteMock = $this->getMock('Magento\Core\Model\Website',
            array('__wakeup', 'getId'),
            array(),
            '',
            false
        );

        $this->plugin = new \Magento\CustomerSegment\Model\App\ContextPlugin(
            $this->customerSessionMock,
            $this->httpContextMock,
            $this->customerSegmentMock,
            $this->storeManagerMock
        );
    }

    /**
     * Test beforeDispatch
     */
    public function testBeforeDispatch()
    {
        $customerId = 1;
        $customerSegmentIds = array(1, 2, 3);
        $websiteId  = 1;

        $this->customerSessionMock->expects($this->exactly(2))
            ->method('getCustomerId')
            ->will($this->returnValue($customerId));

        $this->storeManagerMock->expects($this->once())
            ->method('getWebsite')
            ->will($this->returnValue($this->websiteMock));

        $this->websiteMock->expects($this->once())
            ->method('getId')
            ->will($this->returnValue($websiteId));

        $this->customerSegmentMock->expects($this->once())
            ->method('getCustomerSegmentIdsForWebsite')
            ->with($this->equalTo($customerId), $this->equalTo($websiteId))
            ->will($this->returnValue($customerSegmentIds));

        $this->httpContextMock->expects($this->once())
            ->method('setValue')
            ->with(
                $this->equalTo(\Magento\CustomerSegment\Helper\Data::CONTEXT_SEGMENT),
                $this->equalTo($customerSegmentIds)
            );

        $this->assertNull($this->plugin->beforeDispatch($this->frontControllerMock));
    }
}
