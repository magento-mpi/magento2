<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Test\Block\Adminhtml\Order\Invoice;

use Magento\Sales\Test\Block\Adminhtml\Order\AbstractCreate;
use Magento\Sales\Test\Block\Adminhtml\Order\Invoice\Create\Form;
use Magento\Sales\Test\Block\Adminhtml\Order\Invoice\Create\Items;

/**
 * Class Create
 * Invoice create block
 */
class Create extends AbstractCreate
{
    /**
     * Items block css selector
     *
     * @var string
     */
    protected $items = '#invoice_item_container';

    /**
     * Get items block
     *
     * @return Items
     */
    protected function getItemsBlock()
    {
        return $this->blockFactory->create(
            'Magento\Sales\Test\Block\Adminhtml\Order\Invoice\Create\Items',
            ['element' => $this->_rootElement->find($this->items)]
        );
    }

    /**
     * Get form block
     *
     * @return Form
     */
    public function getFormBlock()
    {
        return $this->blockFactory->create(
            'Magento\Sales\Test\Block\Adminhtml\Order\Invoice\Create\Form',
            ['element' => $this->_rootElement->find($this->form)]
        );
    }
}
