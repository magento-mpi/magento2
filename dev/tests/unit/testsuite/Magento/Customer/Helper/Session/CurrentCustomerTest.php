<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Helper\Session;

class CurrentCustomerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Customer\Helper\Session\CurrentCustomer
     */
    protected $currentCustomer;

    /**
     * @var \Magento\Customer\Model\Session|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $customerSessionMock;

    /**
     * @var \Magento\Framework\View\LayoutInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $layoutMock;

    /**
     * @var \Magento\Customer\Api\Data\CustomerDataBuilder|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $customerDataBuilderMock;

    /**
     * @var \Magento\Customer\Api\Data\CustomerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $customerDataMock;

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $customerServiceMock;

    /**
     * @var \Magento\Framework\App\RequestInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $requestMock;

    /**
     * @var \Magento\Framework\Module\Manager|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $moduleManagerMock;

    /**
     * @var \Magento\Framework\App\ViewInterface
     */
    protected $viewMock;

    /**
     * @var int
     */
    protected $customerId = 100;

    /**
     * @var int
     */
    protected $customerGroupId = 500;

    /**
     * Test setup
     */
    public function setUp()
    {
        $this->customerSessionMock = $this->getMock('Magento\Customer\Model\Session', array(), array(), '', false);
        $this->layoutMock = $this->getMock('Magento\Framework\View\Layout', array(), array(), '', false);
        $this->customerDataBuilderMock = $this->getMock(
            'Magento\Customer\Api\Data\CustomerDataBuilder',
            array('create', 'setGroupId'),
            array(),
            '',
            false
        );
        $this->customerDataMock = $this->getMock(
            'Magento\Customer\Api\Data\CustomerInterface',
            array(),
            array(),
            '',
            false
        );
        $this->customerServiceMock = $this->getMock(
            'Magento\Customer\Api\CustomerRepositoryInterface',
            array(),
            array(),
            '',
            false
        );
        $this->requestMock = $this->getMock('Magento\Framework\App\Request\Http', array(), array(), '', false);
        $this->moduleManagerMock = $this->getMock('Magento\Framework\Module\Manager', array(), array(), '', false);
        $this->viewMock = $this->getMock('Magento\Framework\App\View', array(), array(), '', false);

        $this->currentCustomer = new \Magento\Customer\Helper\Session\CurrentCustomer(
            $this->customerSessionMock,
            $this->layoutMock,
            $this->customerDataBuilderMock,
            $this->customerServiceMock,
            $this->requestMock,
            $this->moduleManagerMock,
            $this->viewMock
        );
    }

    /**
     * test getCustomer method, method returns depersonalized customer Data
     */
    public function testGetCustomerDepersonalizeCustomerData()
    {
        $this->requestMock->expects($this->once())->method('isAjax')->will($this->returnValue(false));
        $this->layoutMock->expects($this->once())->method('isCacheable')->will($this->returnValue(true));
        $this->viewMock->expects($this->once())->method('isLayoutLoaded')->will($this->returnValue(true));
        $this->moduleManagerMock->expects(
            $this->once()
        )->method(
                'isEnabled'
            )->with(
                $this->equalTo('Magento_PageCache')
            )->will(
                $this->returnValue(true)
            );
        $this->customerSessionMock->expects(
            $this->once()
        )->method(
                'getCustomerGroupId'
            )->will(
                $this->returnValue($this->customerGroupId)
            );
        $this->customerDataBuilderMock->expects(
            $this->once()
        )->method(
                'create'
            )->will(
                $this->returnValue($this->customerDataMock)
            );
        $this->customerDataBuilderMock->expects(
            $this->once()
        )->method(
                'setGroupId'
            )->with(
                $this->equalTo($this->customerGroupId)
            )->will(
                $this->returnSelf()
            );
        $this->assertEquals($this->customerDataMock, $this->currentCustomer->getCustomer());
    }

    /**
     * test get customer method, method returns customer from service
     */
    public function testGetCustomerLoadCustomerFromService()
    {
        $this->moduleManagerMock->expects(
            $this->once()
        )->method(
                'isEnabled'
            )->with(
                $this->equalTo('Magento_PageCache')
            )->will(
                $this->returnValue(false)
            );
        $this->customerSessionMock->expects(
            $this->once()
        )->method(
                'getId'
            )->will(
                $this->returnValue($this->customerId)
            );
        $this->customerServiceMock->expects(
            $this->once()
        )->method(
            'getById'
            )->with(
                $this->equalTo($this->customerId)
            )->will(
                $this->returnValue($this->customerDataMock)
            );
        $this->assertEquals($this->customerDataMock, $this->currentCustomer->getCustomer());
    }
}
