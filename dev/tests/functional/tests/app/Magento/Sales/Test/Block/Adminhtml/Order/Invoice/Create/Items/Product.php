<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Test\Block\Adminhtml\Order\Invoice\Create\Items;

use Mtf\Block\Block;

/**
 * Class Product
 * Item product block on invoice items block
 */
class Product extends Block
{
    /**
     * Qty input css selector
     *
     * @var string
     */
    protected $qtyInput = '[name^="invoice[items]"]';

    /**
     * Set product quantity to invoice
     *
     * @param int $value
     * @return void
     */
    public function setQty($value)
    {
        $this->_rootElement->find($this->qtyInput)->setValue($value);
    }
}
