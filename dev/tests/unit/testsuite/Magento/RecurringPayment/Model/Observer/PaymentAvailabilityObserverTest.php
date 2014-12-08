<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\RecurringPayment\Model\Observer;

use Magento\TestFramework\Helper\ObjectManager as ObjectManagerHelper;

class PaymentAvailabilityObserverTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\RecurringPayment\Model\Observer\PaymentAvailabilityObserver */
    protected $paymentAvailabilityObserver;

    /** @var ObjectManagerHelper */
    protected $objectManagerHelper;

    /** @var \Magento\RecurringPayment\Model\Quote\Filter|\PHPUnit_Framework_MockObject_MockObject */
    protected $filterMock;

    protected function setUp()
    {
        $this->filterMock = $this->getMock('Magento\RecurringPayment\Model\Quote\Filter');

        $this->objectManagerHelper = new ObjectManagerHelper($this);
        $this->paymentAvailabilityObserver = $this->objectManagerHelper->getObject(
            'Magento\RecurringPayment\Model\Observer\PaymentAvailabilityObserver',
            ['quoteFilter' => $this->filterMock]
        );
    }

    public function testObserve()
    {
        $quote = $this->getMockBuilder('Magento\Sales\Model\Quote')->disableOriginalConstructor()->getMock();

        $event = new \Magento\Framework\Event(
            [
                'quote' => $quote,
                'method_instance' => $this->getMockBuilder(
                    'Magento\Payment\Model\Method\AbstractMethod'
                )->disableOriginalConstructor()->getMock(),
                'result' => new \StdClass(),
            ]
        );
        $this->filterMock->expects(
            $this->once()
        )->method(
            'hasRecurringItems'
        )->with(
            $quote
        )->will(
            $this->returnValue(true)
        );

        $observer = $this->getMockBuilder('Magento\Framework\Event\Observer')->disableOriginalConstructor()->getMock();

        $observer->expects($this->any())->method('getEvent')->will($this->returnValue($event));

        $this->paymentAvailabilityObserver->observe($observer);
        $this->assertFalse($event->getResult()->isAvailable);
    }
}
