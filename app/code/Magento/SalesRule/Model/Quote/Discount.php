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

class Discount extends \Magento\Sales\Model\Quote\Address\Total\AbstractTotal
{
    /**
     * Discount calculation object
     *
     * @var \Magento\SalesRule\Model\Validator
     */
    protected $_calculator;

    /**
     * Core event manager proxy
     *
     * @var Magento_Core_Model_Event_Manager
     */
    protected $_eventManager = null;

    /**
     * @param Magento_Core_Model_Event_Manager $eventManager
     */
    public function __construct(
        Magento_Core_Model_Event_Manager $eventManager
    ) {
        $this->_eventManager = $eventManager;
        $this->setCode('discount');
        $this->_calculator = \Mage::getSingleton('Magento\SalesRule\Model\Validator');
    }

    /**
     * Collect address discount amount
     *
     * @param   \Magento\Sales\Model\Quote\Address $address
     * @return  \Magento\SalesRule\Model\Quote\Discount
     */
    public function collect(\Magento\Sales\Model\Quote\Address $address)
    {
        parent::collect($address);
        $quote = $address->getQuote();
        $store = \Mage::app()->getStore($quote->getStoreId());
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
     * @param   \Magento\Sales\Model\Quote\Item\AbstractItem $item
     * @return  \Magento\SalesRule\Model\Quote\Discount
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
     * @param   \Magento\Sales\Model\Quote\Item\AbstractItem $child
     * @return  \Magento\SalesRule\Model\Quote\Discount
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
     * @param   \Magento\Sales\Model\Quote\Address $address
     * @return  \Magento\SalesRule\Model\Quote\Discount
     */
    public function fetch(\Magento\Sales\Model\Quote\Address $address)
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
