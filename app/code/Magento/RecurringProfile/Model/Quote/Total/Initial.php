<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Total model for recurring profile initial fee
 */
namespace Magento\RecurringProfile\Model\Quote\Total;

class Initial
    extends \Magento\Sales\Model\Quote\Address\Total\Nominal\AbstractRecurring
{
    /**
     * Custom row total/profile keys
     *
     * @var string
     */
    protected $_itemRowTotalKey = 'recurring_initial_fee';
    protected $_profileDataKey = 'init_amount';

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
