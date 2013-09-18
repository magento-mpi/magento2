<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

class Magento_Sales_Model_Observer_Backend_BillingAgreementTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Sales\Model\Observer\Backend\BillingAgreement
     */
    protected $_model;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_authorizationMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_observerMock;

    protected function setUp()
    {
        $this->_authorizationMock = $this->getMock('Magento\AuthorizationInterface');
        $this->_observerMock = $this->getMock('Magento\Event\Observer', array(), array(), '', false);
        $this->_model = new \Magento\Sales\Model\Observer\Backend\BillingAgreement(
            $this->_authorizationMock
        );
    }

    public function testDispatchIfMethodInterfaceNotAgreement()
    {
        $event = $this->getMock('Magento\Event', array('getMethodInstance'), array(), '', false);
        $this->_observerMock->expects($this->once())->method('getEvent')->will($this->returnValue($event));
        $event->expects($this->once())->method('getMethodInstance')->will($this->returnValue('some incorrect value'));
        $event->expects($this->never())->method('isAvailable');
        $this->_model->dispatch($this->_observerMock);
    }

    public function testDispatchIfMethodInterfaceAgreement()
    {
        $event = $this->getMock('Magento\Event', array('getMethodInstance', 'getResult'), array(), '', false);
        $this->_observerMock->expects($this->once())->method('getEvent')->will($this->returnValue($event));
        $methodInstance = $this->getMock('Magento\Paypal\Model\Method\Agreement', array(), array(), '', false);
        $event->expects($this->once())->method('getMethodInstance')->will($this->returnValue($methodInstance));
        $this->_authorizationMock->expects(
            $this->once())->method('isAllowed')->with('Magento_Sales::use')->will($this->returnValue(false)
        );
        $result = new StdClass();
        $event->expects($this->once())->method('getResult')->will($this->returnValue($result));
        $this->_model->dispatch($this->_observerMock);
        $this->assertFalse($result->isAvailable);
    }
}
