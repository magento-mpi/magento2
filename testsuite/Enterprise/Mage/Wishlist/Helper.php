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
        if ($this->controlIsVisible('link', 'add_to_wishlist')) {
            $this->clickControlAndWaitMessage('link', 'add_to_wishlist');
        } else {
            $this->clickControlAndWaitMessage('link', 'customized_add_to_wishlist');
        }
    }
}