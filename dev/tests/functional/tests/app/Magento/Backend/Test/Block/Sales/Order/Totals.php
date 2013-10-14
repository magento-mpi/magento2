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

namespace Magento\Backend\Test\Block\Sales\Order;

use Mtf\Fixture;
use Mtf\Block\Block;
use Mtf\Client\Element;
use Mtf\Factory\Factory;
use Mtf\Client\Element\Locator;

/**
 * Class Totals
 * Order totals block
 *
 * @package Magento\Backend\Test\Block\Sales\Order
 */
class Totals extends Block
{
    /**
     * Order grand total
     * @var string
     */
    private $grandTotal;

    /**
     * Initialize block elements
     */
    protected function _init()
    {
        $this->grandTotal = '//tr[normalize-space(td)="Grand Total"]//span';
    }

    /**
     * Get price for Grand Total
     */
    public function getGrandTotal()
    {
        return $this->_rootElement->find($this->grandTotal, Locator::SELECTOR_XPATH)->getText();
    }
}
