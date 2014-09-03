<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Wishlist\Test\Block\Customer;

use Mtf\Block\Block;
use Mtf\Client\Element\Locator;

/**
 * Class Wishlist
 * Wish list details block in "My account"
 */
class Wishlist extends Block
{
    /**
     * "Share Wish List" button selector
     *
     * @var string
     */
    protected $shareWishList = '[name="save_and_share"]';

    /**
     * Product name link selector
     *
     * @var string
     */
    protected $productName = '//a[contains(@class,"product-item-link") and contains(.,"%s")]';

    /**
     * Click button "Share Wish List"
     *
     * @return void
     */
    public function clickShareWishList()
    {
        $this->_rootElement->find($this->shareWishList)->click();
    }

    /**
     * Check that product present in wishlist
     * @param string $productName
     *
     * @return bool
     */
    public function isProductPresent($productName)
    {
        $productNameSelector = sprintf($this->productName, $productName);

        return $this->_rootElement->find($productNameSelector, Locator::SELECTOR_XPATH)->isVisible();
    }
}
