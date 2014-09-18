<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Shipping\Test\Block\Adminhtml\Create;

use Mtf\Block\Block;

/**
 * Class Items
 * Adminhtml items to ship block
 */
class Items extends Block
{
    /**
     * Shipment submit button
     *
     * @var string
     */
    protected $submitShipment = '[data-ui-id="order-items-submit-button"]';

    /**
     * Click 'Submit Shipment' button
     *
     * @return void
     */
    public function submit()
    {
        $this->_rootElement->find($this->submitShipment)->click();
    }
}
