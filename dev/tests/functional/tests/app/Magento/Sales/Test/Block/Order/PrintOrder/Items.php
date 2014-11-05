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
 * Class Items
 * Items block on order's print page
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
     * @param string $itemName
     * @return bool
     */
    public function isItemVisible($itemName)
    {
        return $this->_rootElement->find(sprintf($this->itemSelector, $itemName), Locator::SELECTOR_XPATH)->isVisible();
    }
}
