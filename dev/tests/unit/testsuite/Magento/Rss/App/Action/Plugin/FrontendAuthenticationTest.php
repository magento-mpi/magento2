<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */

namespace Magento\Rss\App\Action\Plugin;

use Magento\TestFramework\Helper\ObjectManager as ObjectManagerHelper;

/**
 * Class FrontendAuthenticationTest
 */
class FrontendAuthenticationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Rss\App\Action\Plugin\FrontendAuthentication
     */
    protected $frontendAuthentication;

    /**
     * @var ObjectManagerHelper
     */
    protected $objectManagerHelper;

    /**
     * @var \Magento\Customer\Model\Session|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $sessionModel;

    /**
     * @var \Magento\Customer\Service\V1\CustomerAccountServiceInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $customerAccountService;

    /**
     * @var \Magento\Customer\Service\V1\Data\Customer|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $customerService;

    /**
     * @var \Magento\Framework\HTTP\Authentication|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $httpAuthentication;

    /**
     * @var \Magento\Framework\App\ResponseInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $responseInterface;

    /**
     * @var \Magento\Framework\App\Action\Action|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $subject;

    /**
     * @var \Closure|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $proceed;

    /**
     * @var \Magento\Framework\App\RequestInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $request;


    protected function setUp()
    {
        $this->subject = $this->getMock('\Magento\Framework\App\Action\Action', [], [], '', false);
        $this->request = $this->getMock('\Magento\Framework\App\RequestInterface', [], [], '', false);
        $this->proceed = function () {
            return true;
        };
        $this->sessionModel = $this->getMock(
            'Magento\Customer\Model\Session',
            [
                '__wakeup',
                'isLoggedIn',
                'setCustomerDataAsLoggedIn',
                'regenerateId'
            ],
            [],
            '',
            false
        );
        $this->customerAccountService = $this->getMock('Magento\Customer\Service\V1\CustomerAccountServiceInterface');
        $this->customerService = $this->getMock('\Magento\Customer\Service\V1\Data\Customer', [], [], '', false);
        $this->responseInterface = $this->getMock('Magento\Framework\App\ResponseInterface');
        $this->httpAuthentication = $this->getMock(
            'Magento\Framework\HTTP\Authentication',
            [
                'getCredentials',
                'setAuthenticationFailed'
            ],
            [],
            '',
            false
        );
        $this->objectManagerHelper = new ObjectManagerHelper($this);
        $this->frontendAuthentication = $this->objectManagerHelper->getObject(
            'Magento\Rss\App\Action\Plugin\FrontendAuthentication',
            [
                'customerSession' => $this->sessionModel,
                'customerAccountService' => $this->customerAccountService,
                'httpAuthentication' => $this->httpAuthentication,
                'response' => $this->responseInterface,
            ]
        );
    }

    public function testAroundDispatchLoggedId()
    {
        $this->sessionModel->expects($this->at(0))->method('isLoggedIn')->will($this->returnValue(false));
        $this->sessionModel->expects($this->at(3))->method('isLoggedIn')->will($this->returnValue(true));
        $this->sessionModel->expects($this->once())->method('setCustomerDataAsLoggedIn')->will($this->returnSelf());
        $this->sessionModel->expects($this->once())->method('regenerateId')->will($this->returnSelf());
        $this->httpAuthentication->expects($this->once())->method('getCredentials')
            ->will($this->returnValue(array('', '')));
        $this->customerAccountService->expects($this->once())->method('authenticate')
            ->will($this->returnValue($this->customerService));
        $result = $this->frontendAuthentication->aroundDispatch($this->subject, $this->proceed, $this->request);
        $this->assertEquals(true, $result);
    }

    public function testAroundDispatchNotLoggedId()
    {
        $this->sessionModel->expects($this->any())->method('isLoggedIn')->will($this->returnValue(false));
        $this->sessionModel->expects($this->once())->method('setCustomerDataAsLoggedIn')->will($this->returnSelf());
        $this->sessionModel->expects($this->once())->method('regenerateId')->will($this->returnSelf());
        $this->httpAuthentication->expects($this->once())->method('setAuthenticationFailed')->will($this->returnSelf());
        $this->httpAuthentication->expects($this->once())->method('getCredentials')
            ->will($this->returnValue(array('', '')));
        $this->customerAccountService->expects($this->once())->method('authenticate')
            ->will($this->returnValue($this->customerService));

        $return = $this->frontendAuthentication->aroundDispatch($this->subject, $this->proceed, $this->request);
        $this->assertEquals($this->responseInterface, $return);
    }
}
