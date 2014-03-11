<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\RecurringPayment\Model\Observer;

class CheckoutManagerObserverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Event\Observer
     */
    protected $_observer;

    /**
     * @var \Magento\RecurringPayment\Model\Observer
     */
    protected $_testModel;

    /**
     * @var \Magento\RecurringPayment\Block\Fields
     */
    protected $_fieldsBlock;

    /**
     * @var \Magento\RecurringPayment\Model\RecurringPaymentFactory
     */
    protected $_recurringPaymentFactory;

    /**
     * @var \Magento\Event
     */
    protected $_event;

    /**
     * @var \Magento\RecurringPayment\Model\PaymentFactory
     */
    protected $_paymentFactory;

    /**
     * @var \Magento\RecurringPayment\Model\Payment
     */
    protected $_payment;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;

    protected $_quote;

    protected function setUp()
    {
        $this->_observer = $this->getMock('Magento\Event\Observer', [], [], '', false);
        $this->_fieldsBlock = $this->getMock(
            '\Magento\RecurringPayment\Block\Fields', ['getFieldLabel'], [], '', false
        );
        $this->_recurringPaymentFactory = $this->getMock(
            '\Magento\RecurringPayment\Model\RecurringPaymentFactory', ['create'], [], '', false
        );
        $this->_paymentFactory = $this->getMock(
            '\Magento\RecurringPayment\Model\PaymentFactory', ['create', 'importProduct'], [], '', false
        );
        $this->_checkoutSession = $this->getMock(
            '\Magento\Checkout\Model\Session', ['setLastRecurringPaymentIds'], [], '', false
        );
        $this->_quote = $this->getMock(
            '\Magento\RecurringPayment\Model\QuoteImporter',
            ['import'],
            [],
            '',
            false
        );

        $helper = new \Magento\TestFramework\Helper\ObjectManager($this);

        $this->_testModel = $helper->getObject('Magento\RecurringPayment\Model\Observer\CheckoutManagerObserver', [
            'checkoutSession' => $this->_checkoutSession,
            'quoteImporter' => $this->_quote
        ]);

        $this->_event = $this->getMock(
            'Magento\Event', [
                'getProductElement', 'getProduct', 'getResult', 'getBuyRequest', 'getQuote', 'getApi', 'getObject'
            ], [], '', false
        );

        $this->_observer->expects($this->any())->method('getEvent')->will($this->returnValue($this->_event));
        $this->_payment = $this->getMock('Magento\RecurringPayment\Model\Payment', [
            '__sleep', '__wakeup', 'isValid', 'importQuote', 'importQuoteItem', 'submit', 'getId', 'setMethodCode'
        ], [], '', false);
    }

    public function testSubmitRecurringPayments()
    {
        $this->_prepareRecurringPayments();
        $this->_quote->expects($this->once())->method('import')
            ->will($this->returnValue([$this->_payment]));

        $this->_payment->expects($this->once())->method('isValid')->will($this->returnValue(true));
        $this->_payment->expects($this->once())->method('submit');

        $this->_testModel->submitRecurringPayments($this->_observer);
    }

    public function testAddRecurringPaymentIdsToSession()
    {
        $this->_prepareRecurringPayments();
        $this->_quote->expects($this->once())->method('import')
            ->will($this->returnValue([$this->_payment]));
        $this->_payment->expects($this->once())->method('isValid')->will($this->returnValue(true));
        $this->_payment->expects($this->once())->method('submit');

        $this->_testModel->submitRecurringPayments($this->_observer);

        $this->_testModel->addRecurringPaymentIdsToSession();
    }

    protected function _prepareRecurringPayments()
    {
        $product = $this->getMock('Magento\RecurringPayment\Model\Payment', [
            'getIsRecurring', '__sleep', '__wakeup'
        ], [], '', false);
        $product->expects($this->any())->method('getIsRecurring')->will($this->returnValue(true));

        $this->_payment = $this->getMock('Magento\RecurringPayment\Model\Payment', [
            '__sleep', '__wakeup', 'isValid', 'importQuote', 'importQuoteItem', 'submit', 'getId', 'setMethodCode'
        ], [], '', false);

        $quote = $this->getMock('Magento\Sales\Model\Quote', [
            'getTotalsCollectedFlag', '__sleep', '__wakeup', 'getAllVisibleItems'
        ], [], '', false);

        $this->_event->expects($this->any())->method('getQuote')->will($this->returnValue($quote));
    }
}
