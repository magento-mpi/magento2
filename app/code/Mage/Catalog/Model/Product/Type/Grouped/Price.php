<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Grouped product price model
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Model_Product_Type_Grouped_Price extends Mage_Catalog_Model_Product_Type_Price
{
    /**
     * Returns product final price depending on options chosen
     *
     * @param   double $qty
     * @param   Mage_Catalog_Model_Product $product
     * @return  double
     */
    public function getFinalPrice($qty=null, $product)
    {
        if (is_null($qty) && !is_null($product->getCalculatedFinalPrice())) {
            return $product->getCalculatedFinalPrice();
        }

        $finalPrice = parent::getFinalPrice($qty, $product);
        if ($product->hasCustomOptions()) {
            /* @var $typeInstance Mage_Catalog_Model_Product_Type_Grouped */
            $typeInstance = $product->getTypeInstance();
            $associatedProducts = $typeInstance->setStoreFilter($product->getStore(), $product)
                ->getAssociatedProducts($product);
            foreach ($associatedProducts as $childProduct) {
                /* @var $childProduct Mage_Catalog_Model_Product */
                $option = $product->getCustomOption('associated_product_' . $childProduct->getId());
                if (!$option) {
                    continue;
                }
                $childQty = $option->getValue();
                if (!$childQty) {
                    continue;
                }
                $finalPrice += $childProduct->getFinalPrice($childQty) * $childQty;
            }
        }

        $product->setFinalPrice($finalPrice);
        Mage::dispatchEvent('catalog_product_type_grouped_price', array('product' => $product));

        return max(0, $product->getData('final_price'));
    }
}
