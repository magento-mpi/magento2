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
    private $controller;

    /**
     * @var \Magento\App\RequestInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $requestMock;

    /**
     * @var \Magento\App\ResponseInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $responseMock;

    /**
     * @var \Magento\Message\ManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $messageManagerMock;

    /**
     * @var \Magento\App\Response\RedirectInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $redirectMock;

    /**
     * @var \Magento\Customer\Model\Session|\PHPUnit_Framework_MockObject_MockObject
     */
    private $customerSessionMock;

    /**
     * @var \Magento\Core\App\Action\FormKeyValidator|\PHPUnit_Framework_MockObject_MockObject
     */
    private $formKeyValidatorMock;

    /**
     * @var \Magento\Customer\Service\V1\CustomerAccountServiceInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $customerAccountServiceMock;

    protected function setUp()
    {
        $this->requestMock = $this->getMockBuilder('Magento\App\RequestInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $this->responseMock = $this->getMockBuilder('Magento\App\ResponseInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $this->messageManagerMock = $this->getMockBuilder('Magento\Message\ManagerInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $this->redirectMock = $this->getMockBuilder('Magento\App\Response\RedirectInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $this->customerSessionMock = $this->getMockBuilder('Magento\Customer\Model\Session')
            ->disableOriginalConstructor()
            ->getMock();
        $this->customerSessionMock->expects($this->any())
            ->method('isLoggedIn')
            ->will($this->returnValue(true));
        $this->formKeyValidatorMock = $this->getMockBuilder('Magento\Core\App\Action\FormKeyValidator')
            ->disableOriginalConstructor()
            ->getMock();
        $this->customerAccountServiceMock =
            $this->getMockBuilder('Magento\Customer\Service\V1\CustomerAccountServiceInterface')
                ->disableOriginalConstructor()
                ->getMock();
        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);

        $this->controller = $objectManager->getObject('Magento\Newsletter\Controller\Manage', [
                'request' => $this->requestMock,
                'response' => $this->responseMock,
                'messageManager' => $this->messageManagerMock,
                'redirect' => $this->redirectMock,
                'customerSession' => $this->customerSessionMock,
                'formKeyValidator' => $this->formKeyValidatorMock,
                'customerAccountService' => $this->customerAccountServiceMock
            ]);
    }

    public function testSaveActionInvalidFormKey()
    {
        $this->formKeyValidatorMock->expects($this->once())
            ->method('validate')
            ->will($this->returnValue(false));
        $this->redirectMock->expects($this->once())
            ->method('redirect')
            ->with($this->responseMock, 'customer/account/', []);
        $this->messageManagerMock->expects($this->never())
            ->method('addSuccess');
        $this->messageManagerMock->expects($this->never())
            ->method('addError');
        $this->controller->saveAction();
    }

    public function testSaveActionNoCustomerInSession()
    {
        $this->formKeyValidatorMock->expects($this->once())
            ->method('validate')
            ->will($this->returnValue(true));
        $this->customerSessionMock->expects($this->any())
            ->method('getCustomerId')
            ->will($this->returnValue(null));
        $this->redirectMock->expects($this->once())
            ->method('redirect')
            ->with($this->responseMock, 'customer/account/', []);
        $this->messageManagerMock->expects($this->never())
            ->method('addSuccess');
        $this->messageManagerMock->expects($this->once())
            ->method('addError')
            ->with('Something went wrong while saving your subscription.');
        $this->controller->saveAction();
    }

    public function testSaveActionWithException()
    {
        $this->formKeyValidatorMock->expects($this->once())
            ->method('validate')
            ->will($this->returnValue(true));
        $this->customerSessionMock->expects($this->any())
            ->method('getCustomerId')
            ->will($this->returnValue(1));
        $this->customerAccountServiceMock->expects($this->any())
            ->method('getCustomer')
            ->will($this->throwException(new \Magento\Exception\NoSuchEntityException('customerId', 'value')));
        $this->redirectMock->expects($this->once())
            ->method('redirect')
            ->with($this->responseMock, 'customer/account/', []);
        $this->messageManagerMock->expects($this->never())
            ->method('addSuccess');
        $this->messageManagerMock->expects($this->once())
            ->method('addError')
            ->with('Something went wrong while saving your subscription.');
        $this->controller->saveAction();
    }
}
