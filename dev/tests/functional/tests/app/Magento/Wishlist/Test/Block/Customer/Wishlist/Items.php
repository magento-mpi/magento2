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
     * Product name link selector
     *
     * @var string
     */
    protected $productName = '//a[contains(@class,"product-item-link") and contains(.,"%s")]';

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
