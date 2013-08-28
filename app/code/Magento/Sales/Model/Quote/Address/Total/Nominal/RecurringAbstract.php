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
 * Total model for recurring profiles
 */
abstract class Magento_Sales_Model_Quote_Address_Total_Nominal_RecurringAbstract
    extends Magento_Sales_Model_Quote_Address_Total_Abstract
{
    /**
     * Don't add amounts to address
     *
     * @var bool
     */
    protected $_canAddAmountToAddress = false;

    /**
     * By what key to set data into item
     *
     * @var string
     */
    protected $_itemRowTotalKey = null;

    /**
     * By what key to get data from profile
     *
     * @var string
     */
    protected $_profileDataKey = null;

    /**
     * Collect recurring item parameters and copy to the address items
     *
     * @param Magento_Sales_Model_Quote_Address $address
     * @return Magento_Sales_Model_Quote_Address_Total_Nominal_RecurringAbstract
     */
    public function collect(Magento_Sales_Model_Quote_Address $address)
    {
        parent::collect($address);
        $items = $this->_getAddressItems($address);
        foreach ($items as $item) {
            if ($item->getProduct()->isRecurring()) {
                $profileData = $item->getProduct()->getRecurringProfile();
                if (!empty($profileData[$this->_profileDataKey])) {
                    $item->setData(
                        $this->_itemRowTotalKey,
                        $address->getQuote()->getStore()->convertPrice($profileData[$this->_profileDataKey])
                    );
                    $this->_afterCollectSuccess($address, $item);
                }
            }
        }
        return $this;
    }

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
     * Hook for successful collecting of a recurring amount
     *
     * @param Magento_Sales_Model_Quote_Address $address
     * @param Magento_Sales_Model_Quote_Item_Abstract $item
     */
    protected function _afterCollectSuccess($address, $item)
    {
    }
}
