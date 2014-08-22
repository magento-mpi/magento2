<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Checkout\Controller\Onepage;

class SaveBillingTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Partial mock so that we don't have to test inherited methods.
     *
     * @var \Magento\Checkout\Controller\Onepage\SaveBilling | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $saveBilling;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject | \Magento\Framework\App\RequestInterface
     */
    protected $mockRequest;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject | \Magento\Framework\App\ResponseInterface
     */
    protected $mockResponse;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject | \Magento\Framework\ObjectManager
     */
    protected $mockObjectManager;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject | \Magento\Framework\App\Action\Context
     */
    protected $mockContext;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject | \Magento\Customer\Model\Session
     */
    protected $mockSession;

    public function setUp()
    {
        $this->mockContext = $this->getMockBuilder('\Magento\Framework\App\Action\Context')
            ->disableOriginalConstructor()
            ->getMock();

        $this->mockRequest = $this->getMockBuilder('\Magento\Framework\App\RequestInterface')->setMethods(
            ['isPost', 'getPost', 'getModuleName', 'setModuleName', 'getActionName', 'setActionName', 'getParam']
        )
            ->getMock();
        $this->mockContext->expects($this->once())
            ->method('getRequest')
            ->will($this->returnValue($this->mockRequest));

        $this->mockResponse = $this->getMockBuilder('\Magento\Framework\App\ResponseInterface')
            ->setMethods(['representJson', 'sendResponse'])
            ->getMock();
        $this->mockContext->expects($this->once())
            ->method('getResponse')
            ->will($this->returnValue($this->mockResponse));

        $this->mockObjectManager = $this->getMockBuilder('\Magento\Framework\ObjectManager')
            ->setMethods(['get', 'jsonEncode', 'create', 'configure'])
            ->getMock();
        $this->mockContext->expects($this->once())
            ->method('getObjectManager')
            ->will($this->returnValue($this->mockObjectManager));

        $this->mockSession = $this->getMockBuilder('\Magento\Customer\Model\Session')
            ->disableOriginalConstructor()
            ->getMock();

        $this->saveBilling = $this->getMockBuilder('\Magento\Checkout\Controller\Onepage\SaveBilling')
            ->setMethods(
                ['_expireAjax', 'getOnepage', 'saveBilling', 'getQuote', 'isVirtual', '_getPaymentMethodsHtml']
            )
            ->setConstructorArgs(
                [
                    $this->mockContext,
                    $this->mockSession,
                    $this->getMock('\Magento\Customer\Service\V1\CustomerAccountServiceInterface'),
                    $this->getMock('\Magento\Customer\Service\V1\CustomerMetadataServiceInterface'),
                    $this->getMock('\Magento\Framework\Registry'),
                    $this->getMock('\Magento\Framework\Translate\InlineInterface'),
                    $this->getMockBuilder('\Magento\Core\App\Action\FormKeyValidator')->disableOriginalConstructor()
                        ->getMock()
                ]
            )
            ->getMock();
    }

    /**
     * @cover SaveBilling::execute
     */
    public function testExecute()
    {
        $result = ['something', 'fairly', 'unique'];
        $mergedResult = ['something', 'fairly', 'unique', 'goto_section' => 'shipping'];

        $this->mockRequest->expects($this->once())
            ->method('isPost')
            ->will($this->returnValue(true));
        $this->mockRequest->expects($this->any())
            ->method('getPost')
            ->withAnyParameters()
            ->will($this->returnValue([]));

        $represent = 'REPRESENT';
        $this->mockResponse->expects($this->once())
            ->method('representJson')
            ->with($represent);

        $this->mockObjectManager->expects($this->once())
            ->method('get')
            ->will($this->returnSelf());
        $this->mockObjectManager->expects($this->once())
            ->method('jsonEncode')
            ->with($mergedResult)
            ->will($this->returnValue($represent));

        $this->saveBilling->expects($this->once())
            ->method('_expireAjax')
            ->will($this->returnValue(false));
        $this->saveBilling->expects($this->any())
            ->method('getOnepage')
            ->will($this->returnSelf());
        $this->saveBilling->expects($this->once())
            ->method('saveBilling')
            ->withAnyParameters()
            ->will($this->returnValue($result));
        $this->saveBilling->expects($this->once())
            ->method('getQuote')
            ->will($this->returnSelf());
        $this->saveBilling->expects($this->once())
            ->method('isVirtual')
            ->will($this->returnValue(false));

        $this->mockSession->expects($this->once())
            ->method('regenerateId');
        $this->saveBilling->execute();
    }
}
