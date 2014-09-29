<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Test\Block\Order;

use Mtf\Block\Block;
use Mtf\Client\Element\Locator;
use Magento\Sales\Test\Block\Order\Invoice\Items;

/**
 * Class Invoice
 * Invoice view block on invoice view page
 */
class Invoice extends Block
{
    /**
     * Invoice item block
     *
     * @var string
     */
    protected $invoiceItemBlock = '//*[@class="order-title" and contains(.,"%d")]';

    /**
     * Invoice content block
     *
     * @var string
     */
    protected $invoiceContent = '/following-sibling::div[contains(@class,"table-order-items")][1]';

    /**
     * Get item invoice block
     *
     * @param int $id
     * @return Items
     */
    public function getItemInvoiceBlock($id)
    {
        $selector = sprintf($this->invoiceItemBlock, $id) . $this->invoiceContent;
        return $this->blockFactory->create(
            'Magento\Sales\Test\Block\Order\Invoice\Items',
            ['element' => $this->_rootElement->find($selector, Locator::SELECTOR_XPATH)]
        );
    }
}
