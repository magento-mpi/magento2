<?php
/**
 * {license_notice}
 *
 * @category    Saas
 * @package     Saas_PrintedTemplate
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_PrintedTemplate_Model_Converter_InvoiceTest extends PHPUnit_Framework_TestCase
{
    /**
     * test __construct method
     */
    public function testConstructor()
    {
        $billingAddress = 'billing address';
        $shippingAddress = 'shipping address';
        $payment = new Magento_Object;

        $invoiceMock = $this->_prepareInvoice($billingAddress, $shippingAddress, $payment);

        $template = $this->getMockBuilder('Saas_PrintedTemplate_Model_Template')
            ->disableOriginalConstructor()
            ->getMock();
        $array = array('model' => $invoiceMock, 'template' => $template);

        $expectedVariables = array(
            'invoice' => $invoiceMock,
            'customer' => $invoiceMock->getOrder(),
            'address_billing' => $billingAddress,
            'order' => $invoiceMock->getOrder(),
            'payment' => $invoiceMock->getOrder()->getPayment(),
            'address_shipping' => $shippingAddress
        );

        $model = $this->getMockBuilder('Saas_PrintedTemplate_Model_Converter_Template_Invoice')
            ->setMethods(array('_initVariables'))
            ->disableOriginalConstructor()
            ->getMock();
        $model->expects($this->once())
            ->method('_initVariables')
            ->with($this->equalTo($expectedVariables));
        $model->__construct($array);
    }

    protected function _prepareInvoice($billingAddress, $shippingAddress, $payment)
    {
        $orderMock = $this->getMockBuilder('Magento_Sales_Model_Order')
            ->disableOriginalConstructor()
            ->setMethods(array('getPayment'))
            ->getMock();
        $orderMock->expects($this->any())
            ->method('getPayment')
            ->will($this->returnValue($payment));

        $invoiceMock = $this->getMockBuilder('Magento_Sales_Model_Order_Invoice')
            ->disableOriginalConstructor()
            ->setMethods(array('getOrder', 'getBillingAddress', 'getShippingAddress'))
            ->getMock();
        $invoiceMock->expects($this->any())
            ->method('getOrder')
            ->will($this->returnValue($orderMock));
        $invoiceMock->expects($this->any())
            ->method('getBillingAddress')
            ->will($this->returnValue($billingAddress));
        $invoiceMock->expects($this->any())
            ->method('getShippingAddress')
            ->will($this->returnValue($shippingAddress));

        return $invoiceMock;
    }

    /**
     * test __construct method
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage The constructor's arguments are incorrect.
     */
    public function testExceptionInConstructor()
    {
        new Saas_PrintedTemplate_Model_Converter_Template_Invoice(array());
    }
}
