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
 * Test class for Mage_Paypal_Model_Pro
 */
class Mage_Paypal_Model_ProTest extends PHPUnit_Framework_TestCase
{
    /**
     * @param bool $pendingReason
     * @param bool $isReviewRequired
     * @param bool $expected
     * @dataProvider canReviewPaymentDataProvider
     */
    public function testCanReviewPayment($pendingReason, $isReviewRequired, $expected)
    {
        /** @var $pro Mage_Paypal_Model_Pro */
        $pro = $this->getMock('Mage_Paypal_Model_Pro', array('_isPaymentReviewRequired'));
        $pro->expects($this->any())
            ->method('_isPaymentReviewRequired')
            ->will($this->returnValue($isReviewRequired));
        $payment = $this->getMockBuilder('Mage_Payment_Model_Info')
            ->disableOriginalConstructor()
            ->setMethods(array('getAdditionalInformation'))
            ->getMock();
        $payment->expects($this->once())
            ->method('getAdditionalInformation')
            ->with($this->equalTo(Mage_Paypal_Model_Info::PENDING_REASON_GLOBAL))
            ->will($this->returnValue($pendingReason));

        $this->assertEquals($expected, $pro->canReviewPayment($payment));
    }

    public function canReviewPaymentDataProvider()
    {
        return array(
            array(Mage_Paypal_Model_Info::PAYMENTSTATUS_REVIEW, true, false),
            array(Mage_Paypal_Model_Info::PAYMENTSTATUS_REVIEW, false, false),
            array('another_pending_reason', false, false),
            array('another_pending_reason', true, true),
        );
    }
}
