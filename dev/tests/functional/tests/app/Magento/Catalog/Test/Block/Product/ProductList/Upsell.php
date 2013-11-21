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

use Mtf\Block\Block;
use Mtf\Client\Element\Locator;
use \Magento\Catalog\Test\Fixture\Product;

class Upsell extends Block
{
    /**
     * Link selector
     *
     * @var string
     */
    protected $linkSelector = '.product.name [title="%s"]';

    /**
     * Verify upsell item
     *
     * @param Product $upsell
     * @return bool
     */
    public function verifyProductUpsell(Product $upsell)
    {
        $match = $this->_rootElement->find(sprintf($this->linkSelector,
                $upsell->getProductName()), Locator::SELECTOR_CSS);
        return $match->isVisible();
    }

    /**
     * Click on upsell product link
     *
     * @param Product $product
     * @return \Mtf\Client\Element
     * @throws \Exception
     */
    public function clickLink($product)
    {
        $link = $this->_rootElement->find(sprintf($this->linkSelector, $product->getProductName()),
            Locator::SELECTOR_CSS
        );
        $link->click();
    }
}
