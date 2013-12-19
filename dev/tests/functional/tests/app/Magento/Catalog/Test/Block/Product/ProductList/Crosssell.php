<?php
/**
 * {license_notice}
 *
 * @category    Mtf
 * @package     Mtf
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */


namespace Magento\Catalog\Test\Block\Product\ProductList;

use Magento\Catalog\Test\Fixture\AbstractProduct;
use Mtf\Block\Block;
use Mtf\Client\Element\Locator;
use \Magento\Catalog\Test\Fixture\Product;

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
     * @return \Mtf\Client\Element
     * @throws \Exception
     */
    public function clickLink($product)
    {
        $this->_rootElement->find(
            sprintf($this->linkSelector, $product->getProductName()),
            Locator::SELECTOR_CSS
        )->click();
    }
}
