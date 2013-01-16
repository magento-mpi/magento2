<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Launcher
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Launcher_Model_Storelauncher_Payments_SaveHandlerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Launcher_Model_Storelauncher_Payments_SaveHandler
     */
    protected $_saveHandler;

    protected function setUp()
    {
        // Mock payment save handler factory
        $saveHandlerFactory = $this->getMock('Mage_Launcher_Model_Storelauncher_Payments_PaymentSaveHandlerFactory',
            array(), array(), '', false);
        $this->_saveHandler = new Mage_Launcher_Model_Storelauncher_Payments_SaveHandler($saveHandlerFactory);
    }

    protected function tearDown()
    {
        $this->_saveHandler = null;
    }

    public function testSavePaymentMethod()
    {
        $data = array('payment_method' => 'paypal_express_checkout');
        // Mock payments save handler
        $paymentSaveHandler = $this->getMock(
            'Mage_Launcher_Model_Storelauncher_Payments_PaymentSaveHandler',
            array(),
            array(),
            '',
            false
        );
        $paymentSaveHandler->expects($this->once())
            ->method('save')
            ->with($data)
            ->will($this->returnValue($paymentSaveHandler));

        // Mock payment save handler factory
        $saveHandlerFactory = $this->getMock(
            'Mage_Launcher_Model_Storelauncher_Payments_PaymentSaveHandlerFactory',
            array('create'),
            array(),
            '',
            false
        );
        $saveHandlerFactory->expects($this->once())
            ->method('create')
            ->with('paypal_express_checkout')
            ->will($this->returnValue($paymentSaveHandler));

        $saveHandler = new Mage_Launcher_Model_Storelauncher_Payments_SaveHandler($saveHandlerFactory);
        $saveHandler->savePaymentMethod($data);
    }

    /**
     * @expectedException Mage_Launcher_Exception
     * @expectedExceptionMessage Illegal payment method ID specified.
     */
    public function testSavePaymentMethodThrowsExceptionWhenPaymentMethodHasIllegalId()
    {
        $this->_saveHandler->savePaymentMethod(array('payment_method' => 'wrong_id'));
    }

    public function testGetRelatedPaymentMethods()
    {
        $expectedPayments = array(
            'paypal_express_checkout',
            'paypal_payments_standard',
            'paypal_payments_advanced',
            'paypal_payments_pro',
            'paypal_payflow_link',
            'paypal_payflow_pro',
            'authorize_net',
        );
        $this->assertEquals($expectedPayments, $this->_saveHandler->getRelatedPaymentMethods());
    }
}
