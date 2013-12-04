<?php
/**
 * {license_notice}
 *
 * @category    Mtf
 * @package     Mtf
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Test\Block\Adminhtml\Order\Invoice\Create;

use Mtf\Block\Block;
use Mtf\Client\Element\Locator;

/**
 * Class Form
 * Invoice form block
 *
 * @package Magento\Sales\Test\Block\Adminhtml\Order\Invoice\Create
 */
class Form extends Block
{
    /**
     * Submit invoice selector
     *
     * @var string
     */
    protected $createShipmentCheckbox = '[data-ui-id="order-items-submit-button"]';

    /**
     * Ship order
     */
    public function createShipment()
    {
        $this->_rootElement->find($this->createShipmentCheckbox, Locator::SELECTOR_CSS, 'checkbox')->click();
    }
}
