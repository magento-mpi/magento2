<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_SalesRule
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Magento_SalesRule_Model_Quote_Discount extends Magento_Sales_Model_Quote_Address_Total_Abstract
{
    /**
     * Discount calculation object
     *
     * @var Magento_SalesRule_Model_Validator
     */
    protected $_calculator;

    /**
     * Core event manager proxy
     *
     * @var Magento_Core_Model_Event_Manager
     */
    protected $_eventManager = null;

    /**
     * @var Magento_Core_Model_StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @param Magento_Core_Model_Event_Manager $eventManager
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param Magento_SalesRule_Model_Validator $validator
     */
    public function __construct(
        Magento_Core_Model_Event_Manager $eventManager,
        Magento_Core_Model_StoreManagerInterface $storeManager,
        Magento_SalesRule_Model_Validator $validator
    ) {
        $this->_eventManager = $eventManager;
        $this->setCode('discount');
        $this->_calculator = $validator;
        $this->_storeManager = $storeManager;
    }

    /**
     * Collect address discount amount
     *
     * @param   Magento_Sales_Model_Quote_Address $address
     * @return  Magento_SalesRule_Model_Quote_Discount
     */
    public function collect(Magento_Sales_Model_Quote_Address $address)
    {
        parent::collect($address);
        $quote = $address->getQuote();
        $store = $this->_storeManager->getStore($quote->getStoreId());
        $this->_calculator->reset($address);

        $items = $this->_getAddressItems($address);
        if (!count($items)) {
            return $this;
        }

        $eventArgs = array(
            'website_id'        => $store->getWebsiteId(),
            'customer_group_id' => $quote->getCustomerGroupId(),
            'coupon_code'       => $quote->getCouponCode(),
        );

        $this->_calculator->init($store->getWebsiteId(), $quote->getCustomerGroupId(), $quote->getCouponCode());
        $this->_calculator->initTotals($items, $address);

        $address->setDiscountDescription(array());

        foreach ($items as $item) {
            if ($item->getNoDiscount()) {
                $item->setDiscountAmount(0);
                $item->setBaseDiscountAmount(0);
            }
            else {
                /**
                 * Child item discount we calculate for parent
                 */
                if ($item->getParentItemId()) {
                    continue;
                }

                $eventArgs['item'] = $item;
                $this->_eventManager->dispatch('sales_quote_address_discount_item', $eventArgs);

                if ($item->getHasChildren() && $item->isChildrenCalculated()) {
                    $isMatchedParent = $this->_calculator->canApplyRules($item);
                    $this->_calculator->setSkipActionsValidation($isMatchedParent);
                    foreach ($item->getChildren() as $child) {
                        $this->_calculator->process($child);
                        if ($isMatchedParent) {
                            $this->_recalculateChildDiscount($child);
                        }

                        $eventArgs['item'] = $child;
                        $this->_eventManager->dispatch('sales_quote_address_discount_item', $eventArgs);

                        $this->_aggregateItemDiscount($child);
                    }
                    $this->_calculator->setSkipActionsValidation(false);
                } else {
                    $this->_calculator->process($item);
                    $this->_aggregateItemDiscount($item);
                }
            }
        }

        /**
         * Process shipping amount discount
         */
        $address->setShippingDiscountAmount(0);
        $address->setBaseShippingDiscountAmount(0);
        if ($address->getShippingAmount()) {
            $this->_calculator->processShippingAmount($address);
            $this->_addAmount(-$address->getShippingDiscountAmount());
            $this->_addBaseAmount(-$address->getBaseShippingDiscountAmount());
        }

        $this->_calculator->prepareDescription($address);
        return $this;
    }

    /**
     * Aggregate item discount information to address data and related properties
     *
     * @param   Magento_Sales_Model_Quote_Item_Abstract $item
     * @return  Magento_SalesRule_Model_Quote_Discount
     */
    protected function _aggregateItemDiscount($item)
    {
        $this->_addAmount(-$item->getDiscountAmount());
        $this->_addBaseAmount(-$item->getBaseDiscountAmount());
        return $this;
    }

    /**
     * Recalculate child discount. Separate discount between children
     *
     * @param   Magento_Sales_Model_Quote_Item_Abstract $child
     * @return  Magento_SalesRule_Model_Quote_Discount
     */
    protected function _recalculateChildDiscount($child)
    {
        $item = $child->getParentItem();
        $prices = array('base' => $item->getBaseOriginalPrice(), 'current' => $item->getPrice());
        $keys = array('discount_amount', 'original_discount_amount');
        foreach ($keys as $key) {
            $child->setData($key, $child->getData($key) * $child->getPrice() / $prices['current']);
            $child->setData('base_' . $key, $child->getData('base_' . $key) * $child->getPrice() / $prices['base']);
        }
        return $this;
    }

    /**
     * Add discount total information to address
     *
     * @param   Magento_Sales_Model_Quote_Address $address
     * @return  Magento_SalesRule_Model_Quote_Discount
     */
    public function fetch(Magento_Sales_Model_Quote_Address $address)
    {
        $amount = $address->getDiscountAmount();

        if ($amount!=0) {
            $description = $address->getDiscountDescription();
            if (strlen($description)) {
                $title = __('Discount (%1)', $description);
            } else {
                $title = __('Discount');
            }
            $address->addTotal(array(
                'code'  => $this->getCode(),
                'title' => $title,
                'value' => $amount
            ));
        }
        return $this;
    }
}
