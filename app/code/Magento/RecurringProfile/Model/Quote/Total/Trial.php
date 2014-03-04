<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\RecurringProfile\Model\Quote\Total;

/**
 * Total model for recurring profile trial payment
 */
class Trial extends AbstractRecurring
{
    /**
     * Custom row total key
     *
     * @var string
     */
    protected $_itemRowTotalKey = 'recurring_trial_payment';

    /**
     * Custom row profile key
     *
     * @var string
     */
    protected $_profileDataKey = 'trial_billing_amount';

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
     * @param \Magento\Sales\Model\Quote\Address $address
     * @param \Magento\Sales\Model\Quote\Item\AbstractItem $item
     * @return void
     * @see \Magento\Sales\Model\Quote\Address\Total\Nominal\Subtotal
     */
    protected function _afterCollectSuccess($address, $item)
    {
        $item->setData('skip_compound_row_total', true);
    }
}
