<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Checkout\Block\Onepage;

class PaymentTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param mixed $hasRecurringItems
     * @param bool $expected
     * @dataProvider hasRecurringItemsDataProvider
     */
    public function testHasRecurringItems($hasRecurringItems, $expected)
    {
        $helper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $quote = $this->getMock('Magento\Sales\Model\Quote', array(
            'hasRecurringItems',
            '__wakeup'
        ), array(), '', false);
        $quote->expects($this->once())->method('hasRecurringItems')->will($this->returnValue($hasRecurringItems));
        $checkoutSession = $this->getMock('Magento\Checkout\Model\Session', array('getQuote'), array(), '', false);
        $checkoutSession->expects($this->once())->method('getQuote')->will($this->returnValue($quote));
        /** @var \Magento\Checkout\Block\Onepage\Payment $model */
        $model = $helper->getObject('Magento\Checkout\Block\Onepage\Payment', array(
            'resourceSession' => $checkoutSession
        ));
        $this->assertEquals($expected, $model->hasRecurringItems());
    }

    public function hasRecurringItemsDataProvider()
    {
        return array(
            array(1, true),
            array(false, false),
            array(null, false),
            array(true, true),
        );
    }
}
