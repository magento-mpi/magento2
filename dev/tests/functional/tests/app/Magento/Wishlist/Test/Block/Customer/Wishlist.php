<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Wishlist\Test\Block\Customer;

use Mtf\Block\Block;

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
     * Selector for 'Add to Cart' button
     *
     * @var string
     */
    protected $addToCart = '.action.tocart';

    /**
     * Button 'Update Wish List' css selector
     *
     * @var string
     */
    protected $updateButton = '.action.update';

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
     * Click button 'Add To Cart'
     *
     * @return void
     */
    public function clickAddToCart()
    {
        $this->_rootElement->find($this->addToCart)->click();
    }

    /**
     * Click button 'Update Wish List'
     *
     * @return void
     */
    public function clickUpdateWishlist()
    {
        $this->_rootElement->find($this->updateButton)->click();
    }
}
