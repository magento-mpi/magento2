<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Pbridge\Model\Payment\Method;

use Magento\TestFramework\Helper\ObjectManager as ObjectManagerHelper;

class PbridgeTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\Pbridge\Model\Payment\Method\Pbridge */
    protected $pbridge;

    /** @var ObjectManagerHelper */
    protected $objectManagerHelper;

    /** @var \Magento\Framework\Event\ManagerInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $managerInterfaceMock;

    /** @var \Magento\Payment\Helper\Data|\PHPUnit_Framework_MockObject_MockObject */
    protected $paymentHelperMock;

    /** @var \Magento\Framework\App\Config\ScopeConfigInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $scopeConfigInterfaceMock;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    protected $adapterFactoryMock;

    /** @var \Magento\Pbridge\Helper\Data|\PHPUnit_Framework_MockObject_MockObject */
    protected $pbridgeHelperMock;

    /** @var \Magento\Pbridge\Model\Session|\PHPUnit_Framework_MockObject_MockObject */
    protected $sessionMock;

    /** @var \Magento\Framework\UrlInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $urlInterfaceMock;

    /** @var \Magento\Directory\Model\RegionFactory|\PHPUnit_Framework_MockObject_MockObject */
    protected $regionFactoryMock;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    protected $apiFactoryMock;

    /** @var \Magento\Framework\App\RequestInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $requestInterfaceMock;

    /**
     * @var array
     */
    private $_testingDefaultData = [
        'magento_payment_action' => 'payment_action',
        'additional_params' => [],
        'client_ip' => '123.213.123.123',
        'base_currency_code' => '*',
        'order_id' => 1,
        'customer_email' => 'email@email.com',
        'is_virtual' => 1,
        'is_first_capture' => 1,
        'notify_url' => 'domain.com/action',
        'client_identifier' => 'd41d8cd98f00b204e9800998ecf8427e',
        'country' => 'UA',
        'store_id' => 1,
        'cart' => 'cart',
    ];

    /**
     * @var array
     */
    private $_addressInfo = [
        'street' => ['street1', 'street2'],
        'region_id' => null,
        'region' => 'region'
    ];

    /** @var  \Magento\Pbridge\Model\Payment\Method\Pbridge\Api|\PHPUnit_Framework_MockObject_MockObject */
    protected $api;

    protected function setUp()
    {
        $this->managerInterfaceMock = $this->getMock('Magento\Framework\Event\ManagerInterface');
        $this->paymentHelperMock = $this->getMock('Magento\Payment\Helper\Data', [], [], '', false);
        $this->scopeConfigInterfaceMock = $this->getMock('Magento\Framework\App\Config\ScopeConfigInterface');
        $this->adapterFactoryMock = $this->getMock('Magento\Framework\Logger\AdapterFactory');
        $this->pbridgeHelperMock = $this->getMock('Magento\Pbridge\Helper\Data', [], [], '', false);
        $this->sessionMock = $this->getMock('Magento\Pbridge\Model\Session', [], [], '', false);
        $this->urlInterfaceMock = $this->getMock('Magento\Framework\UrlInterface');
        $this->regionFactoryMock = $this->getMock('Magento\Directory\Model\RegionFactory', [], [], '', false);
        $this->apiFactoryMock = $this->getMock('Magento\Pbridge\Model\Payment\Method\Pbridge\ApiFactory');
        $this->requestInterfaceMock = $this->getMock('Magento\Framework\App\Request\Http', [], [], '', false);

        $this->objectManagerHelper = new ObjectManagerHelper($this);
        $this->pbridge = $this->objectManagerHelper->getObject(
            'Magento\Pbridge\Model\Payment\Method\Pbridge',
            [
                'eventManager' => $this->managerInterfaceMock,
                'paymentData' => $this->paymentHelperMock,
                'scopeConfig' => $this->scopeConfigInterfaceMock,
                'logAdapterFactory' => $this->adapterFactoryMock,
                'pbridgeData' => $this->pbridgeHelperMock,
                'pbridgeSession' => $this->sessionMock,
                'url' => $this->urlInterfaceMock,
                'regionFactory' => $this->regionFactoryMock,
                'pbridgeApiFactory' => $this->apiFactoryMock,
                'requestHttp' => $this->requestInterfaceMock
            ]
        );

        $this->api = $this->getMockBuilder(
            'Magento\Pbridge\Model\Payment\Method\Pbridge\Api'
        )->disableOriginalConstructor()->setMethods([])->getMock();
        $this->apiFactoryMock->expects($this->any())->method('create')->will($this->returnValue($this->api));
    }

    public function testAuthorize()
    {
        $payment = new \Magento\Framework\Object();
        $amount = 5.99;
        /** @var \Magento\Sales\Model\Order|\PHPUnit_Framework_MockObject_MockObject $orderMock */
        $orderMock = $this->getMockBuilder('Magento\Sales\Model\Order')->disableOriginalConstructor()
            ->setMethods([])->getMock();
        $payment->setOrder($orderMock);

        $expectedRequest = $this->_getApiRequest();

        // original payment method
        /** @var \Magento\Payment\Model\Method\AbstractMethod|\PHPUnit_Framework_MockObject_MockObject $paymentMethod */
        $paymentMethod = $this->getMockBuilder('Magento\Payment\Model\Method\AbstractMethod')
            ->disableOriginalConstructor()
            ->setMethods([])->getMock();
        $this->pbridge->setOriginalMethodInstance($paymentMethod);
        $paymentInfo = $this->getMockBuilder('Magento\Payment\Model\Info')->disableOriginalConstructor()
            ->setMethods(['getOrder', '__wakeup'])->getMock();
        $paymentMethod->expects($this->any())->method('getInfoInstance')->will($this->returnValue($paymentInfo));
        $paymentInfo->expects($this->any())->method('getOrder')->will($this->returnValue($orderMock));
        $orderMock->expects($this->any())->method('getQuoteId')->will($this->returnValue(null));

        // set params to request object
        $paymentMethod->expects($this->once())->method('getConfigPaymentAction')->will(
            $this->returnValue($this->_testingDefaultData['magento_payment_action'])
        );
        $this->requestInterfaceMock->expects($this->once())->method('getClientIp')->with(false)->will(
            $this->returnValue($this->_testingDefaultData['client_ip'])
        );
        $orderMock->expects($this->any())->method('getBaseCurrencyCode')->will(
            $this->returnValue($this->_testingDefaultData['base_currency_code'])
        );
        $orderMock->expects($this->any())->method('getIncrementId')->will(
            $this->returnValue($this->_testingDefaultData['order_id'])
        );
        $orderMock->expects($this->any())->method('getCustomerEmail')->will(
            $this->returnValue($this->_testingDefaultData['customer_email'])
        );
        $orderMock->expects($this->any())->method('getIsVirtual')->will(
            $this->returnValue($this->_testingDefaultData['is_virtual'])
        );
        $payment->setFirstCaptureFlag($this->_testingDefaultData['is_first_capture']);
        $storeMock = $this->getMockBuilder('Magento\Store\Model\Store')->disableOriginalConstructor()
            ->setMethods(['getStoreId', '__wakeup'])->getMock();
        $storeMock->expects($this->any())->method('getStoreId')->will(
            $this->returnValue($this->_testingDefaultData['store_id'])
        );
        $orderMock->expects($this->any())->method('getStore')->will($this->returnValue($storeMock));
        $this->urlInterfaceMock->expects($this->any())->method('getUrl')->with(
            'magento_pbridge/PbridgeIpn/',
            ['_scope' => $this->_testingDefaultData['store_id']]
        )->will($this->returnValue($this->_testingDefaultData['notify_url']));
        $billingAddress = $this->getMockBuilder('Magento\Sales\Model\Order\Address')->disableOriginalConstructor()
            ->setMethods([])->getMock();
        $orderMock->expects($this->any())->method('getBillingAddress')->will(
            $this->returnValue($billingAddress)
        );
        $payment->setCart($this->_testingDefaultData['cart']);

        $expectedRequest->setData('magento_payment_action', $this->_testingDefaultData['magento_payment_action'])
            ->setData('additional_params', [])
            ->setData('client_ip', $this->_testingDefaultData['client_ip'])
            ->setData('amount', (string)$amount)
            ->setData('currency_code', $this->_testingDefaultData['base_currency_code'])
            ->setData('order_id', $this->_testingDefaultData['order_id'])
            ->setData('customer_email', $this->_testingDefaultData['customer_email'])
            ->setData('is_virtual', $this->_testingDefaultData['is_virtual'])
            ->setData('is_first_capture', $this->_testingDefaultData['is_first_capture'])
            ->setData('notify_url', $this->_testingDefaultData['notify_url'])
            ->setData('billing_address', []);

        $this->api->expects($this->once())->method('doAuthorize')->with($expectedRequest)->will(
            $this->returnValue($this->api)
        );

        $this->pbridge->authorize($payment, $amount);
    }

    public function testCancel()
    {
    }

    public function testCapture()
    {
    }

    public function testRefund()
    {
    }

    public function testVoid()
    {
    }

    /**
     * @return \Magento\Framework\Object
     */
    private function _getApiRequest()
    {
        $request = new \Magento\Framework\Object();
        $request->setCountryCode($this->_testingDefaultData['country']);
        $request->setClientIdentifier($this->_testingDefaultData['client_identifier']);
        $request->setData('additional_params', []);

        $this->scopeConfigInterfaceMock->expects($this->any())->method('getValue')->with(
            Pbridge::XML_CONFIG_PATH_DEFAULT_COUNTRY,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        )->will($this->returnValue($this->_testingDefaultData['country']));
        return $request;

    }

    private function _getAddressInfo()
    {

    }
}
