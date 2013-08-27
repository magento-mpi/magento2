<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_GiftCard
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Enterprise_GiftCard_Block_Catalog_Product_Price extends Magento_Catalog_Block_Product_Price
{
    /**
     * Return minimal amount for Giftcard product using price model
     *
     * @param Magento_Catalog_Model_Product $product
     * @return float
     */
    public function getMinAmount($product = null)
    {
        if (is_null($product)) {
            $product = $this->getProduct();
        }
        return $product->getPriceModel()->getMinAmount($product);
    }

    /**
     * Return maximal amount for Giftcard product using price model
     *
     * @param Magento_Catalog_Model_Product $product
     * @return float
     */
    public function getMaxAmount($product = null)
    {
        if (is_null($product)) {
            $product = $this->getProduct();
        }
        return $product->getPriceModel()->getMaxAmount($product);
    }
}
