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
 * Total model for recurring profile trial payment
 */
class Magento_Sales_Model_Quote_Address_Total_Nominal_Recurring_Trial
    extends Magento_Sales_Model_Quote_Address_Total_Nominal_RecurringAbstract
{
    /**
     * Custom row total/profile keys
     *
     * @var string
     */
    protected $_itemRowTotalKey = 'recurring_trial_payment';
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
     * @see Magento_Sales_Model_Quote_Address_Total_Nominal_Subtotal
     * @param Magento_Sales_Model_Quote_Address $address
     * @param Magento_Sales_Model_Quote_Item_Abstract $item
     */
    protected function _afterCollectSuccess($address, $item)
    {
        $item->setData('skip_compound_row_total', true);
    }
}
