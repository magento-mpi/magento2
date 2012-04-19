<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Bundle
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Product View block (to modify getTierPrices method)
 *
 * @category   Mage
 * @package    Mage_Bundle
 * @module     Catalog
 */
class Mage_Bundle_Block_Catalog_Product_View extends Mage_Catalog_Block_Product_View
{
    /**
     * Get tier prices (formatted)
     *
     * @param Mage_Catalog_Model_Product|null $product
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
            $store = Mage::app()->getStore();
            $helper = Mage::helper('Mage_Tax_Helper_Data');
            $specialPrice = $product->getSpecialPrice() ? 100 - $product->getSpecialPrice() : 0;
            foreach ($prices as $price) {
                if ($specialPrice < $price['price']) {
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
