<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_ShoppingCart
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Helper class
 */
class Enterprise_Mage_ShoppingCart_Helper extends Core_Mage_ShoppingCart_Helper
{
    /**
     * Verifies if the product(s) are in the Shopping Cart
     *
     * @param string|array $productNameSet Product name (string) or array of product names to check
     * @param string $controlType, default value = 'link'
     *
     * @return bool|array True if the products are all present.
     *                    Otherwise returns an array of product names that are absent.
     */
    public function frontShoppingCartHasProducts($productNameSet, $controlType = 'link')
    {
        if (is_string($productNameSet)) {
            $productNameSet = array($productNameSet);
        }
        $absentProducts = array();
        foreach ($productNameSet as $productName) {
            $this->addParameter('productName', $productName);
            if (!$this->controlIsPresent($controlType, 'product_name')) {
                $absentProducts[] = $productName;
            }
        }

        return $absentProducts ?: true;
    }
}
