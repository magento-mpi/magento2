<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Tax
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Nominal tax total
 */
class Magento_Tax_Model_Sales_Total_Quote_Nominal_Tax extends Magento_Tax_Model_Sales_Total_Quote_Tax
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
    protected $_itemRowTotalKey = 'tax_amount';

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

    /**
     * Process model configuration array
     *
     * This method can be used for changing totals collect sort order
     *
     * @param array $config
     * @param int|string|Magento_Core_Model_Store $store
     * @return array
     */
    public function processConfigArray($config, $store)
    {
        /**
         * Nominal totals use sort_order configuration node to define the order (not before or after nodes)
         * If there is a requirement to change the order, in which nominal total is calculated, change sort_order
         */
        return $config;
    }
}
