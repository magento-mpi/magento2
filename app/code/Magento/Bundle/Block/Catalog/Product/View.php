<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Bundle
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Bundle\Block\Catalog\Product;

/**
 * Product View block (to modify getTierPrices method)
 */
class View extends \Magento\Catalog\Block\Product\View
{
    /**
     * Get tier prices (formatted)
     *
     * @param \Magento\Catalog\Model\Product|null $product
     * @return array
     */
    public function getTierPrices($product = null)
    {
        if ($product === null) {
            $product = $this->getProduct();
        }

        $res = array();

        $prices = $product->getFormatedTierPrice();
        if (is_array($prices)) {
            $store = $this->_storeManager->getStore();
            $specialPrice = $product->getSpecialPrice();
            $defaultDiscount = max($product->getGroupPrice(), $specialPrice ? 100 - $specialPrice : 0);
            foreach ($prices as $price) {
                if ($defaultDiscount < $price['price']) {
                    $price['price_qty'] += 0;
                    $price['savePercent'] = ceil(100 - $price['price']);

                    $priceExclTax = $this->_taxData->getPrice($product, $price['website_price']);
                    $price['formated_price'] = $store->formatPrice($store->convertPrice($priceExclTax));

                    $priceInclTax = $this->_taxData->getPrice($product, $price['website_price'], true);
                    $price['formated_price_incl_tax'] = $store->formatPrice($store->convertPrice($priceInclTax));

                    $res[] = $price;
                }
            }
        }

        return $res;
    }
}
