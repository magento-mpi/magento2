<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Paypal
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Magento_Paypal_Model_Ipn
 */
class Magento_Paypal_Model_IpnTest extends PHPUnit_Framework_TestCase
{
    const REQUEST_MC_GROSS = 38.12;

    /**
     * Prepare order property for ipn model
     *
     * @param Magento_Paypal_Model_Ipn|PHPUnit_Framework_MockObject_MockObject $ipn
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    protected function _prepareIpnOrderProperty($ipn)
    {
        // Create payment and order mocks
        $payment = $this->getMockBuilder('Magento_Sales_Model_Order_Payment')
            ->disableOriginalConstructor()
            ->getMock();
        $order = $this->getMockBuilder('Magento_Sales_Model_Order')
            ->disableOriginalConstructor()
            ->getMock();
        $order->expects($this->any())
            ->method('getPayment')
            ->will($this->returnValue($payment));

        // Set order to ipn
        $orderProperty = new ReflectionProperty('Magento_Paypal_Model_Ipn', '_order');
        $orderProperty->setAccessible(true);
        $orderProperty->setValue($ipn, $order);

        return $order;
    }

    public function testLegacyRegisterPaymentAuthorization()
    {
        $ipn = $this->getMock('Magento_Paypal_Model_Ipn', array('_createIpnComment'));
        $ipn->expects($this->once())
            ->method('_createIpnComment')
            ->with($this->equalTo(''));

        $order = $this->_prepareIpnOrderProperty($ipn);
        $order->expects($this->once())
            ->method('canFetchPaymentReviewUpdate')
            ->will($this->returnValue(false));
        $payment = $order->getPayment();
        $payment->expects($this->once())
            ->method('registerAuthorizationNotification')
            ->with($this->equalTo(self::REQUEST_MC_GROSS));
        $payment->expects($this->any())
            ->method('__call')
            ->will($this->returnSelf());

        // Create info mock
        $info = $this->getMock('Magento_Paypal_Model_Info');
        $info->expects($this->once())
            ->method('importToPayment');

        // Set request to ipn
        $requestProperty = new ReflectionProperty('Magento_Paypal_Model_Ipn', '_request');
        $requestProperty->setAccessible(true);
        $requestProperty->setValue($ipn, array(
            'mc_gross' => self::REQUEST_MC_GROSS,
        ));

        // Set info to ipn
        $infoProperty = new ReflectionProperty('Magento_Paypal_Model_Ipn', '_info');
        $infoProperty->setAccessible(true);
        $infoProperty->setValue($ipn, $info);

        $testMethod = new ReflectionMethod('Magento_Paypal_Model_Ipn', '_registerPaymentAuthorization');
        $testMethod->setAccessible(true);
        $testMethod->invoke($ipn);
    }

    public function testPaymentReviewRegisterPaymentAuthorization()
    {
        $ipn = new Magento_Paypal_Model_Ipn();

        $order = $this->_prepareIpnOrderProperty($ipn);
        $order->expects($this->once())
            ->method('canFetchPaymentReviewUpdate')
            ->will($this->returnValue(true));
        $order->getPayment()->expects($this->once())
            ->method('registerPaymentReviewAction')
            ->with(
                $this->equalTo(Magento_Sales_Model_Order_Payment::REVIEW_ACTION_UPDATE),
                $this->equalTo(true)
            );

        $testMethod = new ReflectionMethod('Magento_Paypal_Model_Ipn', '_registerPaymentAuthorization');
        $testMethod->setAccessible(true);
        $testMethod->invoke($ipn);
    }
}
