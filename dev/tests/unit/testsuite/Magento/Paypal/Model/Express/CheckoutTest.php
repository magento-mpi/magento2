<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Paypal\Model\Express;

class CheckoutTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Paypal\Model\Express\Checkout | \Magento\Paypal\Model\Express\Checkout
     */
    protected $checkoutModel;

    /**
     * @var \Magento\TestFramework\Helper\ObjectManager
     */
    protected $objectManager;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject | \'Magento\Sales\Model\Quote
     */
    protected $quoteMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject | \\Magento\Sales\Model\Service\Quote
     */
    protected $serviceQuote;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject | \\Magento\Sales\Model\Service\QuoteFactory
     */
    protected $quoteFactoryMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject | \\Magento\Customer\Service\V1\CustomerAccountServiceInterface
     */
    protected $customerAccountServiceMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject | \\Magento\Customer\Service\V1\Data\AddressBuilderFactory
     */
    protected $addressBuilderFactoryMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject | \\Magento\Framework\Object\Copy
     */
    protected $objectCopyServiceMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject | \\Magento\Customer\Model\Session
     */
    protected $customerSessionMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $customerBuilderMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Customer\Model\Customer
     */
    protected $customerMock;

    protected function setUp()
    {
        $this->objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->customerMock = $this->getMock('Magento\Customer\Model\Customer', [], [], '', false);
        $this->quoteMock = $this->getMock('Magento\Sales\Model\Quote',
            [
                'getId', 'assignCustomer', 'assignCustomerWithAddressChange', 'getBillingAddress',
                'getShippingAddress', 'isVirtual', 'addCustomerAddressData', 'collectTotals', '__wakeup',
                'save', 'getCustomerData'
            ], [], '', false);
        $this->serviceQuote = $this->getMock('\Magento\Sales\Model\Service\Quote', [], [], '', false);
        $this->quoteFactoryMock = $this->getMock(
            '\Magento\Sales\Model\Service\QuoteFactory',
            ['create'],
            [],
            '',
            false
        );
        $this->customerAccountServiceMock = $this->getMock(
            '\Magento\Customer\Model\AccountManagement',
            [],
            [],
            '',
            false
        );
        $this->addressBuilderFactoryMock = $this->getMockBuilder(
            '\Magento\Customer\Api\Data\AddressInterfaceBuilderFactory'
        )
            ->setMethods(['create', 'populate'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->customerBuilderMock =  $this->getMock(
            'Magento\Customer\Api\Data\CustomerInterfaceBuilder',
            [
                'populateWithArray', 'setEmail', 'create', 'setPrefix', 'setFirstname', 'setMiddlename',
                'setLastname', 'setSuffix'
            ], [], '', false
        );
        $this->objectCopyServiceMock = $this->getMockBuilder('\Magento\Framework\Object\Copy')
            ->disableOriginalConstructor()
            ->getMock();
        $this->customerSessionMock = $this->getMockBuilder('\Magento\Customer\Model\Session')
            ->disableOriginalConstructor()
            ->getMock();
        $paypalConfigMock = $this->getMock('Magento\Paypal\Model\Config', [], [], '', false);
        $this->checkoutModel = $this->objectManager->getObject(
            'Magento\Paypal\Model\Express\Checkout',
            [
                'params'                 => [
                    'quote'   => $this->quoteMock,
                    'config'  => $paypalConfigMock,
                    'session' => $this->customerSessionMock
                ],
                'accountManagement'     => $this->customerAccountServiceMock,
                'serviceQuoteFactory'   => $this->quoteFactoryMock,
                'addressBuilderFactory' => $this->addressBuilderFactoryMock,
                'objectCopyService'     => $this->objectCopyServiceMock,
                'customerBuilder'       => $this->customerBuilderMock
            ]
        );
        parent::setUp();
    }

    public function testSetCustomerData()
    {
        $customerDataMock = $this->getMock('Magento\Customer\Api\Data\CustomerInterface', [], [], '', false);
        $this->quoteMock->expects($this->once())->method('assignCustomer')->with($customerDataMock);
        $customerDataMock->expects($this->once())
            ->method('getId');
        $this->checkoutModel->setCustomerData($customerDataMock);
    }

    public function testSetCustomerWithAddressChange()
    {
        /** @var \Magento\Customer\Service\V1\Data\Customer $customerDataMock */
        $customerDataMock = $this->getMock('Magento\Customer\Api\Data\CustomerInterface', [], [], '', false);
        /** @var \Magento\Sales\Model\Quote\Address $customerDataMock */
        $quoteAddressMock = $this->getMock('Magento\Sales\Model\Quote\Address', [], [], '', false);
        $this->quoteMock
            ->expects($this->once())
            ->method('assignCustomerWithAddressChange')
            ->with($customerDataMock, $quoteAddressMock, $quoteAddressMock);
        $customerDataMock->expects($this->once())->method('getId');
        $this->checkoutModel->setCustomerWithAddressChange($customerDataMock, $quoteAddressMock, $quoteAddressMock);
    }

    public function testPrepareNewCustomerQuote()
    {
        $this->quoteMock->expects($this->any())
            ->method('getCheckoutMethod')
            ->willReturn(\Magento\Checkout\Model\Type\Onepage::METHOD_REGISTER);
        $this->quoteMock->expects($this->any())
            ->method('setCustomerData')
            ->willReturnSelf();
        $this->quoteMock->expects($this->any())
            ->method('addCustomerAddressData')
            ->willReturnSelf();


        $this->quoteFactoryMock->expects($this->once())
            ->method('create')
            ->withAnyParameters()
            ->willReturn($this->serviceQuote);

        $this->objectCopyServiceMock->expects($this->once())
            ->method('getDataFromFieldset')
            ->withAnyParameters()
            ->willReturn([]);

        $this->customerSessionMock->expects($this->once())
            ->method('regenerateId');

        $addressDataBuilderMock = $this->getMockBuilder('\Magento\Customer\Api\Data\AddressInterfaceBuilder')
            ->setMethods(
                ['setDefaultBilling', 'populate', 'setDefaultShipping', 'create', 'populateWithArray',
                    'getBillingAddress'
                ]
            )
            ->disableOriginalConstructor()
            ->getMock();
        $this->addressBuilderFactoryMock->expects($this->any())
            ->method('create')
            ->willReturn($addressDataBuilderMock);
        $addressDataBuilderMock->expects($this->any())
            ->method('populate')
            ->withAnyParameters()
            ->willReturnSelf();
        $addressDataBuilderMock->expects($this->any())
            ->method('setDefaultShipping')
            ->withAnyParameters()
            ->willReturnSelf();
        $addressDataBuilderMock->expects($this->any())
            ->method('setDefaultBilling')
            ->withAnyParameters()
            ->willReturnSelf();
        $this->customerBuilderMock->expects($this->once())
            ->method('populateWithArray')
            ->will($this->returnSelf());
        $addressDataMock = $this->getMockBuilder('\Magento\Customer\Api\Data\AddressInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $addressDataBuilderMock->expects($this->any())
            ->method('create')
            ->willReturn($addressDataMock);

        $addressMock = $this->getMock('\Magento\Sales\Model\Quote\Address', [], [], '', false);
        $this->quoteMock->expects($this->any())
            ->method('getBillingAddress')
            ->willReturn($addressMock);
        $this->quoteMock->expects($this->any())
            ->method('getShippingAddress')
            ->willReturn($addressMock);
        $this->quoteMock->expects($this->exactly(2))
            ->method('addCustomerAddressData')
            ->willReturn($addressMock);

        $addressMock->expects($this->any())
            ->method('exportCustomerAddressData')
            ->willReturn(
                $this->getMockBuilder('\Magento\Customer\Api\Data\AddressInterface')->disableOriginalConstructor()
                    ->getMock()
            );

        $customerDataMock = $this->getMockForAbstractClass('Magento\Customer\Api\Data\CustomerInterface',
            [], '', false, false, false, ['getId']);
        $this->customerAccountServiceMock->expects($this->any())
            ->method('getCustomer')
            ->willReturn($customerDataMock);

        $this->quoteMock->expects($this->any())
            ->method('getCustomerData')
            ->willReturn($customerDataMock);
        $this->checkoutModel->setCustomerData($customerDataMock);
        $this->checkoutModel->place('token');
    }
}
