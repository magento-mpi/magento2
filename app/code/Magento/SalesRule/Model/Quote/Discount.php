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
use Magento\Framework\Pricing\PriceCurrencyInterface;


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
     * @var PriceCurrencyInterface
     */
    protected $priceCurrency;

    /**
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Framework\StoreManagerInterface $storeManager
     * @param \Magento\SalesRule\Model\Validator $validator
     */
    public function __construct(
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Framework\StoreManagerInterface $storeManager,
        \Magento\SalesRule\Model\Validator $validator,
        PriceCurrencyInterface $priceCurrency

    ) {
        $this->_eventManager = $eventManager;
        $this->setCode('discount');
        $this->_calculator = $validator;
        $this->_storeManager = $storeManager;
        $this->priceCurrency = $priceCurrency;
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
                continue;
            }
            /**
             * Child item discount we calculate for parent
             */
            if ($item->getParentItemId()) {
                continue;
            }

            $eventArgs['item'] = $item;
            $this->_eventManager->dispatch('sales_quote_address_discount_item', $eventArgs);

            if ($item->getHasChildren() && $item->isChildrenCalculated()) {
                $this->_calculator->process($item);
                $this->distributeDiscount($item);
                foreach ($item->getChildren() as $child) {
                    $eventArgs['item'] = $child;
                    $this->_eventManager->dispatch('sales_quote_address_discount_item', $eventArgs);

                    $this->_aggregateItemDiscount($child);
                }
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
     * Distribute discount at parent item to children items
     *
     * @param AbstractItem $item
     * @return $this
     */
    protected function distributeDiscount(AbstractItem $item)
    {
        $parentBaseRowTotal = $item->getBaseRowTotal();
        $keys = [
            'discount_amount',
            'base_discount_amount',
            'original_discount_amount',
            'base_original_discount_amount',
        ];
        $roundingDelta = [];
        foreach ($keys as $key) {
            //Initialize the rounding delta to a tiny number to avoid floating point precision problem
            $roundingDelta[$key] = 0.0000001;
        }
        foreach ($item->getChildren() as $child) {
            $ratio = $child->getBaseRowTotal() / $parentBaseRowTotal;
            foreach ($keys as $key) {
                if (!$item->hasData($key)) {
                    continue;
                }
                $value = $item->getData($key) * $ratio;
                $roundedValue = $this->priceCurrency->round($value + $roundingDelta[$key]);
                $roundingDelta[$key] += $value - $roundedValue;
                $child->setData($key, $roundedValue);
            }
        }

        foreach ($keys as $key) {
            $item->setData($key, 0);
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
