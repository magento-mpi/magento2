<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\SalesRule\Model\Quote;

use Magento\Sales\Model\Quote\Address;
use Magento\Sales\Model\Quote\Item\AbstractItem;

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
     * @var \Magento\Framework\Event\ManagerInterface
     */
    protected $_eventManager = null;

    /**
     * @var \Magento\Framework\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Framework\StoreManagerInterface $storeManager
     * @param \Magento\SalesRule\Model\Validator $validator
     */
    public function __construct(
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Framework\StoreManagerInterface $storeManager,
        \Magento\SalesRule\Model\Validator $validator
    ) {
        $this->_eventManager = $eventManager;
        $this->setCode('discount');
        $this->_calculator = $validator;
        $this->_storeManager = $storeManager;
    }

    /**
     * Collect address discount amount
     *
     * @param Address $address
     * @return $this
     */
    public function collect(Address $address)
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
            'website_id' => $store->getWebsiteId(),
            'customer_group_id' => $quote->getCustomerGroupId(),
            'coupon_code' => $quote->getCouponCode()
        );

        $this->_calculator->init($store->getWebsiteId(), $quote->getCustomerGroupId(), $quote->getCouponCode());
        $this->_calculator->initTotals($items, $address);

        $address->setDiscountDescription(array());

        $items = $this->_calculator->sortItemsByPriority($items);
        /** @var \Magento\Sales\Model\Quote\Item $item */
        foreach ($items as $item) {
            if ($item->getNoDiscount() || !$this->_calculator->canApplyDiscount($item)) {
                $item->setDiscountAmount(0);
                $item->setBaseDiscountAmount(0);

                // ensure my children are zeroed out
                if ($item->getHasChildren() && $item->isChildrenCalculated()) {
                    foreach ($item->getChildren() as $child) {
                        $child->setDiscountAmount(0);
                        $child->setBaseDiscountAmount(0);
                    }
                }
                continue;
            }
            // to determine the child item discount, we calculate the parent
            if ($item->getParentItem()) {
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
     * @param AbstractItem $item
     * @return $this
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
     * @param AbstractItem $child
     * @return $this
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
     * @param Address $address
     * @return $this
     */
    public function fetch(Address $address)
    {
        $amount = $address->getDiscountAmount();

        if ($amount != 0) {
            $description = $address->getDiscountDescription();
            $title = __('Discount');
            if (strlen($description)) {
                $title = __('Discount (%1)', $description);
            }
            $address->addTotal(array('code' => $this->getCode(), 'title' => $title, 'value' => $amount));
        }
        return $this;
    }
}
