<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Rma\Test\Block\Returns;

use Mtf\Block\Block;
use Magento\Rma\Test\Block\Returns\View\RmaItems;

/**
 * Rma view block on frontend.
 */
class View extends Block
{
    /**
     * Locator for rma items table.
     *
     * @var string
     */
    protected $rmaItems = '.block-returns-items';

    /**
     * Get rma items.
     *
     * @return RmaItems
     */
    public function getRmaItems()
    {
        return $this->blockFactory->create(
            '\Magento\Rma\Test\Block\Returns\View\RmaItems',
            ['element' => $this->_rootElement->find($this->rmaItems)]
        );
    }
}
