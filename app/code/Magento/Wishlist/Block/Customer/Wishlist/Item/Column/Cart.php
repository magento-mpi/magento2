<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Wishlist
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Wishlist block customer item cart column
 *
 * @category    Magento
 * @package     Magento_Wishlist
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Wishlist_Block_Customer_Wishlist_Item_Column_Cart extends Magento_Wishlist_Block_Customer_Wishlist_Item_Column
{
    /**
     * Returns qty to show visually to user
     *
     * @param Magento_Wishlist_Model_Item $item
     * @return float
     */
    public function getAddToCartQty(Magento_Wishlist_Model_Item $item)
    {
        $qty = $item->getQty();
        return $qty ? $qty : 1;
    }
}
