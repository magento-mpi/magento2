<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Shipping\Test\Block\Order;

use Mtf\Block\Block;
use Mtf\Client\Element\Locator;
use Magento\Shipping\Test\Block\Order\Shipment\Items;

/**
 * Class Shipment
 * Shipment view block on shipment view page
 */
class Shipment extends Block
{
    /**
     * Shipment item block
     *
     * @var string
     */
    protected $shipmentItemBlock = '//*[@class="order-title" and contains(.,"%d")]';

    /**
     * Shipment content block
     *
     * @var string
     */
    protected $shipmentContent = '/following-sibling::div[contains(@class,"order-items-shipment")][1]';

    /**
     * Get item shipment block
     *
     * @param int $id
     * @return Items
     */
    public function getItemShipmentBlock($id)
    {
        $selector = sprintf($this->shipmentItemBlock, $id) . $this->shipmentContent;
        return $this->blockFactory->create(
            'Magento\Shipping\Test\Block\Order\Shipment\Items',
            ['element' => $this->_rootElement->find($selector, Locator::SELECTOR_XPATH)]
        );
    }
}
