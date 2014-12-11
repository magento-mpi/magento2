<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\SalesRule\Test\Block\Order;

use Mtf\Client\Element\Locator;

/**
 * Class View
 * View block on order's view page
 */
class View extends \Magento\Sales\Test\Block\Order\View
{
    /**
     * Get item block
     *
     * @param int $id [optional]
     * @return Items
     */
    public function getItemBlock($id = null)
    {
        $selector = ($id === null) ? $this->content : sprintf($this->itemBlock, $id) . $this->content;
        return $this->blockFactory->create(
            'Magento\SalesRule\Test\Block\Order\Items',
            ['element' => $this->_rootElement->find($selector, Locator::SELECTOR_XPATH)]
        );
    }
}
