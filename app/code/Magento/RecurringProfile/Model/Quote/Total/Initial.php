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
    extends \Magento\RecurringProfile\Model\Quote\Total\AbstractRecurring
{
    /**
     * Custom row total key
     *
     * @var string
     */
    protected $_itemRowTotalKey = 'recurring_initial_fee';

    /**
     * Custom row profile key
     *
     * @var string
     */
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
