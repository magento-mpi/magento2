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

namespace Magento\Backend\Test\Block\Sales\Order\Shipment;

use Mtf\Block\Block;

/**
 * Class Totals
 * Shipment totals block
 *
 * @package Magento\Backend\Test\Block\Sales\Order\Shipment
 */
class Totals extends Block
{
    /**
     * Submit Shipment selector
     *
     * @var string
     */
    protected $submit = '[data-ui-id="order-items-submit-button"]';

    /**
     * Ship order
     */
    public function submit()
    {
        $this->_rootElement->find($this->submit)->click();
    }
}
