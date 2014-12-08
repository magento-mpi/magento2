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
class Success extends \Magento\Framework\View\Element\Template
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
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\RecurringPayment\Model\Resource\Payment\CollectionFactory $recurringPaymentCollectionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\RecurringPayment\Model\Resource\Payment\CollectionFactory $recurringPaymentCollectionFactory,
        array $data = []
    ) {
        $this->_checkoutSession = $checkoutSession;
        $this->_customerSession = $customerSession;
        $this->_recurringPaymentCollectionFactory = $recurringPaymentCollectionFactory;
        parent::__construct($context, $data);
    }

    /**
     * Getter for recurring payment view page
     *
     * @param \Magento\Framework\Object $payment
     * @return string
     */
    public function getPaymentUrl(\Magento\Framework\Object $payment)
    {
        return $this->getUrl('sales/recurringPayment/view', ['payment' => $payment->getId()]);
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
     *
     * @return void
     */
    protected function _prepareLastRecurringPayments()
    {
        $paymentIds = $this->_checkoutSession->getLastRecurringPaymentIds();
        if ($paymentIds && is_array($paymentIds)) {
            $collection = $this->_recurringPaymentCollectionFactory->create()->addFieldToFilter(
                'payment_id',
                ['in' => $paymentIds]
            );
            $payments = [];
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
