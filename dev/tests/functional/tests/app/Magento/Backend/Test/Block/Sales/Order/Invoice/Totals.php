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

namespace Magento\Backend\Test\Block\Sales\Order\Invoice;

use Mtf\Block\Block;
use Mtf\Client\Element\Locator;

/**
 * Class Totals
 * Invoice totals block
 *
 * @package Magento\Backend\Test\Block\Sales\Order\Invoice
 */
class Totals extends Block
{

    /**
     * Submit invoice selector
     *
     * @var string
     */
    private $submit;

    /**
     * Capture amount select selector
     *
     * @var string
     */
    private $capture;

    /**
     * Initialize block elements
     */
    protected function _init()
    {
        $this->submit = '[data-ui-id="order-items-submit-button"]';
        $this->capture = '[name="invoice[capture_case]"]';
    }

    /**
     * Ship order
     */
    public function submit()
    {
        $this->_rootElement->find($this->submit)->click();
    }

    /**
     * Set Capture amount option:
     * Capture Online|Capture Offline|Not Capture
     *
     * @param string $option
     */
    public function setCaptureOption($option)
    {
        $this->_rootElement->find($this->capture, Locator::SELECTOR_CSS, 'select')->setValue($option);
    }
}
