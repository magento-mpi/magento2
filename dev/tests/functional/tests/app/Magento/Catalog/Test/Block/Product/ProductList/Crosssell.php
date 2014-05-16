<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Block\Product\ProductList;

use Mtf\Block\Block;
use Mtf\Client\Element;
use Mtf\Client\Element\Locator;
use \Magento\Catalog\Test\Fixture\Product;

/**
 * Class Crosssell
 * Crosssell product block on the page
 */
class Crosssell extends Block
{
    /**
     * Link selector
     *
     * @var string
     */
    protected $linkSelector = '.product.name [title="%s"]';

    /**
     * Verify cross-sell item
     *
     * @param Product $crosssell
     * @return bool
     */
    public function verifyProductCrosssell(Product $crosssell)
    {
        $match = $this->_rootElement->find(sprintf($this->linkSelector,
            $crosssell->getProductName()), Locator::SELECTOR_CSS);
        return $match->isVisible();
    }

    /**
     * Click on cross-sell product link
     *
     * @param Product $product
     * @return Element
     */
    public function clickLink($product)
    {
        $this->_rootElement->find(
            sprintf($this->linkSelector, $product->getProductName()),
            Locator::SELECTOR_CSS
        )->click();
    }
}
