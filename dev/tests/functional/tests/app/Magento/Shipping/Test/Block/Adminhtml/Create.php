<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Shipping\Test\Block\Adminhtml;

use Magento\Shipping\Test\Block\Adminhtml\Create\Form;
use Magento\Shipping\Test\Block\Adminhtml\Create\Items;
use Magento\Sales\Test\Block\Adminhtml\Order\AbstractCreate;

/**
 * Class Create
 * Shipment create block
 */
class Create extends AbstractCreate
{
    /**
     * Items block css selector
     *
     * @var string
     */
    protected $items = '#ship_items_container';

    /**
     * Fill shipment data
     *
     * @param array $data
     * @param array|null $products [optional]
     * @return void
     */
    public function fill(array $data, $products = null)
    {
        if (isset($data['form_data'])) {
            $this->getFormBlock()->fillData($data['form_data']);
        }
        if (isset($data['items_data']) && $products !== null) {
            foreach ($products as $key => $product) {
                $this->getItemsBlock()->getItemProductBlock($product)->fillProduct($data['items_data'][$key]);
            }
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
            'Magento\Shipping\Test\Block\Adminhtml\Create\Items',
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
            'Magento\Shipping\Test\Block\Adminhtml\Create\Form',
            ['element' => $this->_rootElement->find($this->form)]
        );
    }
}
