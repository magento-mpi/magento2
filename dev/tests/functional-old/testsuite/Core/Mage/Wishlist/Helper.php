<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Helper class
 *
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Core_Mage_Wishlist_Helper extends Mage_Selenium_AbstractHelper
{
    /**
     * Adds product to wishlist from a specific catalog page.
     *
     * @param string $productName
     * @param string $categoryName
     */
    public function frontAddProductToWishlistFromCatalogPage($productName, $categoryName)
    {
        if (!$this->categoryHelper()->frontSearchAndOpenPageWithProduct($productName, $categoryName)) {
            $this->fail('Could not find "' . $productName . '" product on "' . $categoryName . '" category page');
        }
        $this->moveto($this->getControlElement('pageelement', 'product_name'));
        $this->clickControl('link', 'add_to_wishlist');
    }

    /**
     * Adds product to wishlist from the product details page.
     *
     * @param string $productName
     * @param array $options
     */
    public function frontAddProductToWishlistFromProductPage($productName, $options = array())
    {
        $this->productHelper()->frontOpenProduct($productName);
        if ($this->controlIsPresent('button', 'customize_and_add_to_cart')) {
            $this->clickButton('customize_and_add_to_cart', false);
            $this->waitForControlVisible('field', 'bundle_item_qty');
            $this->waitForControlStopsMoving('field', 'bundle_item_qty');
        }
        if (!empty($options)) {
            $this->productHelper()->frontFillBuyInfo($options);
        }
        $this->addParameter('productName', $productName);
        $waitConditions = array(
            $this->getBasicXpathMessagesExcludeCurrent('success'),
            $this->_getControlXpath('fieldset', 'log_in_customer', $this->getUimapPage('frontend', 'customer_login'))
        );
        $this->clickControl('link', 'add_to_wishlist', false);
        $this->waitForElement($waitConditions);
        $this->addParameter('id', $this->defineIdFromUrl());
        $this->validatePage();
    }

    /**
     * Finds the product in the wishlist.
     *
     * @param string|array $productNameSet Product name or array of product names to search for.
     *
     * @return bool|array True if the products are all present.
     *                    Otherwise returns an array of product names that are absent.
     */
    public function frontWishlistHasProducts($productNameSet)
    {
        if (is_string($productNameSet)) {
            $productNameSet = array($productNameSet);
        }
        $absentProducts = array();
        foreach ($productNameSet as $productName) {
            $this->addParameter('productName', $productName);
            if (!$this->controlIsPresent('link', 'product_name')) {
                $absentProducts[] = $productName;
            }
        }
        return (empty($absentProducts)) ? true : $absentProducts;
    }

    /**
     * Removes the product(s) from the wishlist
     *
     * @param string|array $productNameSet Product name (string) or array of product names to remove
     * @param boolean $validate If true, fails the test in case the removed product is not in the wishlist.
     */
    public function frontRemoveProductsFromWishlist($productNameSet, $validate = true)
    {
        if (is_string($productNameSet)) {
            $productNameSet = array($productNameSet);
        }
        foreach ($productNameSet as $productName) {
            $this->addParameter('productName', $productName);
            if ($this->controlIsPresent('link', 'remove_item')) {
                $this->clickControlAndConfirm('link', 'remove_item', 'confirmation_for_delete');
            } elseif ($validate) {
                $this->fail($productName . ' is not in the wishlist.');

            }
        }
    }

    /**
     * Removes all products from the wishlist
     */
    public function frontClearWishlist()
    {
        if (!$this->controlIsVisible(self::UIMAP_TYPE_FIELDSET, 'customer_menu')) {
            return;
        }
        $this->clickControl(self::UIMAP_TYPE_FIELDSET, 'customer_menu', false);
        $this->clickControl('link', 'my_wishlist');
        while ($this->controlIsPresent('link', 'remove_item_generic')) {
            $this->clickControlAndConfirm('link', 'remove_item_generic', 'confirmation_for_delete');
            $this->assertTrue($this->checkCurrentPage('my_wishlist'), $this->getParsedMessages());
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
        $this->fillFieldset($shareData, 'sharing_info');
        $this->saveForm('share_wishlist');
    }

    /**
     * Adds products to Shopping Cart from the wishlist
     *
     * @param string $productName Product name (string)
     * @param array $productOptions Options to be filled
     */
    public function frontAddToShoppingCartFromWishlist($productName, $productOptions = array())
    {
        $this->addParameter('productName', $productName);
        $this->assertTrue($this->buttonIsPresent('add_to_cart'), 'Product ' . $productName . ' is not in the wishlist');
        $this->clickButton('add_to_cart');
        if ($this->getCurrentPage() == 'product_configure_wishlist' && !empty($productOptions)) {
            $this->productHelper()->frontFillBuyInfo($productOptions);
            $this->clickButton('add_to_cart');
        }
    }
}