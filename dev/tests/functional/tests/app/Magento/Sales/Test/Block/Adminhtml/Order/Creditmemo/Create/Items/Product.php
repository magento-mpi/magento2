<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Test\Block\Adminhtml\Order\Creditmemo\Create\Items;

use Mtf\Block\Block;

/**
 * Class Product
 * Item product block on credit memo items block
 */
class Product extends Block
{
    /**
     * Qty input css selector
     *
     * @var string
     */
    protected $qtyInput = '.col-refund [name^="creditmemo[items]"]';

    /**
     * Set product quantity to credit memo
     *
     * @param int $value
     * @return void
     */
    public function setQty($value)
    {
        $this->_rootElement->find($this->qtyInput)->setValue($value);
    }
}
