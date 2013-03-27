<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */
class Saas_Customer_Model_Customer_Online_ObserverTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_requestMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_observerMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_helperMock;

    /**
     * @var Saas_Customer_Model_Customer_Online_Observer
     */
    protected $_modelCustomerOnlineObserver;

    protected function setUp()
    {
        $this->_requestMock = $this->getMock('Mage_Core_Controller_Request_Http', array(), array(), '', false);
        $this->_observerMock = $this->getMock('Varien_Event_Observer', array(), array(), '', false);
        $this->_helperMock = $this->getMock('Saas_Saas_Helper_Data', array(), array(), '', false);

        $objectManagerHelper = new Magento_Test_Helper_ObjectManager($this);
        $this->_modelCustomerOnlineObserver = $objectManagerHelper
            ->getObject('Saas_Customer_Model_Customer_Online_Observer', array(
            'request' => $this->_requestMock,
            'saasHelper' => $this->_helperMock
        ));
    }

    public function testDisabledCustomerOnlineController()
    {
        $this->_requestMock->expects($this->once())->method('getControllerName')
            ->will($this->returnValue('customer_online'));
        $this->_requestMock->expects($this->once())->method('getControllerModule')
            ->will($this->returnValue('Mage_Adminhtml'));

        $this->_helperMock->expects($this->once())->method('customizeNoRoutForward')->with($this->_requestMock);

        $this->_modelCustomerOnlineObserver->disableCustomerOnlineController($this->_observerMock);
    }

    /**
     * @param string $module
     * @param string $controller
     * @dataProvider dataProviderForNotDisabledCustomerOnlineController
     */
    public function testNotDisabledCustomerOnlineController($module, $controller)
    {
        $this->_requestMock->expects($this->any())->method('getControllerName')->will($this->returnValue($controller));
        $this->_requestMock->expects($this->any())->method('getControllerModule')->will($this->returnValue($module));

        $this->_helperMock->expects($this->never())->method('customizeNoRoutForward');

        $this->_modelCustomerOnlineObserver->disableCustomerOnlineController($this->_observerMock);
    }

    /**
     * @return array
     */
    public function dataProviderForNotDisabledCustomerOnlineController()
    {
        return array(
            array('Mage_Adminhtml', 'unknown'),
            array('unknown', 'customer_online'),
            array('unknown', 'unknown'),
        );
    }
}
