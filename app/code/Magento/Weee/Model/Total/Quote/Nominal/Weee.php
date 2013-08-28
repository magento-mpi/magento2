<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Weee
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Nominal fixed product tax total
 */
class Magento_Weee_Model_Total_Quote_Nominal_Weee extends Magento_Weee_Model_Total_Quote_Weee
{
    /**
     * Don't add amounts to address
     *
     * @var bool
     */
    protected $_canAddAmountToAddress = false;

    /**
     * Custom row total key
     *
     * @var string
     */
    protected $_itemRowTotalKey = 'weee_tax_applied_row_amount';

    /**
     * Get nominal items only
     *
     * @param Magento_Sales_Model_Quote_Address $address
     * @return array
     */
    protected function _getAddressItems(Magento_Sales_Model_Quote_Address $address)
    {
        return $address->getAllNominalItems();
    }
}
