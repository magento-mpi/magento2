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
class Magento_Sales_Model_Quote_Address_Total_Nominal_Recurring_Initial
    extends Magento_Sales_Model_Quote_Address_Total_Nominal_RecurringAbstract
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
