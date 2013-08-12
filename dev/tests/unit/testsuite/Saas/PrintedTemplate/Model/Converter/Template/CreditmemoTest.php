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
class Saas_PrintedTemplate_Model_Converter_CreditmemoTest extends PHPUnit_Framework_TestCase
{
    /**
     * test __construct method
     */
    public function testConstructor()
    {
        $billingAddress = 'billing address';
        $shippingAddress = 'shipping address';
        $payment = new Magento_Object;

        $creditmemoMock = $this->_prepareCreditMemo($billingAddress, $shippingAddress, $payment);

        $template = $this->getMockBuilder('Saas_PrintedTemplate_Model_Template')
            ->disableOriginalConstructor()
            ->getMock();
        $array = array('model' => $creditmemoMock, 'template' => $template);

        $expectedVariables = array(
            'creditmemo' => $creditmemoMock,
            'customer' => $creditmemoMock->getOrder(),
            'address_billing' => $billingAddress,
            'order' => $creditmemoMock->getOrder(),
            'payment' => $creditmemoMock->getOrder()->getPayment(),
            'address_shipping' => $shippingAddress
        );

        $model = $this->getMockBuilder('Saas_PrintedTemplate_Model_Converter_Template_Creditmemo')
            ->setMethods(array('_initVariables'))
            ->disableOriginalConstructor()
            ->getMock();
        $model->expects($this->once())
            ->method('_initVariables')
            ->with($this->equalTo($expectedVariables));
        $model->__construct($array);
    }

    protected function _prepareCreditMemo($billingAddress, $shippingAddress, $payment)
    {
        $orderMock = $this->getMockBuilder('Magento_Sales_Model_Order')
            ->disableOriginalConstructor()
            ->setMethods(array('getPayment'))
            ->getMock();
        $orderMock->expects($this->any())
            ->method('getPayment')
            ->will($this->returnValue($payment));

        $creditMemoMock = $this->getMockBuilder('Magento_Sales_Model_Order_Creditmemo')
            ->disableOriginalConstructor()
            ->setMethods(array('getOrder', 'getBillingAddress', 'getShippingAddress'))
            ->getMock();
        $creditMemoMock->expects($this->any())
            ->method('getOrder')
            ->will($this->returnValue($orderMock));
        $creditMemoMock->expects($this->any())
            ->method('getBillingAddress')
            ->will($this->returnValue($billingAddress));
        $creditMemoMock->expects($this->any())
            ->method('getShippingAddress')
            ->will($this->returnValue($shippingAddress));

        return $creditMemoMock;
    }

    /**
     * test __construct method
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage The constructor's arguments are incorrect.
     */
    public function testExceptionInConstructor()
    {
        new Saas_PrintedTemplate_Model_Converter_Template_Creditmemo(array());
    }
}
