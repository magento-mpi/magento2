<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\RecurringPayment\Block\Checkout\Onepage;

class SuccessTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\TestFramework\Helper\ObjectManager
     */
    protected $objectManager;

    protected function setUp()
    {
        $this->objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
    }

    /**
     * @covers Magento\RecurringPayment\Block\Checkout\Onepage\Success::_prepareLastRecurringPayments
     */
    public function testToHtmlPreparesRecurringPayments()
    {
        $checkoutSessionArgs = $this->objectManager->getConstructArguments(
            'Magento\Checkout\Model\Session',
            array('storage' => new \Magento\Session\Storage('checkout'))
        );
        $checkoutSession = $this->getMock(
            'Magento\Checkout\Model\Session',
            array('getLastRecurringPaymentIds'),
            $checkoutSessionArgs
        );
        $checkoutSession->expects(
            $this->once()
        )->method(
            'getLastRecurringPaymentIds'
        )->will(
            $this->returnValue(array(1, 2, 3))
        );
        $collection = $this->getMock(
            'Magento\RecurringPayment\Model\Resource\Payment\Collection',
            array('addFieldToFilter'),
            array(),
            '',
            false
        );
        $collection->expects(
            $this->once()
        )->method(
            'addFieldToFilter'
        )->with(
            'payment_id',
            array('in' => array(1, 2, 3))
        )->will(
            $this->returnValue(array())
        );
        $recurringPaymentCollectionFactory = $this->getMock(
            'Magento\RecurringPayment\Model\Resource\Payment\CollectionFactory',
            array('create'),
            array(),
            '',
            false
        );
        $recurringPaymentCollectionFactory->expects(
            $this->once()
        )->method(
            'create'
        )->will(
            $this->returnValue($collection)
        );

        /** @var \Magento\Checkout\Block\Onepage\Success $block */
        $block = $this->objectManager->getObject(
            'Magento\RecurringPayment\Block\Checkout\Onepage\Success',
            array(
                'checkoutSession' => $checkoutSession,
                'recurringPaymentCollectionFactory' => $recurringPaymentCollectionFactory
            )
        );
        $this->assertEquals('', $block->toHtml());
    }
}
