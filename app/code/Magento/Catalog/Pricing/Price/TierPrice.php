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

/**
 * Tire prices model
 */
class TierPrice extends AbstractPrice implements TierPriceInterface
{
    /**
     * @var string
     */
    protected $priceType = 'tier_price';

    /**
     * {@inheritdoc}
     */
    public function getValue()
    {
        $tierPrice = $this->salableItem->getDataUsingMethod($this->priceType, $this->quantity);
        return is_array($tierPrice) ? $tierPrice[0]['website_price'] : $tierPrice;
    }

    /**
     * @return array
     */
    public function getApplicableTierPrices()
    {
        ///@todo check is float
        $priceList = $this->salableItem->getTierPrice();

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
//                $msrpPrice = $this->priceInfo->getPrice('msrp');
//                if ($msrpPrice->canApplyMsrp($this->salableItem)) {
//                    $oldPrice = $finalPrice;
//                    $this->salableItem->setPriceCalculation(false);
//                    $this->salableItem->setPrice($price['website_price']);
//                    $this->salableItem->setFinalPrice($price['website_price']);
//
//                    $this->getPriceHtml($this->salableItem);
//                    $this->salableItem->setPriceCalculation(true);
//
//                    $price['real_price_html'] = $this->salableItem->getRealPriceHtml();
//                    $this->salableItem->setFinalPrice($oldPrice);
//                }
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
}
