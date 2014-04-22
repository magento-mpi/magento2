<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Helper;

use Magento\TestFramework\Helper\ObjectManager as ObjectManagerHelper;

/**
 * Class GuestTest
 * @package Magento\Sales\Helper
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class GuestTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\Sales\Helper\Guest */
    protected $guest;

    /** @var ObjectManagerHelper */
    protected $objectManagerHelper;

    /** @var \Magento\Framework\App\Helper\Context|\PHPUnit_Framework_MockObject_MockObject */
    protected $appContextHelperMock;

    /** @var \Magento\Framework\App\Config\ScopeConfigInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $scopeConfigInterfaceMock;

    /** @var \Magento\Store\Model\StoreManagerInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $storeManagerInterfaceMock;

    /** @var \Magento\Framework\App\State|\PHPUnit_Framework_MockObject_MockObject */
    protected $stateMock;

    /** @var \Magento\Registry|\PHPUnit_Framework_MockObject_MockObject */
    protected $registryMock;

    /** @var \Magento\Customer\Model\Session|\PHPUnit_Framework_MockObject_MockObject */
    protected $sessionMock;

    /** @var \Magento\Framework\Stdlib\Cookie|\PHPUnit_Framework_MockObject_MockObject */
    protected $cookieMock;

    /** @var \Magento\Framework\Message\ManagerInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $managerInterfaceMock;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    protected $orderFactoryMock;

    /** @var \Magento\Framework\App\ViewInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $viewInterfaceMock;

    protected function setUp()
    {
        $this->appContextHelperMock = $this->getMock('Magento\Framework\App\Helper\Context', [], [], '', false);
        $this->scopeConfigInterfaceMock = $this->getMock('Magento\Framework\App\Config\ScopeConfigInterface');
        $this->storeManagerInterfaceMock = $this->getMock('Magento\Store\Model\StoreManagerInterface');
        $this->stateMock = $this->getMock('Magento\Framework\App\State', [], [], '', false);
        $this->registryMock = $this->getMock('Magento\Registry');
        $this->sessionMock = $this->getMock('Magento\Customer\Model\Session', [], [], '', false);
        $this->cookieMock = $this->getMock('Magento\Framework\Stdlib\Cookie', [], [], '', false);
        $this->managerInterfaceMock = $this->getMock('Magento\Framework\Message\ManagerInterface');
        $this->orderFactoryMock = $this->getMock('Magento\Sales\Model\OrderFactory', ['create'], [], '', false);
        $this->viewInterfaceMock = $this->getMock('Magento\Framework\App\ViewInterface');

        $this->objectManagerHelper = new ObjectManagerHelper($this);
        $this->guest = $this->objectManagerHelper->getObject(
            'Magento\Sales\Helper\Guest',
            [
                'context' => $this->appContextHelperMock,
                'scopeConfig' => $this->scopeConfigInterfaceMock,
                'storeManager' => $this->storeManagerInterfaceMock,
                'appState' => $this->stateMock,
                'coreRegistry' => $this->registryMock,
                'customerSession' => $this->sessionMock,
                'coreCookie' => $this->cookieMock,
                'messageManager' => $this->managerInterfaceMock,
                'orderFactory' => $this->orderFactoryMock,
                'view' => $this->viewInterfaceMock
            ]
        );
    }

    public function testLoadValidOrderNotEmptyPost()
    {
        $this->sessionMock->expects($this->once())->method('isLoggedIn')->will($this->returnValue(false));

        $post = [
            'oar_order_id' => 1,
            'oar_type' => 'email',
            'oar_billing_lastname' => 'oar_billing_lastname',
            'oar_email' => 'oar_email',
            'oar_zip' => 'oar_zip'

        ];
        $incrementId = $post['oar_order_id'];
        $requestMock = $this->getMock('Magento\Framework\App\Request\Http', [], [], '', false);
        $requestMock->expects($this->once())->method('getPost')->will($this->returnValue($post));

        $orderMock = $this->getMock(
            'Magento\Sales\Model\Order',
            ['getProtectCode', 'loadByIncrementId', 'getId', 'getBillingAddress', '__wakeup'],
            [],
            '',
            false
        );
        $this->orderFactoryMock->expects($this->once())->method('create')->will($this->returnValue($orderMock));
        $orderMock->expects($this->once())->method('loadByIncrementId')->with($incrementId);
        $orderMock->expects($this->exactly(2))->method('getId')->will($this->returnValue($incrementId));

        $billingAddressMock = $this->getMock(
            'Magento\Sales\Model\Order\Address',
            ['getLastname', 'getEmail', '__wakeup'],
            [],
            '',
            false
        );
        $billingAddressMock->expects($this->once())->method('getLastname')->will(
            $this->returnValue($post['oar_billing_lastname'])
        );
        $billingAddressMock->expects($this->once())->method('getEmail')->will(
            $this->returnValue($post['oar_email'])
        );
        $orderMock->expects($this->once())->method('getBillingAddress')->will($this->returnValue($billingAddressMock));
        $protectedCode = 'protectedCode';
        $orderMock->expects($this->once())->method('getProtectCode')->will($this->returnValue($protectedCode));
        $this->cookieMock->expects($this->once())->method('set')->with(
            Guest::COOKIE_NAME,
            base64_encode($protectedCode . ':' . $incrementId),
            Guest::COOKIE_LIFETIME,
            Guest::COOKIE_PATH
        );
        $responseMock = $this->getMock('Magento\Framework\App\Response\Http', [], [], '', false);
        $this->assertTrue($this->guest->loadValidOrder($requestMock, $responseMock));
    }

    public function testLoadValidOrderStoredCookie()
    {
        $this->sessionMock->expects($this->once())->method('isLoggedIn')->will($this->returnValue(false));
        $orderMock = $this->getMock(
            'Magento\Sales\Model\Order',
            ['getProtectCode', 'loadByIncrementId', 'getId', 'getBillingAddress', '__wakeup'],
            [],
            '',
            false
        );
        $protectedCode = 'protectedCode';
        $incrementId = 1;
        $cookieData = $protectedCode . ':' . $incrementId;
        $cookieDataHash = base64_encode($cookieData);
        $this->orderFactoryMock->expects($this->once())->method('create')->will($this->returnValue($orderMock));

        $this->cookieMock->expects($this->exactly(3))->method('get')->with(Guest::COOKIE_NAME)->will(
            $this->returnValue($cookieDataHash)
        );
        $orderMock->expects($this->once())->method('loadByIncrementId')->with($incrementId);
        $orderMock->expects($this->exactly(1))->method('getId')->will($this->returnValue($incrementId));
        $orderMock->expects($this->once())->method('getProtectCode')->will($this->returnValue($protectedCode));
        $this->cookieMock->expects($this->once())->method('renew')->with(
            Guest::COOKIE_NAME,
            Guest::COOKIE_LIFETIME,
            Guest::COOKIE_PATH
        );

        $requestMock = $this->getMock('Magento\Framework\App\Request\Http', [], [], '', false);
        $responseMock = $this->getMock('Magento\Framework\App\Response\Http', [], [], '', false);
        $this->assertTrue($this->guest->loadValidOrder($requestMock, $responseMock));
    }
}
