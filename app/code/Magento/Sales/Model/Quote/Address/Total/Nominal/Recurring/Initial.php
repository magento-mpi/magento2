<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Total model for recurring profile initial fee
 */
namespace Magento\Sales\Model\Quote\Address\Total\Nominal\Recurring;

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
