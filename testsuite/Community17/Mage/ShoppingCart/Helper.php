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
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Community17_Mage_ShoppingCart_Helper extends Core_Mage_ShoppingCart_Helper
{
    /**
     * Moves products to the wishlist from Shopping Cart
     *
     * @param string $productName
     */
    public function frontMoveToWishlist($productName)
    {
        $this->addParameter('productName', $productName);
        $this->clickControl('link', 'move_to_wishlist');
    }
}