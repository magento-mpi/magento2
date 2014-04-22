<?php
/**
 * Unit test for session \Magento\Customer\Model\Session
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Model;

class SessionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_converterMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_storageMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_eventManagerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_httpContextMock;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_model;

    protected function setUp()
    {
        $this->_converterMock = $this->getMock('Magento\Customer\Model\Converter', [], [], '', false);
        $this->_storageMock = $this->getMock('Magento\Customer\Model\Session\Storage', [], [], '', false);
        $this->_eventManagerMock = $this->getMock('Magento\Event\ManagerInterface', [], [], '', false);
        $this->_httpContextMock = $this->getMock('Magento\Framework\App\Http\Context', [], [], '', false);
        $helper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->_model = $helper->getObject('Magento\Customer\Model\Session',
            [
                'converter' => $this->_converterMock,
                'storage' => $this->_storageMock,
                'eventManager' => $this->_eventManagerMock,
                'httpContext' => $this->_httpContextMock
            ]
        );
    }

    public function testSetCustomerAsLoggedIn()
    {
        $customer = $this->getMock('Magento\Customer\Model\Customer', [], [], '', false);
        $customerDto = $this->getMock('Magento\Customer\Service\V1\Data\Customer', [], [], '', false);
        $this->_converterMock->expects($this->any())
            ->method('createCustomerFromModel')
            ->will($this->returnValue($customerDto));

        $this->_eventManagerMock->expects($this->at(0))
            ->method('dispatch')
            ->with('customer_login', ['customer' => $customer]);
        $this->_eventManagerMock->expects($this->at(1))
            ->method('dispatch')
            ->with('customer_data_object_login', ['customer' => $customerDto]);

        $_SESSION = array();
        $this->_model->setCustomerAsLoggedIn($customer);
        $this->assertSame($customer, $this->_model->getCustomer());
    }

    public function testSetCustomerDataAsLoggedIn()
    {
        $customer = $this->getMock('Magento\Customer\Model\Customer', [], [], '', false);
        $customerDto = $this->getMock('Magento\Customer\Service\V1\Data\Customer', [], [], '', false);

        $this->_converterMock->expects($this->any())
            ->method('createCustomerModel')
            ->will($this->returnValue($customer));

        $this->_eventManagerMock->expects($this->at(0))
            ->method('dispatch')
            ->with('customer_login', ['customer' => $customer]);
        $this->_eventManagerMock->expects($this->at(1))
            ->method('dispatch')
            ->with('customer_data_object_login', ['customer' => $customerDto]);

        $this->_model->setCustomerDataAsLoggedIn($customerDto);
        $this->assertSame($customer, $this->_model->getCustomer());
    }

}
