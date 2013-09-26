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
 * Test class for Magento_Paypal_Model_Pro
 */
class Magento_Paypal_Model_ProTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Paypal_Model_Pro
     */
    protected $_pro;

    protected function setUp()
    {
        $objectHelper = new Magento_TestFramework_Helper_ObjectManager($this);
        $args = $objectHelper->getConstructArguments('Magento_Paypal_Model_Pro', array(
            'infoFactory' => $this->getMock('Magento_Paypal_Model_InfoFactory')
        ));
        /** @var $pro Magento_Paypal_Model_Pro */
        $this->_pro = $this->getMock('Magento_Paypal_Model_Pro', array('_isPaymentReviewRequired'), $args);
    }

    /**
     * @param bool $pendingReason
     * @param bool $isReviewRequired
     * @param bool $expected
     * @dataProvider canReviewPaymentDataProvider
     */
    public function testCanReviewPayment($pendingReason, $isReviewRequired, $expected)
    {
        $this->_pro->expects($this->any())
            ->method('_isPaymentReviewRequired')
            ->will($this->returnValue($isReviewRequired));
        $payment = $this->getMockBuilder('Magento_Payment_Model_Info')
            ->disableOriginalConstructor()
            ->setMethods(array('getAdditionalInformation'))
            ->getMock();
        $payment->expects($this->once())
            ->method('getAdditionalInformation')
            ->with($this->equalTo(Magento_Paypal_Model_Info::PENDING_REASON_GLOBAL))
            ->will($this->returnValue($pendingReason));

        $this->assertEquals($expected, $this->_pro->canReviewPayment($payment));
    }

    /**
     * @return array
     */
    public function canReviewPaymentDataProvider()
    {
        return array(
            array(Magento_Paypal_Model_Info::PAYMENTSTATUS_REVIEW, true, false),
            array(Magento_Paypal_Model_Info::PAYMENTSTATUS_REVIEW, false, false),
            array('another_pending_reason', false, false),
            array('another_pending_reason', true, true),
        );
    }
}
