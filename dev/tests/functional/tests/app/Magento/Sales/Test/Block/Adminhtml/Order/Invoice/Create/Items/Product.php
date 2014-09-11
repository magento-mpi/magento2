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
 * Item product to invoice block
 */
class Product extends Block
{
    /**
     * Input qty css selector
     *
     * @var string
     */
    protected $qtyInput = '[name^="invoice[items]"]';

    /**
     * Set Qty to Invoice
     *
     * @param int $value
     * @return void
     */
    public function setProductInvoiceQty($value)
    {
        $this->_rootElement->find($this->qtyInput)->setValue($value);
    }
}
