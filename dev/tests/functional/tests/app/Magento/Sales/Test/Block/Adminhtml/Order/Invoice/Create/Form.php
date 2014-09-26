<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Test\Block\Adminhtml\Order\Invoice\Create;

use Mtf\Block\Block;
use Mtf\Client\Element\Locator;

/**
 * Class Form
 * Invoice form block
 */
class Form extends Block
{
    /**
     * Submit invoice selector
     *
     * @var string
     */
    protected $createShipmentCheckbox = '[name="invoice[do_shipment]"]';

    /**
     * Ship order
     *
     * @return void
     */
    public function createShipment()
    {
        $this->_rootElement->find($this->createShipmentCheckbox, Locator::SELECTOR_CSS, 'checkbox')->click();
    }
}
