<?php
/**
 * Test for \Magento\Checkout\Controller\Onepage\Index
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Checkout\Controller\Onepage;

use \Magento\TestFramework\Helper\ObjectManager as ObjectManager;

class IndexTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\TestFramework\Helper\ObjectManager
     */
    private $objectManager;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $objectManagerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $dataMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $quoteMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $contextMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $sessionMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $viewMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $onepageMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $layoutMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $requestMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $responseMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $redirectMock;

    /**
     * @var \Magento\Checkout\Controller\Onepage\Index
     */
    private $model;

    public function setUp()
    {
        // mock objects
        $this->objectManager = new ObjectManager($this);
        $this->objectManagerMock = $this->basicMock('\Magento\Framework\ObjectManager');
        $this->dataMock = $this->basicMock('Magento\Checkout\Helper\Data');
        $this->quoteMock = $this->basicMock('\Magento\Sales\Model\Quote');
        $this->contextMock = $this->basicMock('\Magento\Framework\App\Action\Context');
        $this->sessionMock = $this->basicMock('\Magento\Customer\Model\Session');
        $this->onepageMock = $this->basicMock('\Magento\Checkout\Model\Type\Onepage');
        $this->viewMock = $this->basicMock('\Magento\Framework\App\ViewInterface');
        $this->layoutMock = $this->basicMock('\Magento\Framework\View\Layout');
        $this->requestMock = $this->basicMock('\Magento\Framework\App\RequestInterface');
        $this->responseMock = $this->basicMock('\Magento\Framework\App\ResponseInterface');
        $this->redirectMock = $this->basicMock('\Magento\Framework\App\Response\RedirectInterface');

        // stubs
        $this->basicStub($this->onepageMock, 'getQuote', null, $this->quoteMock);
        $this->basicStub($this->viewMock, 'getLayout', null, $this->layoutMock);
        $this->basicStub($this->layoutMock, 'getBlock', null, $this->basicMock('Magento\Theme\Block\Html\Head'));

        // objectManagerMock
        $objectManagerReturns = [
            ['Magento\Checkout\Helper\Data', $this->dataMock],
            ['Magento\Checkout\Model\Type\Onepage', $this->onepageMock],
            ['Magento\Checkout\Model\Session', $this->basicMock('Magento\Checkout\Model\Session')],
            ['Magento\Customer\Model\Session', $this->basicMock('Magento\Customer\Model\Session')],

        ];
        $this->objectManagerMock->expects($this->any())
            ->method('get')
            ->will($this->returnValueMap($objectManagerReturns));
        $this->basicStub($this->objectManagerMock, 'create', null, $this->basicMock('Magento\Framework\UrlInterface'));
        // context stubs
        $this->basicStub($this->contextMock, 'getObjectManager', null, $this->objectManagerMock);
        $this->basicStub($this->contextMock, 'getView', null, $this->viewMock);
        $this->basicStub($this->contextMock, 'getRequest', null, $this->requestMock);
        $this->basicStub($this->contextMock, 'getResponse', null, $this->responseMock);
        $this->basicStub($this->contextMock, 'getMessageManager', null,
            $this->basicMock('\Magento\Framework\Message\ManagerInterface')
        );
        $this->basicStub($this->contextMock, 'getRedirect', null, $this->redirectMock);


        // SUT
        $this->model = $this->objectManager->getObject('\Magento\Checkout\Controller\Onepage\Index',
            [
                'context' => $this->contextMock,
                'customerSession' => $this->sessionMock,
            ]
        );
    }

    public function testRegenerateSessionIdOnExecute()
    {
        //Stubs to control execution flow
        $this->basicStub($this->dataMock, 'canOnepageCheckout', null, true);
        $this->basicStub($this->quoteMock, 'hasItems', null, true);
        $this->basicStub($this->quoteMock, 'getHasError', null, false);
        $this->basicStub($this->quoteMock, 'validateMinimumAmount', null, true);

        //Expected outcomes
        $this->sessionMock->expects($this->once())
            ->method('regenerateId');
        $this->viewMock->expects($this->once())
            ->method('renderLayout');

        $this->model->execute();
    }

    public function testOnepageCheckoutNotAvailable()
    {

        $this->basicStub($this->dataMock, 'canOnepageCheckout', null, false);

        $expectedPath = 'checkout/cart';
        $this->redirectMock->expects($this->once())
            ->method('redirect')
            ->with($this->responseMock, $expectedPath, []);

        $this->model->execute();
    }

    public function testInvalidQuote()
    {
        $this->basicStub($this->quoteMock, 'hasError', null, true);

        $expectedPath = 'checkout/cart';
        $this->redirectMock->expects($this->once())
            ->method('redirect')
            ->with($this->responseMock, $expectedPath, []);

        $this->model->execute();
    }

    /**
     * @param \PHPUnit_Framework_MockObject_MockObject $mock
     * @param string $method
     * @param mixed $param
     * @param mixed|null $return
     */
    private function basicStub($mock, $method, $param, $return = null)
    {
        if(isset($param)) {
            $mock->expects($this->any())
                ->method($method)
                ->with($param)
                ->will($this->returnValue($return));
        } else {
            $mock->expects($this->any())
                ->method($method)
                ->withAnyParameters()
                ->will($this->returnValue($return));
        }
    }

    /**
     * @param string $className
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    public function basicMock($className)
    {
        return $this->getMockBuilder($className)
            ->disableOriginalConstructor()
            ->getMock();
    }
}
