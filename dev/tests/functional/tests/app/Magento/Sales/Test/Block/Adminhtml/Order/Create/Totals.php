<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Test\Block\Adminhtml\Order\Create;

use Mtf\Block\Block;

/**
 * Class Totals
 * Adminhtml sales order create totals block
 *
 */
class Totals extends Block
{
    /**
     * 'Submit Order' button
     *
     * @var string
     */
    protected $submitOrder = '.order-totals-bottom button';

    /**
     * Click 'Submit Order' button
     */
    public function submitOrder()
    {
        $this->_rootElement->find($this->submitOrder)->click();
    }
}
