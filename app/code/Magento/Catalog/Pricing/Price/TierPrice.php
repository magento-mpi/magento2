<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Pricing\Price;

use Magento\Pricing\Object\SaleableInterface;
use Magento\Customer\Model\Group;
use Magento\Customer\Model\Session;

/**
 * Tire prices model
 */
class TierPrice extends AbstractPrice implements TierPriceInterface, \Magento\Catalog\Pricing\Price\OriginPrice
{
    /**
     * @var string
     */
    protected $priceType = 'tier_price';

    /**
     * @var Session
     */
    protected $customerSession;

    /**
     * @var int
     */
    protected $customerGroup;

    /**
     * @var bool|float
     */
    protected $value;

    /**
     * @var null|array
     */
    protected $clearPriceList;

    /**
     * @param SaleableInterface $salableItem
     * @param float $quantity
     * @param Session $customerSession
     */
    public function __construct(SaleableInterface $salableItem, $quantity, Session $customerSession)
    {
        $this->customerSession = $customerSession;
        if ($salableItem->getCustomerGroupId()) {
            $this->customerGroup = (int) $salableItem->getCustomerGroupId();
        } else {
            $this->customerGroup = (int) $this->customerSession->getCustomerGroupId();
        }
        parent::__construct($salableItem, $quantity);
    }

    /**
     * {@inheritdoc}
     */
    public function getValue()
    {
        if (null === $this->value) {
            $this->value = $this->getTierPrice();
        }
        return $this->value;
    }

    /**
     * @return array
     */
    public function getApplicableTierPrices()
    {
        $priceList = $this->getTierPriceList();

        $applicablePrices = [];
        foreach ($priceList as $price) {
            $price['price_qty'] = $price['price_qty'] * 1;

            $productPrice = $this->priceInfo->getPrice('price')->getValue();
            $finalPrice = $this->priceInfo->getPrice('final_price')->getValue();
            if ($productPrice !== $finalPrice) {
                $productPrice = $finalPrice;
            }

            // Group price must be used for percent calculation if it is lower
            $groupPrice = $this->priceInfo->getPrice('group_price')->getValue();
            if ($productPrice > $groupPrice) {
                $productPrice = $groupPrice;
            }

            if ($price['price'] < $productPrice) {
                $price['savePercent'] = ceil(100 - ((100 / $productPrice) * $price['price']));

                // @TODO check msrp
                /** @var \Magento\Catalog\Pricing\Price\MsrpPrice $msrpPrice */
                // @TODO check msrp

                $applicablePrices[] = $this->applyAdjustment($price);
            }
        }
        return $applicablePrices;
    }

    /**
     * @param float $price
     * @return array
     */
    protected function applyAdjustment($price)
    {
        foreach (array_reverse($this->priceInfo->getAdjustments()) as $adjustment) {
            /** @var \Magento\Pricing\Adjustment\AdjustmentInterface $adjustment */
            if ($adjustment->isIncludedInBasePrice()) {
                $price['adjustedAmount'] = $adjustment->extractAdjustment($price['website_price'], $this->salableItem);
                $price['website_price'] = $price['website_price'] - $price['adjustedAmount'];
            }
        }
        return $price;
    }

    /**
     * Get product tier price by qty
     *
     * @return  bool|float
     */
    protected function getTierPrice()
    {
        $tierPrice = false;
        $prices = $this->getStoredTierPrices();

        if (null === $prices || !is_array($prices)) {
            return $tierPrice;
        }

        $prevQty = 1;
        // @todo it should be minimal price instead
        $prevPrice = $this->salableItem->getPriceInfo()->getPrice('price', $this->quantity);
        $prevGroup = $allGroups = Group::CUST_GROUP_ALL;

        foreach ($prices as $price) {
            if ($price['cust_group'] != $this->customerGroup && $price['cust_group'] != $allGroups) {
                // tier not for current customer group nor is for all groups
                continue;
            }
            if ($this->quantity < $price['price_qty']) {
                // tier is higher than product qty
                continue;
            }
            if ($price['price_qty'] < $prevQty) {
                // higher tier qty already found
                continue;
            }
            if ($price['price_qty'] == $prevQty && $prevGroup != $allGroups && $price['cust_group'] == $allGroups) {
                // found tier qty is same as current tier qty but current tier group is ALL_GROUPS
                continue;
            }
            if ($price['website_price'] < $prevPrice) {
                $tierPrice = $prevPrice = $price['website_price'];
                $prevQty = $price['price_qty'];
                $prevGroup = $price['cust_group'];
            }
        }
        return $tierPrice;
    }

    /**
     * @return array
     */
    public function getTierPriceList()
    {
        $prices = $this->getStoredTierPrices();

        if (null === $prices || !is_array($prices)) {
            return array();
        }

        $qtyCache = array();
        foreach ($prices as $priceKey => $price) {
            if ($price['cust_group'] != $this->customerGroup && $price['cust_group'] != Group::CUST_GROUP_ALL) {
                unset($prices[$priceKey]);
            } elseif (isset($qtyCache[$price['price_qty']])) {
                $priceQty = $qtyCache[$price['price_qty']];
                if ($prices[$priceQty]['website_price'] > $price['website_price']) {
                    unset($prices[$priceQty]);
                    $qtyCache[$price['price_qty']] = $priceKey;
                } else {
                    unset($prices[$priceKey]);
                }
            } else {
                $qtyCache[$price['price_qty']] = $priceKey;
            }
        }
        return $prices ? $prices : array();
    }

    /**
     * Get clear tier price list stored in DB
     *
     * @return array|null
     */
    protected function getStoredTierPrices()
    {
        if (null === $this->clearPriceList) {
            $this->clearPriceList = $this->salableItem->getData('tier_price');

            if (null === $this->clearPriceList) {
                $attribute = $this->salableItem->getResource()->getAttribute('tier_price');
                if ($attribute) {
                    $attribute->getBackend()->afterLoad($this->salableItem);
                    $this->clearPriceList = $this->salableItem->getData('tier_price');
                }
            }
        }
        return $this->clearPriceList;
    }
}
