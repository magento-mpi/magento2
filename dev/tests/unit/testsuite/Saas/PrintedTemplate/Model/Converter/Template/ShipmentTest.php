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
class Saas_PrintedTemplate_Model_Converter_ShipmentTest extends PHPUnit_Framework_TestCase
{
    /**
     * test __construct method
     */
    public function testConstructor()
    {
        $billingAddress = 'billing address';
        $shippingAddress = 'shipping address';
        $payment = new Magento_Object;

        $shipmentMock = $this->_prepareInvoice($billingAddress, $shippingAddress, $payment);

        $template = $this->getMockBuilder('Saas_PrintedTemplate_Model_Template')
            ->disableOriginalConstructor()
            ->getMock();
        $array = array('model' => $shipmentMock, 'template' => $template);

        $expectedVariables = array(
            'shipment' => $shipmentMock,
            'customer' => $shipmentMock->getOrder(),
            'address_billing' => $billingAddress,
            'order' => $shipmentMock->getOrder(),
            'payment' => $shipmentMock->getOrder()->getPayment(),
            'address_shipping' => $shippingAddress
        );

        $model = $this->getMockBuilder('Saas_PrintedTemplate_Model_Converter_Template_Shipment')
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

        $shipmentMock = $this->getMockBuilder('Magento_Sales_Model_Order_Shipment')
            ->disableOriginalConstructor()
            ->setMethods(array('getOrder', 'getBillingAddress', 'getShippingAddress'))
            ->getMock();
        $shipmentMock->expects($this->any())
            ->method('getOrder')
            ->will($this->returnValue($orderMock));
        $shipmentMock->expects($this->any())
            ->method('getBillingAddress')
            ->will($this->returnValue($billingAddress));
        $shipmentMock->expects($this->any())
            ->method('getShippingAddress')
            ->will($this->returnValue($shippingAddress));

        return $shipmentMock;
    }

    /**
     * test __construct method
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage The constructor's arguments are incorrect.
     */
    public function testExceptionInConstructor()
    {
        new Saas_PrintedTemplate_Model_Converter_Template_Shipment(array());
    }
}
