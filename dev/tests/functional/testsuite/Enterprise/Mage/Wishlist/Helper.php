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
        if (!empty($options)) {
            $this->productHelper()->frontFillBuyInfo($options);
        }
        $this->addParameter('productName', $productName);
        $waitConditions = array($this->getBasicXpathMessagesExcludeCurrent('success'),
                                $this->_getControlXpath('fieldset', 'log_in_customer',
                                    $this->getUimapPage('frontend', 'customer_login')));
        if ($this->controlIsVisible('link', 'add_to_wishlist')) {
            $this->clickControl('link', 'add_to_wishlist', false);
        } else {
            $this->clickControlAndWaitMessage('link', '');
        }
        $this->waitForElement($waitConditions);
        $this->addParameter('id', $this->defineIdFromUrl());
        $this->validatePage();
    }
}