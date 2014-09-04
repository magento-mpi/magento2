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
     * Product items selector
     *
     * @var string
     */
    protected $productItems = '.product-items';

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
     * Get items product block
     *
     * @return \Magento\Wishlist\Test\Block\Customer\Wishlist\Items
     */
    public function getProductItemsBlock()
    {
        return $this->blockFactory->create(
            'Magento\Wishlist\Test\Block\Customer\Wishlist\Items',
            ['element' => $this->_rootElement->find($this->productItems)]
        );
    }
}
