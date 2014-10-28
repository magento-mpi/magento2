<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\RecurringPayment\Model\Observer;

class CheckoutManagerObserver
{
    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;

    /**
     * @var \Magento\RecurringPayment\Model\QuoteImporter
     */
    protected $_quoteImporter;

    /**
     * @var array
     */
    protected $_recurringPayments = null;

    /** @var  \Magento\RecurringPayment\Model\Method\RecurringPaymentSpecification */
    protected $specification;

    /**
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\RecurringPayment\Model\QuoteImporter $quoteImporter
     * @param \Magento\RecurringPayment\Model\Method\RecurringPaymentSpecification $specification
     */
    public function __construct(
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\RecurringPayment\Model\QuoteImporter $quoteImporter,
        \Magento\RecurringPayment\Model\Method\RecurringPaymentSpecification $specification
    ) {
        $this->_checkoutSession = $checkoutSession;
        $this->_quoteImporter = $quoteImporter;
        $this->specification = $specification;
    }

    /**
     * Submit recurring payments
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     * @throws \Magento\Framework\Model\Exception
     */
    public function submitRecurringPayments($observer)
    {
        $paymentMethod = $observer->getEvent()->getQuote()->getPayment();
        if ($this->specification->isSatisfiedBy($paymentMethod->getCode())) {
            $this->_recurringPayments = $this->_quoteImporter->import($observer->getEvent()->getQuote());
            foreach ($this->_recurringPayments as $payment) {
                if (!$payment->isValid()) {
                    throw new \Magento\Framework\Model\Exception($payment->getValidationErrors());
                }
                $payment->submit();
            }
        }
    }

    /**
     * Add recurring payment ids to session
     *
     * @return void
     */
    public function addRecurringPaymentIdsToSession()
    {
        if ($this->_recurringPayments) {
            $ids = array();
            foreach ($this->_recurringPayments as $payment) {
                $ids[] = $payment->getId();
            }
            $this->_checkoutSession->setLastRecurringPaymentIds($ids);
        }
    }
}
