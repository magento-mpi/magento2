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

    protected function setUp()
    {
        $this->objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->quoteMock = $this->getMock('Magento\Sales\Model\Quote', [], [], '', false);
        $this->serviceQuote = $this->getMock('\Magento\Sales\Model\Service\Quote', [], [], '', false);
        $this->quoteFactoryMock = $this->getMock(
            '\Magento\Sales\Model\Service\QuoteFactory',
            ['create'],
            [],
            '',
            false
        );
        $this->customerAccountServiceMock = $this->getMock(
            '\Magento\Customer\Service\V1\CustomerAccountServiceInterface',
            [],
            [],
            '',
            false
        );
        $this->addressBuilderFactoryMock = $this->getMockBuilder(
            '\Magento\Customer\Service\V1\Data\AddressBuilderFactory'
        )
            ->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->objectCopyServiceMock = $this->getMockBuilder('\Magento\Framework\Object\Copy')
            ->disableOriginalConstructor()
            ->getMock();
        $this->customerSessionMock = $this->getMockBuilder('\Magento\Customer\Model\Session')
            ->disableOriginalConstructor()
            ->getMock();
        $paypalConfigMock = $this->getMock('Magento\Paypal\Model\Config', [], [], '', false);

        $customerBuilder = $this->getMockBuilder('Magento\Customer\Api\Data\CustomerDataBuilder')
            ->setMethods(['populateWithArray', 'setEmail', 'setPrefix', 'setFirstname', 'setMiddlename', 'setLastname', 'setSuffix', 'create'])
            ->disableOriginalConstructor()
            ->getMock();
        $customerBuilder->expects($this->any())
            ->method('populateWithArray')
            ->will($this->returnSelf());
        $customerData = $this->getMockBuilder('Magento\Customer\Api\Data\CustomerInterface')
            ->getMock();
        $customerBuilder->expects($this->any())
            ->method('create')
            ->will($this->returnValue($customerData));

        $this->checkoutModel = $this->objectManager->getObject(
            'Magento\Paypal\Model\Express\Checkout',
            [
                'params'                 => [
                    'quote'   => $this->quoteMock,
                    'config'  => $paypalConfigMock,
                    'session' => $this->customerSessionMock
                ],
                'customerAccountService' => $this->customerAccountServiceMock,
                'serviceQuoteFactory'    => $this->quoteFactoryMock,
                'addressBuilderFactory'  => $this->addressBuilderFactoryMock,
                'objectCopyService'      => $this->objectCopyServiceMock,
                'customerBuilder'        => $customerBuilder
            ]
        );
        parent::setUp();
    }

    public function testSetCustomerData()
    {
        /** @var \Magento\Customer\Api\Data\CustomerInterface $customerDataMock */
        $customerDataMock = $this->getMock('Magento\Customer\Api\Data\CustomerInterface', [], [], '', false);
        $this->quoteMock->expects($this->once())->method('assignCustomer')->with($customerDataMock);
        $customerDataMock->expects($this->once())->method('getId');
        $this->checkoutModel->setCustomerData($customerDataMock);
    }

    public function testSetCustomerWithAddressChange()
    {
        /** @var \Magento\Customer\Api\Data\CustomerInterface $customerDataMock */
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
        $this->quoteMock->expects($this->once())
            ->method('setCustomerData')
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

        $addressDataBuilderMock = $this->getMockBuilder('\Magento\Customer\Service\V1\Data\AddressBuilder')
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

        $addressDataMock = $this->getMockBuilder('\Magento\Customer\Service\V1\Data\Address')
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
        $addressMock->expects($this->any())
            ->method('exportCustomerAddressData')
            ->willReturn(
                $this->getMockBuilder('\Magento\Customer\Service\V1\Data\Address')->disableOriginalConstructor()
                    ->getMock()
            );

        $customerDataMock = $this->getMock('Magento\Customer\Api\Data\CustomerInterface', [], [], '', false);
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
