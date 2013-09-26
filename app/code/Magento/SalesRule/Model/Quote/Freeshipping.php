<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_SalesRule
 * @copyright   {copyright}
 * @license     {license_link}
 */


namespace Magento\SalesRule\Model\Quote;

class Freeshipping extends \Magento\Sales\Model\Quote\Address\Total\AbstractTotal
{
    /**
     * Discount calculation object
     *
     * @var \Magento\SalesRule\Model\Validator
     */
    protected $_calculator;

    /**
     * @var \Magento\Core\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param \Magento\SalesRule\Model\Validator $validator
     */
    public function __construct(
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        \Magento\SalesRule\Model\Validator $validator
    ) {
        $this->setCode('discount');
        $this->_calculator = $validator;
        $this->_storeManager = $storeManager;
    }

    /**
     * Collect information about free shipping for all address items
     *
     * @param   \Magento\Sales\Model\Quote\Address $address
     * @return  \Magento\SalesRule\Model\Quote\Freeshipping
     */
    public function collect(\Magento\Sales\Model\Quote\Address $address)
    {
        parent::collect($address);
        $quote = $address->getQuote();
        $store = $this->_storeManager->getStore($quote->getStoreId());

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
    * @param   \Magento\Sales\Model\Quote\Address $address
    * @return  \Magento\SalesRule\Model\Quote\Freeshipping
    */
    public function fetch(\Magento\Sales\Model\Quote\Address $address)
    {
        return $this;
    }

}
