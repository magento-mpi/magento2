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

namespace Magento\Sales\Test\Block\Adminhtml\Order\Create;

use Mtf\Block\Block;

/**
 * Class Totals
 * Adminhtml sales order create totals block
 *
 * @package Magento\Sales\Test\Block\Adminhtml\Order\Create
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
