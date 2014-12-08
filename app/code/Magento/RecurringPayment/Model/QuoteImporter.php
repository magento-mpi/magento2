<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Recurring payment quote model
 */
namespace Magento\RecurringPayment\Model;

class QuoteImporter
{
    /**
     * @var \Magento\RecurringPayment\Model\PaymentFactory
     */
    protected $_paymentFactory;

    /**
     * @param \Magento\RecurringPayment\Model\PaymentFactory $paymentFactory
     */
    public function __construct(\Magento\RecurringPayment\Model\PaymentFactory $paymentFactory)
    {
        $this->_paymentFactory = $paymentFactory;
    }

    /**
     * Prepare recurring payments
     *
     * @param \Magento\Sales\Model\Quote $quote
     * @throws \Exception
     * @return array
     */
    public function import(\Magento\Sales\Model\Quote $quote)
    {
        if (!$quote->getTotalsCollectedFlag()) {
            throw new \Exception('Quote totals must be collected before this operation.');
        }

        $result = [];
        foreach ($quote->getAllVisibleItems() as $item) {
            $product = $item->getProduct();
            if (is_object(
                $product
            ) && $product->getIsRecurring() && ($payment = $this->_paymentFactory->create()->importProduct(
                $product
            ))
            ) {
                $payment->importQuote($quote);
                $payment->importQuoteItem($item);
                $result[] = $payment;
            }
        }
        return $result;
    }
}
