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
     * Is visible product with appropriate quantity in gift registry items grid
     *
     * @param string $name
     * @param string|null $qty
     * @return bool
     */
    public function isProductInGrid($name, $qty = null)
    {
        $productNameSelector = sprintf($this->productName, $name);
        $selector = $qty === null ? $productNameSelector : $productNameSelector . sprintf($this->productQty, $qty);
        return $this->_rootElement->find($selector, Locator::SELECTOR_XPATH)->isVisible();
    }
}
