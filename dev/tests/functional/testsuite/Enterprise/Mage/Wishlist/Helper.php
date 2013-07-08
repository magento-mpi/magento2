<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Wishlist
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
class Enterprise_Mage_Wishlist_Helper extends Core_Mage_Wishlist_Helper
{
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
            $this->waitForControlVisible('fieldset', 'customize_product_info');
            $this->waitForControlNotVisible('fieldset', 'product_info');
        }
        if (!empty($options)) {
            $this->productHelper()->frontFillBuyInfo($options);
        }
        $this->addParameter('productName', $productName);
        $waitConditions = array(
            $this->getBasicXpathMessagesExcludeCurrent('success'),
            $this->_getControlXpath('fieldset', 'log_in_customer', $this->getUimapPage('frontend', 'customer_login'))
        );
        if ($this->controlIsVisible('link', 'add_to_wishlist')) {
            $this->clickControl('link', 'add_to_wishlist', false);
        } else {
            $this->clickControlAndWaitMessage('link', 'customized_add_to_wishlist');
        }
        $this->waitForElement($waitConditions);
        $this->addParameter('id', $this->defineIdFromUrl());
        $this->validatePage();
    }
}