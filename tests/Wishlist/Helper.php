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
    // Add product to Wishlist from shopping cart
    // @TODO: Add to ShoppingCart Helper

    /**
     * Adds product to wishlist from a specific catalog page.
     *
     * @param string $productName
     * @param string $categoryName
     */
    public function addProductToWishlistFromCatalogPage($productName, $categoryName)
    {
        $pageId = $this->categoryHelper()->frontSearchAndOpenPageWithProduct($productName, $categoryName);
        if (!$pageId)
            $this->fail('Could not find the product');
        $this->addParameter('productName', $productName);
        $this->clickButton('add_to_wishlist');
    }

    /**
     * Adds product to wishlist from the product details page.
     *
     * @param string $productName
     * @param string $categoryPath
     */
    public function addProductToWishlistFromProductPage($productName, $categoryPath = null)
    {
        $this->productHelper()->frontOpenProduct($productName, $categoryPath);
        $this->addParameter('productName', $productName);
        $this->clickButton('add_to_wishlist');
    }

    /**
     * Finds the product in the wishlist.
     *
     * @param string|array $productNameSet Product name or array of product names to search for.
     * @return boolean True if the product is in the wishlist, False otherwise.
     */
    public function frontWishlistHasProducts($productNameSet)
    {
        if (is_string($productNameSet)) {
            $productNameSet = array($productNameSet);
        }
        foreach ($productNameSet as $productName) {
            $this->addParameter('productName', $productName);
            if (!$this->controlIsPresent('link', 'product_name')) {
                return false;
            }
        }
        return true;
    }

    /**
     * Removes the product from the wishlist
     *
     * @param string $productName
     */
    public function frontRemoveProductFromWishlist($productName)
    {
        $this->addParameter('productName', $productName);
        $this->clickButtonAndConfirm('remove_item', 'confirmation_for_delete');
    }

    /**
     * Removes all products from the wishlist
     */
    public function frontClearWishlist()
    {
        while ($this->controlIsPresent('link', 'remove_item_generic')) {
            $this->clickControlAndConfirm('link', 'remove_item_generic', 'confirmation_for_delete');
        }
    }

    /**
     * Shares the wishlist
     *
     * @param string|array $shareData Data used to share the wishlist (email, message)
     */
    public function frontShareWishlist($shareData)
    {
        if (!$this->buttonIsPresent('share_wishlist')) {
            $this->fail("Cannot share an empty wishlist");
        }
        $this->clickButton('share_wishlist');
        $this->fillForm($shareData);
        $this->saveForm('share_wishlist');
    }

}
