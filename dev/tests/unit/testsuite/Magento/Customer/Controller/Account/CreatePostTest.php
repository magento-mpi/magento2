<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Controller\Account;

class CreatePostTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Customer\Controller\Account\CreatePost
     */
    protected $object;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $customerSession;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $customerHelperMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $redirectMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $accountServiceMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $response;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $request;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $urlFactoryMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $urlMock;

    protected function setUp()
    {
        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->customerSession = $this->getMock('\Magento\Customer\Model\Session', [], [], '', false);
        $this->customerHelperMock = $this->getMock('\Magento\Customer\Helper\Data', [], [], '', false);
        $this->redirectMock = $this->getMock('Magento\Framework\App\Response\RedirectInterface');
        $this->accountServiceMock = $this->getMock('Magento\Customer\Service\V1\CustomerAccountServiceInterface');
        $this->response = $this->getMock('Magento\Webapi\Controller\Response');
        $this->request = $this->getMockBuilder('Magento\Webapi\Controller\Request')
            ->disableOriginalConstructor()
            ->getMock();
        $this->urlFactoryMock = $this->getMockBuilder('\Magento\Framework\UrlFactory')
            ->disableOriginalConstructor()
            ->getMock();
        $this->urlMock = $this->getMockBuilder('\Magento\Backend\Model\Url')
            ->disableOriginalConstructor()
            ->getMock();
        $this->object = $objectManager->getObject('Magento\Customer\Controller\Account\CreatePost',
            [
                'response' => $this->response,
                'customerSession' => $this->customerSession,
                'customerHelperData' => $this->customerHelperMock,
                'redirect' => $this->redirectMock,
                'customerAccountService' => $this->accountServiceMock,
                'request' => $this->request,
                'urlFactory' => $this->urlFactoryMock,
            ]
        );
    }

    /**
     * @return void
     */
    public function testCreatePostActionRegistrationDisabled()
    {
        $this->customerSession->expects($this->once())
            ->method('isLoggedIn')
            ->will($this->returnValue(false));

        $this->customerHelperMock->expects($this->once())
            ->method('isRegistrationAllowed')
            ->will($this->returnValue(false));

        $this->redirectMock->expects($this->once())
            ->method('redirect')
            ->with($this->response, '*/*/', array())
            ->will($this->returnValue(false));

        $this->accountServiceMock->expects($this->never())
            ->method('createCustomer');

        $this->object->execute();
    }

    public function testRegenerateIdOnExecution()
    {
        $this->customerSession->expects($this->once())
            ->method('regenerateId');
        $this->customerSession->expects($this->once())
            ->method('isLoggedIn')
            ->will($this->returnValue(false));

        $this->customerHelperMock->expects($this->once())
            ->method('isRegistrationAllowed')
            ->will($this->returnValue(true));
        $this->request->expects($this->once())
            ->method('isPost')
            ->will($this->returnValue(true));
        $this->urlFactoryMock->expects($this->once())
            ->method('create')
            ->will($this->returnValue($this->urlMock));
        $this->object->execute();
    }
}
