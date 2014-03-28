<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Pricing\Price;

use Magento\Pricing\Object\SaleableInterface;
use Magento\Customer\Model\Group;
use Magento\Customer\Model\Session;
use Magento\Pricing\PriceInfoInterface;

/**
 * Tire prices model
 */
class TierPrice extends RegularPrice implements TierPriceInterface
{
    /**
     * @var string
     */
    protected $priceType = self::PRICE_TYPE_TIER;

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
     * Raw price list stored in DB
     *
     * @var array
     */
    protected $rawPriceList;

    /**
     * Applicable price list
     *
     * @var array
     */
    protected $priceList;

    /**
     * @param SaleableInterface $salableItem
     * @param float $quantity
     * @param Session $customerSession
     */
    public function __construct(SaleableInterface $salableItem, $quantity, Session $customerSession)
    {
        $this->customerSession = $customerSession;
        if ($salableItem->hasCustomerGroupId()) {
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
            $prices = $this->getStoredTierPrices();
            $prevQty = PriceInfoInterface::PRODUCT_QUANTITY_DEFAULT;
            $prevPrice = $tierPrice = false;
            $priceGroup = Group::CUST_GROUP_ALL;

            foreach ($prices as $price) {
                if (!$this->canApplyTierPrice($price, $priceGroup, $prevQty)) {
                    continue;
                }
                if (false === $prevPrice || $price['website_price'] < $prevPrice) {
                    $tierPrice = $prevPrice = $price['website_price'];
                    $prevQty = $price['price_qty'];
                    $priceGroup = $price['cust_group'];
                }
            }
            $this->value = $tierPrice;
        }
        return $this->value;
    }

    /**
     * @return int
     */
    public function getTierPriceCount()
    {
        return count($this->getTierPriceList());
    }

    /**
     * @return array
     */
    public function getTierPriceList()
    {
        if (null === $this->priceList) {
            $prices = $this->getStoredTierPrices();
            $qtyCache = [];
            /** @var float $productPrice is a minimal available price */
            $productPrice = $this->priceInfo->getPrice(BasePrice::PRICE_TYPE_BASE_PRICE)->getValue();
            foreach ($prices as $priceKey => $price) {
                if ($price['cust_group'] !== $this->customerGroup && $price['cust_group'] !== Group::CUST_GROUP_ALL) {
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

            $applicablePrices = [];
            foreach ($prices as $price) {
                // convert string value to float
                $price['price_qty'] = $price['price_qty'] * 1;

                if ($price['price'] < $productPrice) {
                    $price['savePercent'] = ceil(100 - ((100 / $productPrice) * $price['price']));
                    $applicablePrices[] = $this->applyAdjustment($price);
                }
            }
            $this->priceList = $applicablePrices;
        }
        return $this->priceList;
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
     * Can apply tier price
     *
     * @param array $currentTierPrice
     * @param int $prevPriceGroup
     * @param float|string $prevQty
     * @return bool
     */
    protected function canApplyTierPrice(array $currentTierPrice, $prevPriceGroup, $prevQty)
    {
        // Tier price can be applied, if:
        // tier price is for current customer group or is for all groups
        if ($currentTierPrice['cust_group'] !== $this->customerGroup
            && $currentTierPrice['cust_group'] !== Group::CUST_GROUP_ALL
        ) {
            return false;
        }
        // and tier qty is lower than product qty
        if ($this->quantity < $currentTierPrice['price_qty']) {
            return false;
        }
        // and tier qty is bigger than previous qty
        if ($currentTierPrice['price_qty'] < $prevQty) {
            return false;
        }
        // and found tier qty is same as previous tier qty, but current tier group isn't ALL_GROUPS
        if ($currentTierPrice['price_qty'] == $prevQty
            && $prevPriceGroup !== Group::CUST_GROUP_ALL
            && $currentTierPrice['cust_group'] === Group::CUST_GROUP_ALL
        ) {
            return false;
        }
        return true;
    }

    /**
     * Get clear tier price list stored in DB
     *
     * @return array
     */
    protected function getStoredTierPrices()
    {
        if (null === $this->rawPriceList) {
            $this->rawPriceList = $this->salableItem->getData(self::PRICE_TYPE_TIER);
            if (null === $this->rawPriceList) {
                /** @var \Magento\Eav\Model\Entity\Attribute\AbstractAttribute $attribute */
                $attribute = $this->salableItem->getResource()->getAttribute(self::PRICE_TYPE_TIER);
                if ($attribute) {
                    $attribute->getBackend()->afterLoad($this->salableItem);
                    $this->rawPriceList = $this->salableItem->getData(self::PRICE_TYPE_TIER);
                }
            }
            if (null === $this->rawPriceList || !is_array($this->rawPriceList)) {
                $this->rawPriceList = array();
            }
        }
        return $this->rawPriceList;
    }
}
