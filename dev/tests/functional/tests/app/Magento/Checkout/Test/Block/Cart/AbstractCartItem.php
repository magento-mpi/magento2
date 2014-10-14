<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Checkout\Test\Block\Cart;

use Mtf\Block\Block;

/**
 * Class AbstractCartItem
 * Base product item block on checkout page
 */
class AbstractCartItem extends Block
{
    /**
     * Selector for product name
     *
     * @var string
     */
    protected $productName = '.product-item-name > a';

    /**
     * Selector for unit price
     *
     * @var string
     */
    protected $price = './/td[@class="col price"]/*[@class="price-excluding-tax"]/span';

    /**
     * Quantity input selector
     *
     * @var string
     */
    protected $qty = './/input[@type="number" and @title="Qty"]';

    /**
     * Cart item sub-total xpath selector
     *
     * @var string
     */
    protected $subtotalPrice = './/td[@class="col subtotal"]//*[@class="price-excluding-tax"]//span[@class="price"]';

    /**
     *  Selector for options block
     *
     * @var string
     */
    protected $optionsBlock = './/dl[@class="cart-item-options"]';

    /**
     * 'Move to Wishlist' button
     *
     * @var string
     */
    protected $wishlistButton = '.actions .towishlist';

    /**
     * Escape currency in price
     *
     * @param string $price
     * @return string|null
     */
    protected function escapeCurrency($price)
    {
        preg_match("/^\\D*\\s*([\\d,\\.]+)\\s*\\D*$/", $price, $matches);
        return (isset($matches[1])) ? $matches[1] : null;
    }

    /**
     * Click on move to wishlist button
     *
     * @return void
     */
    public function moveToWishlist()
    {
        $this->_rootElement->find($this->wishlistButton)->click();
    }
}
