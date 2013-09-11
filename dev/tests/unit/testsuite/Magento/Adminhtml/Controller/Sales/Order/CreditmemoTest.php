<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Adminhtml_Controller_Sales_Order_CreditmemoTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Adminhtml_Controller_Sales_Order_Creditmemo
     */
    protected $_controller;

    /**
     * @var Magento_Core_Controller_Response_Http|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_responseMock;

    /**
     * @var Magento_Core_Controller_Request_Http|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_requestMock;

    /**
     * @var Magento_Backend_Model_Session|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_sessionMock;

    /**
     * @var Magento_ObjectManager|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_objectManager;

    /**
     * Init model for future tests
     */
    protected function setUp()
    {
        $helper = new Magento_TestFramework_Helper_ObjectManager($this);
        $this->_responseMock = $this->getMock('Magento_Core_Controller_Response_Http',
            array('setRedirect'), array(), '', false
        );
        $this->_responseMock->headersSentThrowsException = false;
        $this->_requestMock = $this->getMock('Magento_Core_Controller_Request_Http', array(), array(), '', false);
        $this->_sessionMock = $this->getMock('Magento_Backend_Model_Session',
            array('addError', 'setFormData'), array(), '', false);
        $this->_objectManager = $this->getMock('Magento_ObjectManager', array(), array(), '', false);
        $registryMock = $this->getMock('Magento_Core_Model_Registry', array(), array(), '', false, false);
        $this->_objectManager->expects($this->any())
            ->method('get')
            ->with($this->equalTo('Magento_Core_Model_Registry'))
            ->will($this->returnValue($registryMock));

        $arguments = array(
            'response' => $this->_responseMock,
            'request' => $this->_requestMock,
            'session' => $this->_sessionMock,
            'objectManager' => $this->_objectManager,
        );

        $context = $helper->getObject('Magento_Backend_Controller_Context', $arguments);

        $this->_controller = $helper->getObject('Magento_Adminhtml_Controller_Sales_Order_Creditmemo',
            array('context' => $context));
    }

    /**
     * Test saveAction when was chosen online refund with refund to store credit
     */
    public function testSaveActionOnlineRefundToStoreCredit()
    {
        $data = array(
            'comment_text' => '',
            'do_offline' => '0',
            'refund_customerbalance_return_enable' => '1'
        );
        $creditmemoId = '1';
        $this->_requestMock->expects($this->once())
            ->method('getPost')->with('creditmemo')->will($this->returnValue($data));
        $this->_requestMock->expects($this->at(1))
            ->method('getParam')->with('creditmemo_id')->will($this->returnValue($creditmemoId));
        $this->_requestMock->expects($this->any())
            ->method('getParam')->will($this->returnValue(null));

        $creditmemoMock = $this->getMock(
            'Magento_Sales_Model_Order_Creditmemo', array('load', 'getGrandTotal'), array(), '', false
        );
        $creditmemoMock->expects($this->once())->method('load')
            ->with($this->equalTo($creditmemoId))->will($this->returnSelf());
        $creditmemoMock->expects($this->once())->method('getGrandTotal')->will($this->returnValue('1'));
        $this->_objectManager->expects($this->once())
            ->method('create')
            ->with($this->equalTo('Magento_Sales_Model_Order_Creditmemo'))
            ->will($this->returnValue($creditmemoMock));

        $this->_setSaveActionExpectationForMageCoreException($data,
            'Cannot create online refund for Refund to Store Credit.'
        );

        $this->_controller->saveAction();
    }

    /**
     * Test saveAction when was credit memo total is not positive
     */
    public function testSaveActionWithNegativeCreditmemo()
    {
        $data = array('comment_text' => '');
        $creditmemoId = '1';
        $this->_requestMock->expects($this->once())
            ->method('getPost')->with('creditmemo')->will($this->returnValue($data));
        $this->_requestMock->expects($this->at(1))
            ->method('getParam')->with('creditmemo_id')->will($this->returnValue($creditmemoId));
        $this->_requestMock->expects($this->any())
            ->method('getParam')->will($this->returnValue(null));

        $creditmemoMock = $this->getMock('Magento_Sales_Model_Order_Creditmemo',
            array('load', 'getGrandTotal', 'getAllowZeroGrandTotal'), array(), '', false);
        $creditmemoMock->expects($this->once())->method('load')
            ->with($this->equalTo($creditmemoId))->will($this->returnSelf());
        $creditmemoMock->expects($this->once())->method('getGrandTotal')->will($this->returnValue('0'));
        $creditmemoMock->expects($this->once())->method('getAllowZeroGrandTotal')->will($this->returnValue(false));
        $this->_objectManager->expects($this->once())
            ->method('create')
            ->with($this->equalTo('Magento_Sales_Model_Order_Creditmemo'))
            ->will($this->returnValue($creditmemoMock));

        $this->_setSaveActionExpectationForMageCoreException($data, 'Credit memo\'s total must be positive.');

        $this->_controller->saveAction();
    }

    /**
     * Set expectations in case of Magento_Core_Exception for saveAction method
     *
     * @param array $data
     * @param string $errorMessage
     */
    protected function _setSaveActionExpectationForMageCoreException($data, $errorMessage)
    {
        $this->_sessionMock->expects($this->once())
            ->method('addError')
            ->with($this->equalTo($errorMessage));
        $this->_sessionMock->expects($this->once())
            ->method('setFormData')
            ->with($this->equalTo($data));

        $this->_responseMock->expects($this->once())
            ->method('setRedirect');
    }
}
