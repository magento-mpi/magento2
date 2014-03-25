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

/**
 * Tire prices model
 */
class TierPrice extends AbstractPrice implements TierPriceInterface, OriginPrice
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
            $tierPrice = false;
            $prices = $this->getStoredTierPrices();
            $prevQty = 1.;
            $prevPrice = $this->salableItem->getPriceInfo()->getPrice('price', $prevQty)->getValue();
            $priceGroup = Group::CUST_GROUP_ALL;

            foreach ($prices as $price) {
                if (!$this->canApplyTierPrice($price, $priceGroup, $prevQty)) {
                    continue;
                }
                if ($price['website_price'] < $prevPrice) {
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
            /** @var BasePrice $productPrice s a minimal available price */

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
     * @param array $price
     * @param int $currentPriceGroup
     * @param float|string $currentQty
     * @return bool
     */
    protected function canApplyTierPrice(array $price, $currentPriceGroup, $currentQty)
    {
        if ($price['cust_group'] !== $this->customerGroup && $price['cust_group'] !== Group::CUST_GROUP_ALL) {
            // tier not for current customer group nor is for all groups
            return false;
        }
        if ($this->quantity < $price['price_qty']) {
            // tier is higher than product qty
            return false;
        }
        if ($price['price_qty'] < $currentQty) {
            // higher tier qty already found
            return false;
        }
        if ($price['price_qty'] == $currentQty
            && $currentPriceGroup !== Group::CUST_GROUP_ALL
            && $price['cust_group'] === Group::CUST_GROUP_ALL
        ) {
            // found tier qty is same as current tier qty but current tier group is ALL_GROUPS
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
            $this->rawPriceList = $this->salableItem->getData('tier_price');
            if (null === $this->rawPriceList) {
                $attribute = $this->salableItem->getResource()->getAttribute('tier_price');
                if ($attribute) {
                    $attribute->getBackend()->afterLoad($this->salableItem);
                    $this->rawPriceList = $this->salableItem->getData('tier_price');
                }
            }
            if (null === $this->rawPriceList || !is_array($this->rawPriceList)) {
                $this->rawPriceList = array();
            }
        }
        return $this->rawPriceList;
    }
}
