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
    protected $price = './/td[@class="col price"]/*[@class="excl tax"]/span';

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
    protected $subtotalPrice = './/td[@class="col subtotal"]//*[@class="excl tax"]//span[@class="price"]';

    /**
     *  Selector for options block
     *
     * @var string
     */
    protected $optionsBlock = './/dl[@class="cart-item-options"]';

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
}
