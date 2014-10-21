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
 * Class View
 * Rma view block
 */
class View extends Block
{
    /**
     * Locator for rma items table.
     *
     * @var string
     */
    protected $rmaItems = '.returns.items';

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
