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

use Magento\Catalog\Model\Product;

/**
 * Tire prices model
 */
class TierPrice extends AbstractPrice
{
    /**
     * @var string
     */
    protected $priceType = 'tier_price';

    /**
     * @var int
     */
    protected $productQty;

    /**
     * @param Product $product
     * @param int $productQty
     */
    public function __construct(Product $product, $productQty = 1)
    {
        $this->productQty = $productQty;
        parent::__construct($product);
    }

    /**
     * {@inheritdoc}
     */
    public function getValue()
    {
        $tierPrice = $this->product->getDataUsingMethod($this->priceType, $this->productQty);
        return is_array($tierPrice) ? $tierPrice[0]['website_price'] : $tierPrice;
    }

    /**
     * @return array
     */
    public function getApplicableTierPrices()
    {
        ///@todo check is float
        $priceList = $this->product->getTierPrice();

        $applicablePrices = [];
        foreach ($priceList as $price) {
            $price['price_qty'] = $price['price_qty'] * 1;

            $productPrice = $this->priceInfo->getPrice('price')->getValue();
            $finalPrice = $this->priceInfo->getPrice('final_price')->getValue();
            if ($productPrice !== $finalPrice) {
                $productPrice = $finalPrice;
            }

            // @TODO create group price model
            // Group price must be used for percent calculation if it is lower
            $groupPrice = $this->priceInfo->getPrice('group_price')->getValue();
            if ($productPrice > $groupPrice) {
                $productPrice = $groupPrice;
            }

            if ($price['price'] < $productPrice) {
                $price['savePercent'] = ceil(100 - ((100 / $productPrice) * $price['price']));

//                $tierPrice = $this->_storeManager->getStore()->convertPrice(
//                    $this->_taxData->getPrice($product, $price['website_price'])
//                );
//                $price['formated_price'] = $this->_storeManager->getStore()->formatPrice($tierPrice);
//                $price['formated_price_incl_tax'] = $this->_storeManager->getStore()->formatPrice(
//                    $this->_storeManager->getStore()->convertPrice(
//                        $this->_taxData->getPrice($product, $price['website_price'], true)
//                    )
//                );

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
                $price['adjustedAmount'] = $adjustment->extractAdjustment($price['website_price'], $this->product);
                $price['website_price'] = $price['website_price'] - $price['adjustedAmount'];
            }
        }
        return $price;
    }
}
