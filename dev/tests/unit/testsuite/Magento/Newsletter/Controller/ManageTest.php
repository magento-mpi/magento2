<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Newsletter\Controller;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ManageTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Newsletter\Controller\Manage
     */
    private $_controller;

    /**
     * @var \Magento\App\RequestInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $_requestMock;

    /**
     * @var \Magento\App\ResponseInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $_responseMock;

    /**
     * @var \Magento\Message\ManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $_messageManagerMock;

    /**
     * @var \Magento\App\Response\RedirectInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $_redirectMock;

    /**
     * @var \Magento\Customer\Model\Session|\PHPUnit_Framework_MockObject_MockObject
     */
    private $_customerSessionMock;

    /**
     * @var \Magento\Core\App\Action\FormKeyValidator|\PHPUnit_Framework_MockObject_MockObject
     */
    private $_formKeyValidatorMock;

    /**
     * @var \Magento\Customer\Service\V1\CustomerAccountServiceInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $_customerAccountServiceMock;

    /**
     * @var \Magento\Customer\Service\V1\Data\CustomerDetailsBuilder|\PHPUnit_Framework_MockObject_MockObject
     */
    private $_customerDetailsBuilderMock;

    /**
     * @var \Magento\Customer\Service\V1\Data\CustomerBuilder|\PHPUnit_Framework_MockObject_MockObject
     */
    private $_customerBuilderMock;

    /**
     * @var \Magento\Newsletter\Model\SubscriberFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $_subscriberFactoryMock;

    protected function setUp()
    {
        $this->_requestMock = $this->getMockBuilder('Magento\App\RequestInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $this->_responseMock = $this->getMockBuilder('Magento\App\ResponseInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $this->_messageManagerMock = $this->getMockBuilder('Magento\Message\ManagerInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $this->_redirectMock = $this->getMockBuilder('Magento\App\Response\RedirectInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $this->_customerSessionMock = $this->getMockBuilder('Magento\Customer\Model\Session')
            ->disableOriginalConstructor()
            ->getMock();
        $this->_customerSessionMock->expects($this->any())
            ->method('isLoggedIn')
            ->will($this->returnValue(true));
        $this->_formKeyValidatorMock = $this->getMockBuilder('Magento\Core\App\Action\FormKeyValidator')
            ->disableOriginalConstructor()
            ->getMock();
        $this->_customerAccountServiceMock =
            $this->getMockBuilder('Magento\Customer\Service\V1\CustomerAccountServiceInterface')
                ->disableOriginalConstructor()
                ->getMock();
        $this->_customerDetailsBuilderMock =
            $this->getMockBuilder('Magento\Customer\Service\V1\Data\CustomerDetailsBuilder')
                ->disableOriginalConstructor()
                ->getMock();
        $this->_customerBuilderMock =
            $this->getMockBuilder('Magento\Customer\Service\V1\Data\CustomerBuilder')
                ->disableOriginalConstructor()
                ->getMock();
        $this->_subscriberFactoryMock =
            $this->getMockBuilder('Magento\Newsletter\Model\SubscriberFactory')
                ->disableOriginalConstructor()
                ->setMethods(['create'])
                ->getMock();
        $storeManagerMock = $this->getMockBuilder('Magento\Core\Model\StoreManagerInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $storeMock = $this->getMockBuilder('Magento\Core\Model\Store')
            ->disableOriginalConstructor()
            ->getMock();
        $storeManagerMock->expects($this->any())
            ->method('getStore')
            ->will($this->returnValue($storeMock));
        $storeMock->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(1));
        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);

        $this->_controller = $objectManager->getObject('Magento\Newsletter\Controller\Manage', [
                'request' => $this->_requestMock,
                'response' => $this->_responseMock,
                'messageManager' => $this->_messageManagerMock,
                'redirect' => $this->_redirectMock,
                'customerSession' => $this->_customerSessionMock,
                'formKeyValidator' => $this->_formKeyValidatorMock,
                'customerAccountService' => $this->_customerAccountServiceMock,
                'customerDetailsBuilder' => $this->_customerDetailsBuilderMock,
                'customerBuilder' => $this->_customerBuilderMock,
                'subscriberFactory' => $this->_subscriberFactoryMock,
                'storeManager' => $storeManagerMock
            ]);
    }

    public function testSaveActionInvalidFormKey()
    {
        $this->_formKeyValidatorMock->expects($this->once())
            ->method('validate')
            ->will($this->returnValue(false));
        $this->_redirectMock->expects($this->once())
            ->method('redirect')
            ->with($this->_responseMock, 'customer/account/', []);
        $this->_messageManagerMock->expects($this->never())
            ->method('addSuccess');
        $this->_messageManagerMock->expects($this->never())
            ->method('addError');
        $this->_controller->saveAction();
    }

    public function testSaveActionNoCustomerInSession()
    {
        $this->_formKeyValidatorMock->expects($this->once())
            ->method('validate')
            ->will($this->returnValue(true));
        $this->_customerSessionMock->expects($this->any())
            ->method('getCustomerId')
            ->will($this->returnValue(null));
        $this->_redirectMock->expects($this->once())
            ->method('redirect')
            ->with($this->_responseMock, 'customer/account/', []);
        $this->_messageManagerMock->expects($this->never())
            ->method('addSuccess');
        $this->_messageManagerMock->expects($this->once())
            ->method('addError')
            ->with('Something went wrong while saving your subscription.');
        $this->_controller->saveAction();
    }

    public function testSaveActionSaveSubscription()
    {
        $this->_formKeyValidatorMock->expects($this->once())
            ->method('validate')
            ->will($this->returnValue(true));
        $this->_customerSessionMock->expects($this->any())
            ->method('getCustomerId')
            ->will($this->returnValue(1));
        $customerDetailsMock = $this->getMockBuilder('Magento\Customer\Service\V1\Data\CustomerDetails')
            ->disableOriginalConstructor()
            ->getMock();
        $customerMock = $this->getMockBuilder('Magento\Customer\Service\V1\Data\Customer')
            ->disableOriginalConstructor()
            ->getMock();
        $customerDetailsMock->expects($this->any())
            ->method('getCustomer')
            ->will($this->returnValue($customerMock));
        $this->_customerAccountServiceMock->expects($this->any())
            ->method('getCustomerDetails')
            ->will($this->returnValue($customerDetailsMock));
        $this->_customerAccountServiceMock->expects($this->once())
            ->method('updateCustomer');
        $this->_customerBuilderMock->expects($this->once())
            ->method('setStoreId')
            ->with(1)
            ->will($this->returnSelf());
        $this->_customerBuilderMock->expects($this->once())
            ->method('create')
            ->will($this->returnValue($customerMock));
        $this->_customerDetailsBuilderMock->expects($this->any())
            ->method('populate')
            ->will($this->returnSelf());
        $this->_customerDetailsBuilderMock->expects($this->any())
            ->method('setCustomer')
            ->will($this->returnSelf());
        $this->_customerDetailsBuilderMock->expects($this->once())
            ->method('create')
            ->will($this->returnValue($customerDetailsMock));
        $this->_requestMock->expects($this->any())
            ->method('getParam')
            ->with('is_subscribed', false)
            ->will($this->returnValue(true));
        $subscriberMock = $this->getMockBuilder('Magento\Newsletter\Model\Subscriber')
            ->disableOriginalConstructor()
            ->getMock();
        $this->_subscriberFactoryMock->expects($this->once())
            ->method('create')
            ->will($this->returnValue($subscriberMock));
        $subscriberMock->expects($this->once())
            ->method('subscribeCustomerById')
            ->with(1);
        $this->_redirectMock->expects($this->once())
            ->method('redirect')
            ->with($this->_responseMock, 'customer/account/', []);
        $this->_messageManagerMock->expects($this->once())
            ->method('addSuccess')
            ->with('We saved the subscription.');
        $this->_messageManagerMock->expects($this->never())
            ->method('addError');
        $this->_controller->saveAction();
    }

    public function testSaveActionRemoveSubscription()
    {
        $this->_formKeyValidatorMock->expects($this->once())
            ->method('validate')
            ->will($this->returnValue(true));
        $this->_customerSessionMock->expects($this->any())
            ->method('getCustomerId')
            ->will($this->returnValue(1));
        $customerDetailsMock = $this->getMockBuilder('Magento\Customer\Service\V1\Data\CustomerDetails')
            ->disableOriginalConstructor()
            ->getMock();
        $customerMock = $this->getMockBuilder('Magento\Customer\Service\V1\Data\Customer')
            ->disableOriginalConstructor()
            ->getMock();
        $customerDetailsMock->expects($this->any())
            ->method('getCustomer')
            ->will($this->returnValue($customerMock));
        $this->_customerAccountServiceMock->expects($this->any())
            ->method('getCustomerDetails')
            ->will($this->returnValue($customerDetailsMock));
        $this->_customerAccountServiceMock->expects($this->once())
            ->method('updateCustomer');
        $this->_customerBuilderMock->expects($this->once())
            ->method('setStoreId')
            ->with(1)
            ->will($this->returnSelf());
        $this->_customerBuilderMock->expects($this->once())
            ->method('create')
            ->will($this->returnValue($customerMock));
        $this->_customerDetailsBuilderMock->expects($this->any())
            ->method('populate')
            ->will($this->returnSelf());
        $this->_customerDetailsBuilderMock->expects($this->any())
            ->method('setCustomer')
            ->will($this->returnSelf());
        $this->_customerDetailsBuilderMock->expects($this->once())
            ->method('create')
            ->will($this->returnValue($customerDetailsMock));
        $this->_requestMock->expects($this->any())
            ->method('getParam')
            ->with('is_subscribed', false)
            ->will($this->returnValue(false));
        $subscriberMock = $this->getMockBuilder('Magento\Newsletter\Model\Subscriber')
            ->disableOriginalConstructor()
            ->getMock();
        $this->_subscriberFactoryMock->expects($this->once())
            ->method('create')
            ->will($this->returnValue($subscriberMock));
        $subscriberMock->expects($this->once())
            ->method('unsubscribeCustomerById')
            ->with(1);
        $this->_redirectMock->expects($this->once())
            ->method('redirect')
            ->with($this->_responseMock, 'customer/account/', []);
        $this->_messageManagerMock->expects($this->once())
            ->method('addSuccess')
            ->with('We removed the subscription.');
        $this->_messageManagerMock->expects($this->never())
            ->method('addError');
        $this->_controller->saveAction();
    }

    public function testSaveActionWithException()
    {
        $this->_formKeyValidatorMock->expects($this->once())
            ->method('validate')
            ->will($this->returnValue(true));
        $this->_customerSessionMock->expects($this->any())
            ->method('getCustomerId')
            ->will($this->returnValue(1));
        $this->_customerAccountServiceMock->expects($this->any())
            ->method('getCustomerDetails')
            ->will($this->throwException(new \Magento\Exception\NoSuchEntityException('customerId', 'value')));
        $this->_redirectMock->expects($this->once())
            ->method('redirect')
            ->with($this->_responseMock, 'customer/account/', []);
        $this->_messageManagerMock->expects($this->never())
            ->method('addSuccess');
        $this->_messageManagerMock->expects($this->once())
            ->method('addError')
            ->with('Something went wrong while saving your subscription.');
        $this->_controller->saveAction();
    }
}
