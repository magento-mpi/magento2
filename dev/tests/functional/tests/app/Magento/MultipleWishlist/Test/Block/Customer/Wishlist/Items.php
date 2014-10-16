<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\MultipleWishlist\Test\Block\Customer\Wishlist;

use Mtf\Client\Element;
use Mtf\Client\Element\Locator;
use Magento\MultipleWishlist\Test\Block\Customer\Wishlist\Items\Product;
use Mtf\Fixture\FixtureInterface;

/**
 * Class Items
 * Customer multiple wishlist items block on frontend
 */
class Items extends \Magento\Wishlist\Test\Block\Customer\Wishlist\Items
{
    /**
     * Get item product block
     *
     * @param FixtureInterface $product
     * @return Product
     */
    public function getItemProduct(FixtureInterface $product)
    {
        $productBlock = sprintf($this->itemBlock, $product->getName());
        return $this->blockFactory->create(
            'Magento\MultipleWishlist\Test\Block\Customer\Wishlist\Items\Product',
            ['element' => $this->_rootElement->find($productBlock, Locator::SELECTOR_XPATH)]
        );
    }
}
