<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Pbridge\Model\Payment;

class MethodTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Pbridge\Model\Payment\Method|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_model;

    /**
     * @var \ReflectionProperty
     */
    protected $_allowCurrencyCode;

    /**
     * @var \ReflectionProperty
     */
    protected $_paymentCode;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_config;

    /**
     * @var \Magento\Payment\Helper\Data|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $paymentHelper;

    /**
     * setUp
     */
    protected function setUp()
    {
        $this->_config = $this->getMock('Magento\Framework\App\Config\ScopeConfigInterface');

        $this->_paymentHelper = $this->getMockBuilder(
            'Magento\Payment\Helper\Data'
        )->disableOriginalConstructor()->setMethods(
            array('getMethodInstance')
        )->getMock();

        $this->_model = new \Magento\Pbridge\Model\Payment\Method(
            $this->getMock('Magento\Framework\Event\ManagerInterface', array(), array(), '', false),
            $this->_paymentHelper,
            $this->_config,
            $this->getMock('Magento\Framework\Logger\AdapterFactory', array(), array(), '', false),
            $this->getMock('Magento\Framework\Logger', array(), array(), '', false),
            $this->getMock('Magento\Framework\Module\ModuleListInterface', array(), array(), '', false),
            $this->getMock('Magento\Framework\Stdlib\DateTime\TimezoneInterface', array(), array(), '', false),
            $this->getMock('Magento\Centinel\Model\Service', array(), array(), '', false),
            $this->getMock('Magento\Pbridge\Helper\Data', array(), array(), '', false),
            $this->getMock('Magento\Framework\StoreManagerInterface', array(), array(), '', false),
            'getFormBlockType',
            array()
        );

        $this->_allowCurrencyCode = new \ReflectionProperty(
            'Magento\Pbridge\Model\Payment\Method',
            '_allowCurrencyCode'
        );
        $this->_allowCurrencyCode->setAccessible(true);
        $this->_paymentCode = new \ReflectionProperty('Magento\Payment\Model\Method\AbstractMethod', '_code');
        $this->_paymentCode->setAccessible(true);
    }

    public function testCanUseForCurrency()
    {
        $this->_config->expects($this->any())->method('getValue')->with(
            'payment/code/currency',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            0
        )->will($this->returnValue('BTN'));
        $this->assertTrue($this->_model->canUseForCurrency('UAH'));
        $this->_allowCurrencyCode->setValue($this->_model, array('USD', 'EUR'));
        $this->_model->setData('_accepted_currency', array('USD', 'EUR'));
        $this->assertFalse($this->_model->canUseForCurrency('UAH'));
    }

    public function testGetAcceptedCurrencyCodes()
    {
        $this->_config->expects($this->any())->method('getValue')->with(
            'payment/code/currency',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            0
        )->will($this->returnValue('BTN'));
        $this->_allowCurrencyCode->setValue($this->_model, array('USD', 'EUR'));
        $this->_paymentCode->setValue($this->_model, 'code');
        $this->assertEquals(array('USD', 'EUR', 'BTN'), $this->_model->getAcceptedCurrencyCodes());
        $this->_model->setData('_accepted_currency', array('USD', 'EUR'));
        $this->assertEquals(array('USD', 'EUR'), $this->_model->getAcceptedCurrencyCodes());
    }

    public function testGetIsDummy()
    {
        $this->assertTrue($this->_model->getIsDummy());
    }

    public function testGetPbridgeMethodInstance()
    {

        $this->assertEquals($this->_getPreparePbridgeInstance(), $this->_model->getPbridgeMethodInstance());
    }

    public function testGetOriginalCode()
    {
        $this->_paymentCode->setValue($this->_model, 'code');
        $this->assertEquals('code', $this->_model->getOriginalCode());
    }

    public function testGetFormBlockType()
    {
        $this->assertEquals('getFormBlockType', $this->_model->getFormBlockType());
    }

    public function testGetIsCentinelValidationEnabled()
    {
        $this->assertFalse($this->_model->getIsCentinelValidationEnabled());
    }

    public function testIs3dSecureEnabled()
    {
        $this->_paymentCode->setValue($this->_model, 'code');
        $this->_config->expects($this->any())->method('getValue')->with(
            'payment/code/enable3ds',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            0
        )->will($this->returnValue(1));
        $this->assertTrue($this->_model->is3dSecureEnabled());
    }

    public function testIsDeffered3dCheck()
    {
        $this->assertFalse($this->_model->getIsDeferred3dCheck());
    }

    public function testAuthorize()
    {
        $payment = new \Magento\Framework\Object();
        $amount = 5.99;
        $expectedResponse = ['key' => 'value'];
        $pbridgeMock = $this->_getPreparePbridgeInstance();
        $pbridgeMock->expects($this->once())->method('authorize')->with($payment, $amount)->will(
            $this->returnValue($expectedResponse)
        );
        $this->assertEquals($this->_model, $this->_model->authorize($payment, $amount));
        $this->assertEquals($expectedResponse, $payment->getData());
    }

    public function testCapture()
    {
        $payment = new \Magento\Framework\Object();
        $amount = 5.99;
        $expectedResponse = ['key' => 'value'];
        $pbridgeMock = $this->_getPreparePbridgeInstance();

        $pbridgeMock->expects($this->once())->method('capture')->with($payment, $amount)->will(
            $this->returnValue([])
        );

        $pbridgeMock->expects($this->once())->method('authorize')->with($payment, $amount)->will(
            $this->returnValue($expectedResponse)
        );
        $this->assertEquals($this->_model, $this->_model->capture($payment, $amount));
        $this->assertEquals(array_merge($expectedResponse, ['is_transaction_closed' => 1]), $payment->getData());
    }

    public function testRefund()
    {
        $payment = new \Magento\Framework\Object();
        $amount = 5.99;
        $expectedResponse = ['key' => 'value', 'is_transaction_closed' => 1];
        $pbridgeMock = $this->_getPreparePbridgeInstance();

        $pbridgeMock->expects($this->once())->method('refund')->with($payment, $amount)->will(
            $this->returnValue($expectedResponse)
        );
        $this->assertEquals($this->_model, $this->_model->refund($payment, $amount));
        $this->assertEquals(
            array_merge($expectedResponse, ['should_close_parent_transaction' => 1]),
            $payment->getData()
        );
    }

    public function testVoid()
    {
        $payment = new \Magento\Framework\Object();
        $expectedResponse = ['key' => 'value'];
        $pbridgeMock = $this->_getPreparePbridgeInstance();

        $pbridgeMock->expects($this->once())->method('void')->with($payment)->will(
            $this->returnValue($expectedResponse)
        );
        $this->assertEquals($this->_model, $this->_model->void($payment));
        $this->assertEquals(
            array_merge($expectedResponse, ['is_transaction_closed' => 1]),
            $payment->getData()
        );
    }

    public function testCancel()
    {
        $payment = new \Magento\Framework\Object();
        $expectedResponse = ['key' => 'value'];
        $pbridgeMock = $this->_getPreparePbridgeInstance();

        $pbridgeMock->expects($this->once())->method('void')->with($payment)->will(
            $this->returnValue($expectedResponse)
        );

        /** @var \Magento\Sales\Model\Order|\PHPUnit_Framework_MockObject_MockObject $orderMock */
        $orderMock = $this->getMockBuilder('Magento\Sales\Model\Order')->disableOriginalConstructor()
            ->setMethods([])->getMock();
        $invoiceCollectionMock = $this->getMockBuilder(
            'Magento\Sales\Model\Resource\Order\Invoice\Collection'
        )->disableOriginalConstructor()->setMethods([])->getMock();
        $orderMock->expects($this->once())->method('getInvoiceCollection')->will(
            $this->returnValue($invoiceCollectionMock)
        );
        $invoiceCollectionMock->expects($this->once())->method('count')->will($this->returnValue(0));

        $payment->setOrder($orderMock);

        $this->assertEquals($this->_model, $this->_model->cancel($payment));
        $this->assertEquals(
            array_merge($expectedResponse, ['is_transaction_closed' => 1, 'order' => $orderMock]),
            $payment->getData()
        );
    }

    /**
     * Mocks pbridge method instance
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function _getPreparePbridgeInstance()
    {
        $pbridgeMock = $this->getMockBuilder(
            'Magento\Pbridge\Model\Payment\Method\Pbridge'
        )->disableOriginalConstructor()->setMethods([])->getMock();
        $this->_paymentHelper->expects($this->any())->method('getMethodInstance')->with('pbridge')->will(
            $this->returnValue($pbridgeMock)
        );
        $pbridgeMock->expects($this->once())->method('setOriginalMethodInstance')->with($this->_model);
        return $pbridgeMock;
    }
}
