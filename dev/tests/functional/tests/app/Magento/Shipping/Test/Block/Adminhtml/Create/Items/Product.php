<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Shipping\Test\Block\Adminhtml\Create\Items;

use Mtf\Block\Block;

/**
 * Class Product
 * Item product block on shipment items block
 */
class Product extends Block
{
    /**
     * Qty input css selector
     *
     * @var string
     */
    protected $qtyInput = '[name^="shipment[items]"]';

    /**
     * Set product quantity to shipment
     *
     * @param int $value
     * @return void
     */
    public function setQty($value)
    {
        $this->_rootElement->find($this->qtyInput)->setValue($value);
    }
}
