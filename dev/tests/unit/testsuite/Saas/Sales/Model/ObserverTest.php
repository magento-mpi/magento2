<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */
class Saas_Sales_Model_ObserverTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_requestMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_saasHelper;

    /**
     * @var Saas_Sales_Model_Observer
     */
    protected $_observerMock;

    /**
     * @var Saas_Sales_Model_Observer
     */
    protected $_modelSalesObserver;

    protected function setUp()
    {
        $this->_requestMock = $this->getMock('Mage_Core_Controller_Request_Http', array(), array(), '', false);
        $this->_saasHelper = $this->getMock('Saas_Saas_Helper_Data', array(), array(), '', false);
        $this->_observerMock = $this->getMock('Varien_Event_Observer', array(), array(), '', false);

        $objectManagerHelper = new Magento_Test_Helper_ObjectManager($this);
        $this->_modelSalesObserver = $objectManagerHelper->getObject('Saas_Sales_Model_Observer', array(
            'request' => $this->_requestMock,
            'saasHelper' => $this->_saasHelper,
        ));
    }

    public function testLimitedSalesFunctionality()
    {
        $this->_requestMock->expects($this->any())->method('getControllerModule')
            ->will($this->returnValue('Mage_Adminhtml'));
        $this->_requestMock->expects($this->any())->method('getControllerName')
            ->will($this->returnValue('sales_transactions'));
        $this->_saasHelper->expects($this->once())->method('customizeNoRoutForward');

        $this->_modelSalesObserver->disableAdminhtmlSalesTransactionsController($this->_observerMock);
    }

    /**
     * @param string $module
     * @param string $controller
     * @dataProvider dataProviderForNonLimitedSalesFunctionality
     */
    public function testNonLimitedSalesFunctionality($module, $controller)
    {
        $this->_requestMock->expects($this->any())->method('getControllerModule')->will($this->returnValue($module));
        $this->_requestMock->expects($this->any())->method('getControllerName')->will($this->returnValue($controller));
        $this->_saasHelper->expects($this->never())->method('customizeNoRoutForward');

        $this->_modelSalesObserver->disableAdminhtmlSalesTransactionsController($this->_observerMock);
    }

    /**
     * @return array
     */
    public function dataProviderForNonLimitedSalesFunctionality()
    {
        return array(
            array('Mage_Adminhtml', 'unknown'),
            array('unknown', 'sales_transactions'),
            array('unknown', 'unknown')
        );
    }
}
