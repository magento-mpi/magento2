<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Bundle
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Product View block (to modify getTierPrices method)
 *
 * @category   Magento
 * @package    Magento_Bundle
 * @module     Catalog
 */
class Magento_Bundle_Block_Catalog_Product_View extends Magento_Catalog_Block_Product_View
{
    /**
     * Get tier prices (formatted)
     *
     * @param Magento_Catalog_Model_Product|null $product
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
            $helper = $this->_taxData;
            $specialPrice = $product->getSpecialPrice();
            $defaultDiscount = max($product->getGroupPrice(), $specialPrice ? 100 - $specialPrice : 0);
            foreach ($prices as $price) {
                if ($defaultDiscount < $price['price']) {
                    $price['price_qty'] += 0;
                    $price['savePercent'] = ceil(100 - $price['price']);

                    $priceExclTax = $helper->getPrice($product, $price['website_price']);
                    $price['formated_price'] = $store->formatPrice($store->convertPrice($priceExclTax));

                    $priceInclTax = $helper->getPrice($product, $price['website_price'], true);
                    $price['formated_price_incl_tax'] = $store->formatPrice($store->convertPrice($priceInclTax));

                    $res[] = $price;
                }
            }
        }

        return $res;
    }
}
