<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Test\Block\Order\PrintOrder;

use Mtf\Block\Block;
use Mtf\Client\Element\Locator;

/**
 * Items block on order's print page.
 */
class Items extends Block
{
    /**
     * Item selector.
     *
     * @var string
     */
    protected $itemSelector = './/tbody[tr[td[contains(., "%s")]]]';

    /**
     * Check if item is visible in print order page.
     *
     * @param \Mtf\Fixture\InjectableFixture $product
     * @return bool
     */
    public function isItemVisible($product)
    {
        return $this->_rootElement->find(
            sprintf($this->itemSelector, $product->getName()),
            Locator::SELECTOR_XPATH
        )->isVisible();
    }
}
