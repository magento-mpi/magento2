<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Checkout\Model\Type;

use Magento\TestFramework\Helper\ObjectManager as ObjectManagerHelper;

class OnepageTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\Checkout\Model\Type\Onepage */
    protected $onepage;

    /** @var ObjectManagerHelper */
    protected $objectManagerHelper;

    /** @var \Magento\Event\ManagerInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $eventManagerMock;

    /** @var \Magento\Checkout\Helper\Data|\PHPUnit_Framework_MockObject_MockObject */
    protected $checkoutHelperMock;

    /** @var \Magento\Customer\Helper\Data|\PHPUnit_Framework_MockObject_MockObject */
    protected $customerHelperMock;

    /** @var \Magento\Logger|\PHPUnit_Framework_MockObject_MockObject */
    protected $loggerMock;

    /** @var \Magento\Checkout\Model\Session|\PHPUnit_Framework_MockObject_MockObject */
    protected $checkoutSessionMock;

    /** @var \Magento\Customer\Model\Session|\PHPUnit_Framework_MockObject_MockObject */
    protected $customerSessionMock;

    /** @var \Magento\Store\Model\StoreManagerInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $storeManagerMock;

    /** @var \Magento\App\RequestInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $requestMock;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    protected $addressFactoryMock;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    protected $formFactoryMock;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    protected $customerFactoryMock;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    protected $quoteFactoryMock;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    protected $orderFactoryMock;

    /** @var \Magento\Object\Copy|\PHPUnit_Framework_MockObject_MockObject */
    protected $copyMock;

    /** @var \Magento\Message\ManagerInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $messageManagerMock;

    /** @var \Magento\Customer\Model\Metadata\FormFactory|\PHPUnit_Framework_MockObject_MockObject */
    protected $customerFormFactoryMock;

    /** @var \Magento\Customer\Service\V1\Data\CustomerBuilder|\PHPUnit_Framework_MockObject_MockObject */
    protected $customerBuilderMock;

    /** @var \Magento\Customer\Service\V1\Data\AddressBuilder|\PHPUnit_Framework_MockObject_MockObject */
    protected $addressBuilderMock;

    /** @var \Magento\Math\Random|\PHPUnit_Framework_MockObject_MockObject */
    protected $randomMock;

    /** @var \Magento\Encryption\EncryptorInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $encryptorMock;

    /** @var \Magento\Customer\Service\V1\CustomerAddressServiceInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $customerAddressServiceMock;

    /** @var \Magento\Customer\Service\V1\CustomerAccountServiceInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $customerAccountServiceMock;

    protected function setUp()
    {
        $this->eventManagerMock = $this->getMock('Magento\Event\ManagerInterface');
        $this->checkoutHelperMock = $this->getMock('Magento\Checkout\Helper\Data', [], [], '', false);
        $this->customerHelperMock = $this->getMock('Magento\Customer\Helper\Data', [], [], '', false);
        $this->loggerMock = $this->getMock('Magento\Logger', [], [], '', false);
        $this->checkoutSessionMock = $this->getMock('Magento\Checkout\Model\Session', [], [], '', false);
        $this->customerSessionMock = $this->getMock('Magento\Customer\Model\Session', [], [], '', false);
        $this->storeManagerMock = $this->getMock('Magento\Store\Model\StoreManagerInterface');
        $this->requestMock = $this->getMock('Magento\App\RequestInterface');
        $this->addressFactoryMock = $this->getMock('Magento\Customer\Model\AddressFactory');
        $this->formFactoryMock = $this->getMock('Magento\Customer\Model\FormFactory');
        $this->customerFactoryMock = $this->getMock('Magento\Customer\Model\CustomerFactory');
        $this->quoteFactoryMock = $this->getMock('Magento\Sales\Model\Service\QuoteFactory');
        $this->orderFactoryMock = $this->getMock('Magento\Sales\Model\OrderFactory');
        $this->copyMock = $this->getMock('Magento\Object\Copy', [], [], '', false);
        $this->messageManagerMock = $this->getMock('Magento\Message\ManagerInterface');

        $this->customerFormFactoryMock = $this->getMock(
            'Magento\Customer\Model\Metadata\FormFactory',
            [],
            [],
            '',
            false
        );

        $this->customerBuilderMock = $this->getMock(
            'Magento\Customer\Service\V1\Data\CustomerBuilder',
            [],
            [],
            '',
            false
        );

        $this->addressBuilderMock = $this->getMock(
            'Magento\Customer\Service\V1\Data\AddressBuilder',
            [],
            [],
            '',
            false
        );

        $this->randomMock = $this->getMock('Magento\Math\Random');
        $this->encryptorMock = $this->getMock('Magento\Encryption\EncryptorInterface');

        $this->customerAddressServiceMock = $this->getMock(
            'Magento\Customer\Service\V1\CustomerAddressServiceInterface'
        );

        $this->customerAccountServiceMock = $this->getMock(
            'Magento\Customer\Service\V1\CustomerAccountServiceInterface'
        );

        $this->objectManagerHelper = new ObjectManagerHelper($this);
        $this->onepage = $this->objectManagerHelper->getObject(
            'Magento\Checkout\Model\Type\Onepage',
            [
                'eventManager' => $this->eventManagerMock,
                'helper' => $this->checkoutHelperMock,
                'customerData' => $this->customerHelperMock,
                'logger' => $this->loggerMock,
                'checkoutSession' => $this->checkoutSessionMock,
                'customerSession' => $this->customerSessionMock,
                'storeManager' => $this->storeManagerMock,
                'request' => $this->requestMock,
                'customrAddrFactory' => $this->addressFactoryMock,
                'customerFormFactory' => $this->formFactoryMock,
                'customerFactory' => $this->customerFactoryMock,
                'serviceQuoteFactory' => $this->quoteFactoryMock,
                'orderFactory' => $this->orderFactoryMock,
                'objectCopyService' => $this->copyMock,
                'messageManager' => $this->messageManagerMock,
                'formFactory' => $this->customerFormFactoryMock,
                'customerBuilder' => $this->customerBuilderMock,
                'addressBuilder' => $this->addressBuilderMock,
                'mathRandom' => $this->randomMock,
                'encryptor' => $this->encryptorMock,
                'customerAddressService' => $this->customerAddressServiceMock,
                'accountService' => $this->customerAccountServiceMock
            ]
        );
    }

    public function testGetQuote()
    {
        $returnValue = 'ababagalamaga';
        $this->checkoutSessionMock->expects($this->once())->method('getQuote')->will($this->returnValue($returnValue));
        $this->assertEquals($returnValue, $this->onepage->getQuote());
    }

    public function testSetQuote()
    {
        /** @var \Magento\Sales\Model\Quote $quoteMock */
        $quoteMock = $this->getMock('Magento\Sales\Model\Quote', [], [], '', false);
        $this->onepage->setQuote($quoteMock);
        $this->assertEquals($quoteMock, $this->onepage->getQuote());
    }

    /**
     * @dataProvider initCheckoutDataProvider
     */
    public function testInitCheckout($stepData, $isLoggedIn, $isSetStepDataCalled)
    {
        $customer = 'customer';
        /** @var \Magento\Sales\Model\Quote|\PHPUnit_Framework_MockObject_MockObject $quoteMock */
        $quoteMock = $this->getMock('Magento\Sales\Model\Quote', [], [], '', false);
        $quoteMock->expects($this->once())->method('isMultipleShippingAddresses')->will($this->returnValue(true));
        $quoteMock->expects($this->once())->method('removeAllAddresses');
        $quoteMock->expects($this->once())->method('save');
        $quoteMock->expects($this->once())->method('assignCustomer')->with($customer);

        $this->customerSessionMock
            ->expects($this->once())
            ->method('getCustomerDataObject')
            ->will($this->returnValue($customer));
        $this->customerSessionMock->expects($this->any())->method('isLoggedIn')->will($this->returnValue($isLoggedIn));

        $this->checkoutSessionMock->expects($this->once())->method('getQuote')->will($this->returnValue($quoteMock));
        $this->checkoutSessionMock->expects($this->any())->method('getStepData')->will($this->returnValue($stepData));

        if ($isSetStepDataCalled) {
            $this->checkoutSessionMock
                ->expects($this->once())
                ->method('setStepData')
                ->with(key($stepData), 'allow', false);
        } else {
            $this->checkoutSessionMock->expects($this->never())->method('setStepData');
        }

        $this->onepage->initCheckout();
    }

    public function initCheckoutDataProvider()
    {
        return [
            [['login' => ''], false, false],
            [['someStep' => ''], true, true],
            [['billing' => ''], true, false],
        ];
    }

    /**
     * @dataProvider getCheckoutMethodDataProvider
     */
    public function testGetCheckoutMethod($isLoggedIn, $quoteCheckoutMethod, $isAllowedGuestCheckout, $expected)
    {
        $this->customerSessionMock->expects($this->once())->method('isLoggedIn')->will($this->returnValue($isLoggedIn));
        /** @var \Magento\Sales\Model\Quote|\PHPUnit_Framework_MockObject_MockObject $quoteMock */
        $quoteMock = $this->getMock('Magento\Sales\Model\Quote', [], [], '', false);
        $quoteMock->expects($this->any())->method('setCheckoutMethod')->with($expected);

        $quoteMock
            ->expects($this->any())
            ->method('getCheckoutMethod')
            ->will($this->returnValue($quoteCheckoutMethod));

        $this->checkoutHelperMock
            ->expects($this->any())
            ->method('isAllowedGuestCheckout')
            ->will($this->returnValue($isAllowedGuestCheckout));

        $this->onepage->setQuote($quoteMock);
        $this->assertEquals($expected, $this->onepage->getCheckoutMethod());
    }

    public function getCheckoutMethodDataProvider()
    {
        return [
            // isLoggedIn(), getQuote()->getCheckoutMethod(), isAllowedGuestCheckout(), expected
            [true, null, false, Onepage::METHOD_CUSTOMER],
            [false, 'something else', false, 'something else'],
            [false, Onepage::METHOD_GUEST, true, Onepage::METHOD_GUEST],
            [false, Onepage::METHOD_REGISTER, false, Onepage::METHOD_REGISTER],
        ];
    }

    public function testSaveCheckoutMethod()
    {
        $this->assertEquals(['error' => -1, 'message' => 'Invalid data'], $this->onepage->saveCheckoutMethod(null));
        /** @var \Magento\Sales\Model\Quote|\PHPUnit_Framework_MockObject_MockObject $quoteMock */
        $quoteMock = $this->getMock(
            'Magento\Sales\Model\Quote',
            ['setCheckoutMethod', 'save', '__wakeup'],
            [],
            '',
            false
        );
        $quoteMock->expects($this->once())->method('save');
        $quoteMock->expects($this->once())->method('setCheckoutMethod')->with('someMethod')->will($this->returnSelf());
        $this->checkoutSessionMock->expects($this->once())->method('setStepData')->with('billing', 'allow', true);
        $this->onepage->setQuote($quoteMock);
        $this->assertEquals([], $this->onepage->saveCheckoutMethod('someMethod'));
    }

    public function testSaveBilling()
    {
    }

    public function testSaveShipping()
    {
    }

    public function testSaveShippingMethod()
    {
    }

    public function testSavePayment()
    {
    }

    public function testSaveOrder()
    {
    }

    public function testGetLastOrderId()
    {
    }
}
