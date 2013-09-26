<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_SalesRule
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Magento_SalesRule_Model_Quote_Freeshipping extends Magento_Sales_Model_Quote_Address_Total_Abstract
{
    /**
     * Discount calculation object
     *
     * @var Magento_SalesRule_Model_Validator
     */
    protected $_calculator;

    /**
     * @param Magento_SalesRule_Model_Validator $ruleValidator
     */
    public function __construct(
        Magento_SalesRule_Model_Validator $ruleValidator
    ) {
        $this->_calculator = $ruleValidator;
        $this->setCode('discount');
    }

    /**
     * Collect information about free shipping for all address items
     *
     * @param   Magento_Sales_Model_Quote_Address $address
     * @return  Magento_SalesRule_Model_Quote_Freeshipping
     */
    public function collect(Magento_Sales_Model_Quote_Address $address)
    {
        parent::collect($address);
        $quote = $address->getQuote();
        $store = Mage::app()->getStore($quote->getStoreId());

        $address->setFreeShipping(0);
        $items = $this->_getAddressItems($address);
        if (!count($items)) {
            return $this;
        }
        $this->_calculator->init($store->getWebsiteId(), $quote->getCustomerGroupId(), $quote->getCouponCode());

        $isAllFree = true;
        foreach ($items as $item) {
            if ($item->getNoDiscount()) {
                $isAllFree = false;
                $item->setFreeShipping(false);
            } else {
                /**
                 * Child item discount we calculate for parent
                 */
                if ($item->getParentItemId()) {
                    continue;
                }
                $this->_calculator->processFreeShipping($item);
                $isItemFree = (bool)$item->getFreeShipping();
                $isAllFree = $isAllFree && $isItemFree;
                if ($item->getHasChildren() && $item->isChildrenCalculated()) {
                    foreach ($item->getChildren() as $child) {
                        $this->_calculator->processFreeShipping($child);
                        /**
                         * Parent free shipping we apply to all children
                         */
                        if ($isItemFree) {
                            $child->setFreeShipping($isItemFree);
                        }

                    }
                }
            }
        }
        if ($isAllFree && !$address->getFreeShipping()) {
            $address->setFreeShipping(true);
        }
        return $this;
    }

   /**
    * Add information about free shipping for all address items to address object
    * By default we not present such information
    *
    * @param   Magento_Sales_Model_Quote_Address $address
    * @return  Magento_SalesRule_Model_Quote_Freeshipping
    */
    public function fetch(Magento_Sales_Model_Quote_Address $address)
    {
        return $this;
    }

}
