<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Total model for recurring payment trial payment
 */
namespace Magento\RecurringPayment\Model\Quote\Total;

class Trial
    extends \Magento\RecurringPayment\Model\Quote\Total\AbstractRecurring
{
    /**
     * Custom row total/payment keys
     *
     * @var string
     */
    protected $_itemRowTotalKey = 'recurring_trial_payment';
    protected $_paymentDataKey = 'trial_billing_amount';

    /**
     * Get trial payment label
     *
     * @return string
     */
    public function getLabel()
    {
        return __('Trial Payment');
    }

    /**
     * Prevent compounding nominal subtotal in case if the trial payment exists
     *
     * @see \Magento\Sales\Model\Quote\Address\Total\Nominal\Subtotal
     * @param \Magento\Sales\Model\Quote\Address $address
     * @param \Magento\Sales\Model\Quote\Item\AbstractItem $item
     */
    protected function _afterCollectSuccess($address, $item)
    {
        $item->setData('skip_compound_row_total', true);
    }
}
