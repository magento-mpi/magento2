<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Test\Block\Adminhtml\Order\Invoice;

use Mtf\Block\Block;
use Magento\Sales\Test\Block\Adminhtml\Order\Invoice\Create\Form;
use Magento\Sales\Test\Block\Adminhtml\Order\Invoice\Create\Items;

/**
 * Class Create
 * Invoice create block
 */
class Create extends Block
{
    /**
     * Items block css selector
     *
     * @var string
     */
    protected $items = '#invoice_item_container';

    /**
     * Form block css selector
     *
     * @var string
     */
    protected $form = '#edit_form';

    /**
     * Fill invoice data
     *
     * @param array $data
     * @param array|null $products [optional]
     * @return void
     */
    public function fill(array $data, $products = null)
    {
        if (isset($data['comment'])) {
            $this->getItemsBlock()->setHistory($data['comment']);
        }
        if (isset($data['qty']) && $data['qty'] !== '-' && $products !== null) {
            foreach ($products as $product) {
                $this->getItemsBlock()->getItemProductBlockBySku($product->getSku())->setQty($data['qty']);
            }
            $this->getItemsBlock()->clickUpdateQty();
        }
        if (isset($data['do_shipment']) && $data['do_shipment'] !== '-') {
            $this->getFormBlock()->createShipment();
        }
    }

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
    protected function getFormBlock()
    {
        return $this->blockFactory->create(
            'Magento\Sales\Test\Block\Adminhtml\Order\Invoice\Create\Form',
            ['element' => $this->_rootElement->find($this->form)]
        );
    }
}
