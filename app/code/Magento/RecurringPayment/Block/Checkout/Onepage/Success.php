<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\RecurringPayment\Block\Checkout\Onepage;

/**
 * Recurring Payment information on Order success page
 */
class Success extends \Magento\View\Element\Template
{
    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var \Magento\RecurringPayment\Model\Resource\Payment\CollectionFactory
     */
    protected $_recurringPaymentCollectionFactory;

    /**
     * @param \Magento\View\Element\Template\Context $context
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\RecurringPayment\Model\Resource\Payment\CollectionFactory $recurringPaymentCollectionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\View\Element\Template\Context $context,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\RecurringPayment\Model\Resource\Payment\CollectionFactory $recurringPaymentCollectionFactory,
        array $data = array()
    ) {
        $this->_checkoutSession = $checkoutSession;
        $this->_customerSession = $customerSession;
        $this->_recurringPaymentCollectionFactory = $recurringPaymentCollectionFactory;
        parent::__construct($context, $data);
    }

    /**
     * Getter for recurring payment view page
     *
     * @param $payment
     * @return string
     */
    public function getPaymentUrl(\Magento\Object $payment)
    {
        return $this->getUrl('sales/recurringPayment/view', array('payment' => $payment->getId()));
    }

    /**
     * Before rendering html, but after trying to load cache
     *
     * @return $this
     */
    protected function _beforeToHtml()
    {
        $this->_prepareLastRecurringPayments();
        return parent::_beforeToHtml();
    }

    /**
     * Prepare recurring payments from the session
     */
    protected function _prepareLastRecurringPayments()
    {
        $paymentIds = $this->_checkoutSession->getLastRecurringPaymentIds();
        if ($paymentIds && is_array($paymentIds)) {
            $collection = $this->_recurringPaymentCollectionFactory->create()
                ->addFieldToFilter('payment_id', array('in' => $paymentIds));
            $payments = array();
            foreach ($collection as $payment) {
                $payments[] = $payment;
            }
            if ($payments) {
                $this->setRecurringPayments($payments);
                if ($this->_customerSession->isLoggedIn()) {
                    $this->setCanViewPayments(true);
                }
            }
        }
    }
}
