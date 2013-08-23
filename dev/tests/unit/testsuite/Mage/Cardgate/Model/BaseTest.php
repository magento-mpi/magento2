<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Cardgate
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Mage_Cardgate_Model_Base
 */
class Mage_Cardgate_Model_BaseTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Cardgate_Model_Base
     */
    protected $_baseModel;

    /**
     * @var Mage_Core_Model_Store_Config|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_storeConfigMock;

    /**
     * @var Mage_Core_Model_Config|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_configMock;

    /**
     * @var Mage_Core_Model_Dir|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_dirMock;

    /**
     * @var Mage_Core_Model_Logger|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_loggerMock;

    /**
     * @var Mage_Core_Model_Resource_Transaction_Factory|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_transFactoryMock;

    /**
     * @var Mage_Sales_Model_OrderFactory|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_orderFactoryMock;

    /**
     * @var Mage_Cardgate_Helper_Data|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_helperMock;

    /**
     * @var Magento_Filesystem|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_filesystemMock;

    /**
     * Set up model
     * @var array $config
     */
    public function _createModel(array $config = array())
    {
        $this->_storeConfigMock = $this->getMock('Mage_Core_Model_Store_Config', array(), array(), '', false);
        $this->_configMock = $this->getMock('Mage_Core_Model_Config', array(), array(), '', false);
        $this->_dirMock = $this->getMock('Mage_Core_Model_Dir', array(), array(), '', false);
        $this->_loggerMock = $this->getMock('Mage_Core_Model_Logger', array(), array(), '', false);
        $this->_transFactoryMock =
            $this->getMock('Mage_Core_Model_Resource_TransactionFactory', array('create'), array(), '', false);
        $this->_orderFactoryMock = $this->getMock('Mage_Sales_Model_OrderFactory', array('create'), array(), '', false);
        $this->_helperMock = $this->getMock('Mage_Cardgate_Helper_Data', array(), array(), '', false);
        $this->_filesystemMock = $this->getMock('Magento_Filesystem', array(), array(), '', false);

        $this->_storeConfigMock->expects($this->once())->method('getConfig')->with($this->equalTo('payment/cardgate'))
            ->will($this->returnValue($config));
        $this->_dirMock->expects($this->any())->method('getDir')->will($this->returnValue('/dev/null'));
        $this->_loggerMock->expects($this->any())->method('log');
        $this->_helperMock->expects($this->any())->method('__')->will($this->returnArgument(0));

        $this->_baseModel = new Mage_Cardgate_Model_Base(
            $this->_storeConfigMock,
            $this->_configMock,
            $this->_dirMock,
            $this->_loggerMock,
            $this->_transFactoryMock,
            $this->_orderFactoryMock,
            $this->_helperMock,
            $this->_filesystemMock
        );
    }

    /**
     * @test
     */
    public function testProcessCallback()
    {
        $config = array(
            'mail_invoice' => true,
            'debug' => true,
            'complete_status' => 'complete_status',
            'failed_status' => 'failed_status',
            'fraud_status' => 'fraud_status',
            'autocreate_invoice' => true,
        );
        $this->_createModel($config);

        $callbackData = array(
            'ref' => 'ID-1',
            'amount' => '1234',
            'status' => '200',
        );
        $this->_baseModel->setCallbackData($callbackData);

        $order = $this->getMock('Mage_Sales_Model_Order',
            array('getState', 'getStatus', 'getEmailSent', 'loadByIncrementId', 'getBaseTotalDue', 'sendNewOrderEmail',
                'getStatusHistoryCollection', 'addStatusToHistory', 'setState', 'canInvoice', 'getInvoiceCollection',
                'prepareInvoice', 'save'),
            array(), '', false);
        $invoiceCollection = $this->getMock('Mage_Sales_Model_Resource_Order_Invoice_Collection', array(), array(),
            '', false);
        $invoice = $this->getMock('Mage_Sales_Model_Order_Invoice',
            array('getIncrementId', 'save', 'sendEmail', 'getOrder', 'register', 'setRequestedCaptureCase',
                'setEmailSent'),
            array(), '', false);
        $transactionMock = $this->getMock('Mage_Core_Model_Resource_Transaction', array(), array(), '', false);

        $this->_orderFactoryMock->expects($this->once())->method('create')->will($this->returnValue($order));

        $order->expects($this->once())->method('loadByIncrementId')->with($this->equalTo(1))
            ->will($this->returnValue(true));

        $order->expects($this->once())->method('getBaseTotalDue')->will($this->returnValue(12.34));

        $order->expects($this->once())->method('getEmailSent')->will($this->returnValue(false));
        $order->expects($this->once())->method('sendNewOrderEmail');

        $order->expects($this->any())->method('getState')
            ->will($this->returnValue(Mage_Sales_Model_Order::STATE_NEW));
        $order->expects($this->any())->method('getStatus')->will($this->returnValue(''));

        $order->expects($this->once())->method('getStatusHistoryCollection')->will($this->returnValue(array()));

        $order->expects($this->once())->method('canInvoice')->will($this->returnValue(true));
        $order->expects($this->once())->method('getInvoiceCollection')->will($this->returnValue($invoiceCollection));
        $invoiceCollection->expects($this->once())->method('getSize')->will($this->returnValue(false));

        $order->expects($this->once())->method('prepareInvoice')->will($this->returnValue($invoice));
        $invoice->expects($this->exactly(2))->method('save')->will($this->returnSelf());

        $this->_transFactoryMock->expects($this->once())->method('create')
            ->will($this->returnValue($transactionMock));
        $invoice->expects($this->once())->method('getOrder')->will($this->returnValue($order));
        $transactionMock->expects($this->exactly(2))->method('addObject')
            ->with($this->logicalOr($this->equalTo($order), $this->equalTo($invoice)))->will($this->returnSelf());
        $transactionMock->expects($this->once())->method('save')->will($this->returnSelf());

        $invoice->expects($this->once())->method('sendEmail')->will($this->returnSelf());

        $invoice->expects($this->once())->method('getIncrementId')->will($this->returnValue(1));
        $order->expects($this->once())->method('getStatus')->will($this->returnValue(true));
        $order->expects($this->once())->method('addStatusToHistory')
            ->with($this->equalTo(''),
                $this->equalTo('Invoice #%s created and send to customer.'), $this->equalTo(true));

        $order->expects($this->once())->method('setState')
            ->with($this->equalTo(Mage_Sales_Model_Order::STATE_PROCESSING), $this->equalTo('complete_status'),
                $this->equalTo('Payment complete.'));

        $order->expects($this->once())->method('save');

        $this->_baseModel->processCallback();
    }

    /**
     * @test
     */
    public function testProcessCallbackCannotUpdate()
    {
        $config = array(
            'mail_invoice' => true,
            'debug' => true,
            'complete_status' => 'complete_status',
            'failed_status' => 'failed_status',
            'fraud_status' => 'fraud_status',
            'autocreate_invoice' => true,
        );
        $this->_createModel($config);

        $callbackData = array(
            'ref' => 'ID-1',
            'amount' => '1234',
            'status' => '200',
        );
        $this->_baseModel->setCallbackData($callbackData);

        $order = $this->getMock('Mage_Sales_Model_Order',
            array('getState', 'getStatus', 'getEmailSent', 'loadByIncrementId', 'getBaseTotalDue', 'sendNewOrderEmail',
                'getStatusHistoryCollection', 'addStatusToHistory', 'setState', 'canInvoice', 'getInvoiceCollection',
                'prepareInvoice', 'save'),
            array(), '', false);

        $this->_orderFactoryMock->expects($this->once())->method('create')->will($this->returnValue($order));

        $order->expects($this->once())->method('loadByIncrementId')->with($this->equalTo(1))
            ->will($this->returnValue(true));

        $order->expects($this->once())->method('getBaseTotalDue')->will($this->returnValue(12.34));

        $order->expects($this->once())->method('getEmailSent')->will($this->returnValue(false));
        $order->expects($this->once())->method('sendNewOrderEmail');

        $order->expects($this->any())->method('getState')
            ->will($this->returnValue(Mage_Sales_Model_Order::STATE_CLOSED));
        $order->expects($this->any())->method('getStatus')->will($this->returnValue(''));

        $order->expects($this->once())->method('getStatusHistoryCollection')->will($this->returnValue(array()));

        $order->expects($this->never())->method('canInvoice');
        $order->expects($this->never())->method('getInvoiceCollection');
        $order->expects($this->never())->method('prepareInvoice');

        $order->expects($this->never())->method('setState');

        $order->expects($this->once())->method('save');

        $this->_baseModel->processCallback();
    }

    /**
     * @test
     */
    public function testProcessCallbackDifferentAmounts()
    {
        $config = array(
            'mail_invoice' => true,
            'debug' => true,
            'complete_status' => 'complete_status',
            'failed_status' => 'failed_status',
            'fraud_status' => 'fraud_status',
            'autocreate_invoice' => true,
        );
        $this->_createModel($config);

        $callbackData = array(
            'ref' => '1',
            'amount' => '1234',
            'status' => '200',
        );
        $this->_baseModel->setCallbackData($callbackData);

        $order = $this->getMock('Mage_Sales_Model_Order',
            array('getStatus','loadByIncrementId', 'getBaseTotalDue', 'addStatusToHistory', 'prepareInvoice', 'save'),
            array(), '', false);

        $this->_orderFactoryMock->expects($this->once())->method('create')->will($this->returnValue($order));

        $order->expects($this->once())->method('loadByIncrementId')->with($this->equalTo(1))
            ->will($this->returnValue(true));

        $order->expects($this->once())->method('getBaseTotalDue')->will($this->returnValue(12.44));

        $order->expects($this->once())->method('addStatusToHistory')
            ->with(
                $this->equalTo(''),
                $this->equalTo('Hacker attempt: Order total amount does not match CardGatePlus\'s gross total amount!')
            );

        $order->expects($this->once())->method('save');

        $order->expects($this->never())->method('prepareInvoice');

        $this->setExpectedException('RuntimeException', 'Amount validation failed!');

        $this->_baseModel->processCallback();
    }
}
