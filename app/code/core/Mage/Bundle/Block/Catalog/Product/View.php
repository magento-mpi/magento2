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
     * @param Mage_Catalog_Model_Product $product
     * @return array
     */
    public function getTierPrices($product = null)
    {
        if (is_null($product)) {
            $product = $this->getProduct();
        }
        $prices  = $product->getFormatedTierPrice();

        $taxHelper = Mage::helper('Mage_Tax_Helper_Data');

        $res = array();
        if (is_array($prices)) {
            foreach ($prices as $price) {
                $price['price_qty'] = $price['price_qty']*1;
                $price['savePercent'] = ceil(100 - $price['price']);
                $price['formated_price'] = Mage::app()->getStore()->formatPrice(
                    Mage::app()->getStore()->convertPrice($taxHelper->getPrice($product, $price['website_price']))
                );
                $price['formated_price_incl_tax'] = Mage::app()->getStore()->formatPrice(
                    Mage::app()->getStore()->convertPrice($taxHelper->getPrice($product, $price['website_price'], true))
                );
                $res[] = $price;
            }
        }

        return $res;
    }
}
