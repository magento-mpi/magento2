<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    tests
 * @package     selenium
 * @subpackage  tests
 * @author      Magento Core Team <core@magentocommerce.com>
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Helper class
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Wishlist_Helper extends Mage_Selenium_TestCase
{
    // Add product to Wishlist from product page
    // @TODO: Add to Product Helper
    //
    // Add product to Wishlist from category page
    // @TODO: Add to Category Helper
    //
    // Add product to Wishlist from shopping cart
    // @TODO: Add to ShoppingCart Helper
    //
    // Empty shopping cart
    // @TODO: Add to ShoppingCart Helper

    /**
     * Opens My Wishlist.
     *
     * @return boolean If the wishlist was open
     */
    public function frontOpenWishlist()
    {
        // @TODO
    }

    /**
     * Finds the product in the wishlist.
     *
     * @param string|array $productSearchData Data used to find the product in the wishlist
     * @return null|array Returns the product details (name, comment, qty) if the product was found, or null otherwise.
     */
    public function frontFindProductInWishlist($productSearchData)
    {
        // @TODO
    }

    /**
     * Removes the product from the wishlist
     *
     * @param string|array $productSearchData Data used to find the product in the wishlist
     */
    public function frontRemoveProductFromWishlist($productSearchData)
    {
        // @TODO
    }

    /**
     * Removes all products from the wishlist
     */
    public function frontEmptyWishlist()
    {
        // @TODO
    }

    /**
     * Shares the wishlist
     *
     * @param string|array $shareData Data used to share the wishlist (email, message)
     */
    public function frontShareWishlist($shareData)
    {
        // @TODO
    }

}
