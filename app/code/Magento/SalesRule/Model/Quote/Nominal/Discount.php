<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_SalesRule
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Nominal discount total
 */
class Magento_SalesRule_Model_Quote_Nominal_Discount extends Magento_SalesRule_Model_Quote_Discount
{
    /**
     * Don't add amounts to address
     *
     * @var bool
     */
    protected $_canAddAmountToAddress = false;

    /**
     * Don't fetch anything
     *
     * @param Magento_Sales_Model_Quote_Address $address
     * @return array
     */
    public function fetch(Magento_Sales_Model_Quote_Address $address)
    {
        return Magento_Sales_Model_Quote_Address_Total_Abstract::fetch($address);
    }

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
