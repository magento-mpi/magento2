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
 * Wishlist block
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
     * Product name link selector
     *
     * @var string
     */
    protected $productName = '//a[contains(@class,"product-item-link") and contains(.,"%s")]';

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

    /**
     * Check that product present in wishlist
     *
     * @param string $productName
     * @return bool
     */
    public function isProductPresent($productName)
    {
        $productNameSelector = sprintf($this->productName, $productName);

        return $this->_rootElement->find($productNameSelector, Locator::SELECTOR_XPATH)->isVisible();
    }
}
