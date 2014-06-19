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
use Magento\Catalog\Test\Fixture\CatalogProductSimple;
use Mtf\Fixture\FixtureInterface;
use Mtf\Fixture\InjectableFixture;

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
     * @param FixtureInterface $crosssell
     * @return bool
     */
    public function verifyProductCrosssell(FixtureInterface $crosssell)
    {
        $productName = ($crosssell instanceof InjectableFixture)
            /** @var CatalogProductSimple $crosssell */
            ? $crosssell->getName()
            /** @var Product $crosssell */
            : $crosssell->getProductName();
        $match = $this->_rootElement->find(sprintf($this->linkSelector, $productName), Locator::SELECTOR_CSS);
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
