<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftRegistry\Test\Block;

use Mtf\Block\Block;
use Mtf\Client\Element\Locator;
use Mtf\Fixture\InjectableFixture;

/**
 * Class Items
 * Frontend gift registry items
 */
class Items extends Block
{
    /**
     * Product name selector in registry items grid
     *
     * @var string
     */
    protected $productName = '//tr[//a[contains(text(), "%s")]]';

    /**
     * Product quantity selector in registry items grid
     *
     * @var string
     */
    protected $productQty = '[//input[@value="%s"]]';

    /**
     * Is product with appropriate quantity visible in gift registry items grid
     *
     * @param InjectableFixture $product
     * @return bool
     */
    public function isProductInGrid(InjectableFixture $product)
    {
        $name = $product->getName();
        $qty = $product->getCheckoutData()['qty'];
        $productNameSelector = sprintf($this->productName, $name);
        $selector = $qty === null ? $productNameSelector : $productNameSelector . sprintf($this->productQty, $qty);
        return $this->_rootElement->find($selector, Locator::SELECTOR_XPATH)->isVisible();
    }
}
