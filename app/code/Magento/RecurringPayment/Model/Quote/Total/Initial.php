<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Total model for recurring payment initial fee
 */
namespace Magento\RecurringPayment\Model\Quote\Total;

class Initial
    extends \Magento\RecurringPayment\Model\Quote\Total\AbstractRecurring
{
    /**
     * Custom row total/payment keys
     *
     * @var string
     */
    protected $_itemRowTotalKey = 'recurring_initial_fee';

    /**
     * @var string
     */
    protected $_paymentDataKey = 'init_amount';

    /**
     * Get initial fee label
     *
     * @return string
     */
    public function getLabel()
    {
        return __('Initial Fee');
    }
}
