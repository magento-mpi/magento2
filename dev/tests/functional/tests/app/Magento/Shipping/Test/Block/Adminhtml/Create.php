<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Shipping\Test\Block\Adminhtml;

use Mtf\Block\Block;
use Magento\Shipping\Test\Block\Adminhtml\Create\Items;
use Magento\Shipping\Test\Block\Adminhtml\Order\Tracking;

/**
 * Class Create
 * Shipment create block
 */
class Create extends Block
{
    /**
     * Items block css selector
     *
     * @var string
     */
    protected $items = '#ship_items_container';

    /**
     * Tracking block css selector
     *
     * @var string
     */
    protected $tracking = '#tracking_numbers_table';

    /**
     * Fill shipment data
     *
     * @param array $data
     * @param array|null $products [optional]
     * @return void
     */
    public function fill(array $data, $products = null)
    {
        if (isset($data['comment']) && $data['comment'] != '-') {
            $this->getItemsBlock()->setComment($data['comment']);
        }
        if (isset($data['qty']) && $products !== null) {
            foreach ($products as $key => $product) {
                if ($data['qty'][$key] == '-') {
                    continue;
                }
                $this->getItemsBlock()->getItemProductBlock($product)->setQty($data['qty'][$key]);
            }
        }
        if (isset($data['tracking']) && $data['tracking']['number'] != '-') {
            $this->getTrackingBlock()->fill([$data['tracking']]);
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
     * Get tracking block
     *
     * @return Tracking
     */
    protected function getTrackingBlock()
    {
        return $this->blockFactory->create(
            'Magento\Shipping\Test\Block\Adminhtml\Order\Tracking',
            ['element' => $this->_rootElement->find($this->tracking)]
        );
    }
}
