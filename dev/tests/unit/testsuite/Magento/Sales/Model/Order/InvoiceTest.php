<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Sales_Model_Order_InvoiceTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Sales_Model_Order_Invoice
     */
    protected $_model;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject|Magento_Sales_Model_Order
     */
    protected $_orderMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject|Magento_Sales_Model_Order_Payment
     */
    protected $_paymentMock;

    protected function setUp()
    {
        $helperManager = new Magento_TestFramework_Helper_ObjectManager($this);
        $this->_orderMock = $this->getMockBuilder('Magento_Sales_Model_Order')
            ->disableOriginalConstructor()
            ->setMethods(array('getPayment'))
            ->getMock();
        $this->_paymentMock = $this->getMockBuilder('Magento_Sales_Model_Order_Payment')
            ->disableOriginalConstructor()
            ->setMethods(array('canVoid'))
            ->getMock();

        $arguments = array(
            'orderFactory' => $this->getMock(
                'Magento_Sales_Model_OrderFactory', array(), array(), '', false
            ),
            'orderResourceFactory' => $this->getMock(
                'Magento_Sales_Model_Resource_OrderFactory', array(), array(), '', false
            ),
            'calculatorFactory' => $this->getMock(
                'Magento_Core_Model_CalculatorFactory', array(), array(), '', false
            ),
            'invoiceItemCollFactory' => $this->getMock(
                'Magento_Sales_Model_Resource_Order_Invoice_Item_CollectionFactory', array(), array(), '', false
            ),
            'invoiceCommentFactory' => $this->getMock(
                'Magento_Sales_Model_Order_Invoice_CommentFactory', array(), array(), '', false
            ),
            'commentCollFactory' => $this->getMock(
                'Magento_Sales_Model_Resource_Order_Invoice_Comment_CollectionFactory', array(), array(), '', false
            ),
            'templateMailerFactory' => $this->getMock(
                'Magento_Core_Model_Email_Template_MailerFactory', array(), array(), '', false
            ),
            'emailInfoFactory' => $this->getMock(
                'Magento_Core_Model_Email_InfoFactory', array(), array(), '', false
            ),
        );
        $this->_model = $helperManager->getObject('Magento_Sales_Model_Order_Invoice', $arguments);
        $this->_model->setOrder($this->_orderMock);
    }

    /**
     * @dataProvider canVoidDataProvider
     * @param bool $canVoid
     */
    public function testCanVoid($canVoid)
    {
        $this->_orderMock->expects($this->once())->method('getPayment')->will($this->returnValue($this->_paymentMock));
        $this->_paymentMock->expects($this->once())
            ->method('canVoid')
            ->with($this->equalTo($this->_model))
            ->will($this->returnValue($canVoid));

        $this->_model->setState(Magento_Sales_Model_Order_Invoice::STATE_PAID);
        $this->assertEquals($canVoid, $this->_model->canVoid());
    }

    /**
     * @dataProvider canVoidDataProvider
     * @param bool $canVoid
     */
    public function testDefaultCanVoid($canVoid)
    {
        $this->_model->setState(Magento_Sales_Model_Order_Invoice::STATE_PAID);
        $this->_model->setCanVoidFlag($canVoid);

        $this->assertEquals($canVoid, $this->_model->canVoid());
    }

    public function canVoidDataProvider()
    {
        return array(
            array(true),
            array(false)
        );
    }
}
