<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for \Magento\Sales\Helper\Guest
 */
namespace Magento\Sales\Helper;

class GuestTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Sales\Helper\Guest
     */
    private $helper;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject | \Magento\Customer\Model\Session
     */
    private $mockCustomerSession;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject | \Magento\UrlInterface
     */
    private $mockUrlInterface;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject | \Magento\Sales\Model\OrderFactory
     */
    private $mockOrderFactory;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject | \Magento\Stdlib\Cookie
     */
    private $mockCookie;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject | \Magento\Registry
     */
    private $mockRegistry;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject | \Magento\App\ViewInterface
     */
    private $mockView;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject | \Magento\Store\Model\StoreManagerInterface
     */
    private $mockStoreManager;

    public function setUp()
    {
        $this->mockCustomerSession = $this->getMockBuilder('\Magento\Customer\Model\Session')
            ->disableOriginalConstructor()
            ->getMock();

        $this->mockUrlInterface = $this->getMockBuilder('\Magento\UrlInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $this->mockOrderFactory = $this->getMockBuilder('\Magento\Sales\Model\OrderFactory')
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();

        $this->mockCookie = $this->getMockBuilder('\Magento\Stdlib\Cookie')
            ->disableOriginalConstructor()
            ->getMock();

        $this->mockRegistry = $this->getMockBuilder('\Magento\Registry')
            ->disableOriginalConstructor()
            ->getMock();

        $this->mockView = $this->getMockBuilder('\Magento\App\ViewInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $this->mockStoreManager = $this->getMockBuilder('\Magento\Store\Model\StoreManagerInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $objectManagerHelper = new \Magento\TestFramework\Helper\ObjectManager($this);

        /** @var \Magento\App\Helper\Context $context */
        $context = $objectManagerHelper->getObject(
            '\Magento\App\Helper\Context',
            ['urlBuilder' => $this->mockUrlInterface]
        );

        $this->helper = $objectManagerHelper->getObject(
            'Magento\Sales\Helper\Guest',
            [
                'context'         => $context,
                'customerSession' => $this->mockCustomerSession,
                'orderFactory'    => $this->mockOrderFactory,
                'coreCookie'      => $this->mockCookie,
                'coreRegistry'    => $this->mockRegistry,
                'view'            => $this->mockView,
                'storeManager'    => $this->mockStoreManager
            ]
        );
    }

    public function testLoadValidOrderLoggedIn()
    {
        $this->mockCustomerSession->expects($this->once())
            ->method('isLoggedIn')
            ->will($this->returnValue(true));

        $this->assertFalse(
            $this->helper->loadValidOrder(
                $this->getMock('\Magento\App\RequestInterface'),
                $this->getMockResponse('sales/order/history')
            )
        );
    }

    public function testLoadValidOrderEmptyPostNoCookie()
    {
        $this->mockCustomerSession->expects($this->once())
            ->method('isLoggedIn')
            ->will($this->returnValue(false));

        $this->mockCookie->expects($this->once())
            ->method('get')
            ->with('guest-view')
            ->will($this->returnValue(false));

        $this->assertFalse(
            $this->helper->loadValidOrder($this->getMockRequest([]), $this->getMockResponse('sales/guest/form'))
        );
    }

    public function testLoadValidOrderBasicError()
    {
        $this->mockCustomerSession->expects($this->once())
            ->method('isLoggedIn')
            ->will($this->returnValue(false));

        // getId can not be retrieved
        $this->getMockOrder(['getId']);

        $this->mockCookie->expects($this->once())
            ->method('get')
            ->with('guest-view')
            ->will($this->returnValue(false));

        $this->assertFalse(
            $this->helper->loadValidOrder(
                $this->getMockRequest(['not empty']),
                $this->getMockResponse('sales/guest/form')
            )
        );
    }

    public function testLoadValidOrderPostSet()
    {
        $this->mockCustomerSession->expects($this->once())
            ->method('isLoggedIn')
            ->will($this->returnValue(false));

        $mockOrder = $this->getMockOrder(['getId', 'loadByIncrementId']);
        $mockOrder->expects($this->once())
            ->method('loadByIncrementId')
            ->with(54321);

        $this->assertFalse(
            $this->helper->loadValidOrder(
                $this->getMockRequest(
                    [
                        'oar_order_id'         => 54321,
                        'oar_type'             => 'email',
                        'oar_billing_lastname' => 'lastname',
                        'oar_email'            => 'email@example.com',
                        'oar_zip'              => 12345
                    ]
                ),
                $this->getMockResponse('sales/guest/form')
            )
        );
    }

    public function testLoadValidOrderMissingIdPost()
    {
        $this->mockCustomerSession->expects($this->once())
            ->method('isLoggedIn')
            ->will($this->returnValue(false));

        $this->getMockOrder(['getId']);

        $this->assertFalse(
            $this->helper->loadValidOrder(
                $this->getMockRequest(
                    [
                        'oar_order_id'         => 1,
                        'oar_type'             => 'type',
                        'oar_billing_lastname' => 'lastname',
                        'oar_email'            => 'email@example.com',
                        'oar_zip'              => 12345
                    ]
                ),
                $this->getMockResponse('sales/guest/form')
            )
        );
    }

    public function testLoadValidOrderPostMismatch()
    {
        $this->mockCustomerSession->expects($this->once())
            ->method('isLoggedIn')
            ->will($this->returnValue(false));

        $mockBillingAddress = $this->getMockBuilder('\Magento\Sales\Model\Order\Address')
            ->setMethods(['__wakeup', 'getLastname'])
            ->disableOriginalConstructor()
            ->getMock();

        $mockBillingAddress->expects($this->once())
            ->method('getLastname')
            ->will($this->returnValue('a different last name'));

        $mockOrder = $this->getMockOrder(['getId', 'getBillingAddress']);
        $mockOrder->expects($this->once())
            ->method('getId')
            ->will($this->returnValue(2));
        $mockOrder->expects($this->once())
            ->method('getBillingAddress')
            ->will($this->returnValue($mockBillingAddress));

        $this->assertFalse(
            $this->helper->loadValidOrder(
                $this->getMockRequest(
                    [
                        'oar_order_id'         => 1,
                        'oar_type'             => 'type',
                        'oar_billing_lastname' => 'lastname',
                        'oar_email'            => 'email@example.com',
                        'oar_zip'              => 12345
                    ]
                ),
                $this->getMockResponse('sales/guest/form')
            )
        );
    }

    public function testLoadValidOrderSetCookie()
    {
        $this->mockCustomerSession->expects($this->once())
            ->method('isLoggedIn')
            ->will($this->returnValue(false));

        $mockBillingAddress = $this->getMockBuilder('\Magento\Sales\Model\Order\Address')
            ->setMethods(['__wakeup', 'getLastname', 'getEmail'])
            ->disableOriginalConstructor()
            ->getMock();
        $mockBillingAddress->expects($this->once())
            ->method('getLastname')
            ->will($this->returnValue('lastName'));
        $mockBillingAddress->expects($this->once())
            ->method('getEmail')
            ->will($this->returnValue('email@example.com'));

        $mockOrder = $this->getMockOrder(['getId', 'loadByIncrementId', 'getProtectCode', 'getBillingAddress']);
        $mockOrder->expects($this->once())
            ->method('loadByIncrementId')
            ->with(1);
        $mockOrder->expects($this->exactly(2))
            ->method('getId')
            ->will($this->returnValue(1));
        $mockOrder->expects($this->once())
            ->method('getProtectCode')
            ->will($this->returnValue('protected code'));
        $mockOrder->expects($this->once())
            ->method('getBillingAddress')
            ->will($this->returnValue($mockBillingAddress));

        $this->mockCookie->expects($this->once())
            ->method('set')
            ->with('guest-view', base64_encode('protected code'), 600, '/');

        $this->assertTrue(
            $this->helper->loadValidOrder(
                $this->getMockRequest(
                    [
                        'oar_order_id'         => 1,
                        'oar_type'             => 'email',
                        'oar_billing_lastname' => 'lastname',
                        'oar_email'            => 'email@example.com',
                        'oar_zip'              => 12345
                    ]
                ),
                $this->getMockResponse()
            )
        );
    }

    public function testLoadValidOrderRegisterOrder()
    {
        $this->mockCustomerSession->expects($this->once())
            ->method('isLoggedIn')
            ->will($this->returnValue(false));

        $mockOrder = $this->getMockOrder(['getId']);
        $mockOrder->expects($this->once())
            ->method('getId')
            ->will($this->returnValue('order_id'));

        $this->mockCookie->expects($this->once())
            ->method('get')
            ->with('guest-view')
            ->will($this->returnValue(false));

        $this->mockRegistry->expects($this->once())
            ->method('register')
            ->with('current_order', $mockOrder);

        $this->assertTrue(
            $this->helper->loadValidOrder($this->getMockRequest(['not empty']), $this->getMockResponse())
        );
    }

    public function testGetBreadCrumbs()
    {
        $mockLayout = $this->getMockBuilder('\Magento\View\LayoutInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $this->mockView->expects($this->once())
            ->method('getLayout')
            ->will($this->returnValue($mockLayout));

        $mockBlock = $this->getMockBuilder('Element\BlockInterface')
            ->disableOriginalConstructor()
            ->setMethods(['addCrumb'])
            ->getMock();

        $mockLayout->expects($this->once())
            ->method('getBlock')
            ->with('breadcrumbs')
            ->will($this->returnValue($mockBlock));

        $mockStore = $this->getMockBuilder('\Magento\Store\Model\Store')
            ->disableOriginalConstructor()
            ->getMock();

        $this->mockStoreManager->expects($this->once())
            ->method('getStore')
            ->will($this->returnValue($mockStore));

        $mockStore->expects($this->once())
            ->method('getBaseUrl')
            ->will($this->returnValue('http://base.url'));

        $mockBlock->expects($this->at(0))
            ->method('addCrumb')
            ->with(
                'home',
                [
                    'label' => __('Home'),
                    'title' => __('Go to Home Page'),
                    'link'  => 'http://base.url'
                ]
            );

        $mockBlock->expects($this->at(1))
            ->method('addCrumb')
            ->with(
                'cms_page',
                ['label' => __('Order Information'), 'title' => __('Order Information')]
            );

        $this->helper->getBreadcrumbs();
    }

    /**
     * @param array $methods
     * @return \PHPUnit_Framework_MockObject_MockObject | \Magento\Sales\Model\Order
     */
    private function getMockOrder(array $methods)
    {
        $mockOrder = $this->getMockBuilder('\Magento\Sales\Model\Order')
            ->setMethods(array_merge(['__wakeup'], $methods))
            ->disableOriginalConstructor()
            ->getMock();

        $this->mockOrderFactory->expects($this->once())
            ->method('create')
            ->will($this->returnValue($mockOrder));

        return $mockOrder;
    }

    /**
     * @param null $redirectName
     * @return \PHPUnit_Framework_MockObject_MockObject | \Magento\App\ResponseInterface
     */
    private function getMockResponse($redirectName = null)
    {
        $mockResponse = $this->getMockBuilder('\Magento\App\ResponseInterface')
            ->disableOriginalConstructor()
            ->setMethods(['setRedirect', 'sendResponse'])
            ->getMock();

        if (!is_null($redirectName)) {
            $url = 'http://some.url';
            $this->mockUrlInterface->expects($this->once())
                ->method('getUrl')
                ->with($redirectName)
                ->will($this->returnValue($url));
            $mockResponse->expects($this->once())
                ->method('setRedirect')
                ->with($url);
        }
        return $mockResponse;
    }

    /**
     * @param array $post
     * @return \Magento\App\RequestInterface
     */
    private function getMockRequest(array $post)
    {
        $mockRequest = $this->getMockBuilder('\Magento\App\RequestInterface')
            ->setMethods(['getPost', 'getModuleName', 'setModuleName', 'getActionName', 'setActionName', 'getParam'])
            ->getMock();
        $mockRequest->expects($this->once())
            ->method('getPost')
            ->will($this->returnValue($post));

        return $mockRequest;
    }
}
