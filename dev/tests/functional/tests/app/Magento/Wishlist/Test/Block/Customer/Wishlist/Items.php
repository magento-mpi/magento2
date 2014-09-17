<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Wishlist\Test\Block\Customer\Wishlist;

use Mtf\Block\Block;
use Mtf\Client\Element;
use Mtf\Client\Element\Locator;

/**
 * Class Items
 * Customer wishlist items block on frontend
 */
class Items extends Block
{
    /**
     * Item product block
     *
     * @var string
     */
    protected $itemBlock = '//li[.//a[contains(.,"%s")]]';

    /**
     * Get item product block
     *
     * @param string $productName
     * @return \Magento\Wishlist\Test\Block\Customer\Wishlist\Items\Product
     */
    public function getItemProductByName($productName)
    {
        $productBlock = sprintf($this->itemBlock, $productName);
        return $this->blockFactory->create(
            'Magento\Wishlist\Test\Block\Customer\Wishlist\Items\Product',
            ['element' => $this->_rootElement->find($productBlock, Locator::SELECTOR_XPATH)]
        );
    }
}
