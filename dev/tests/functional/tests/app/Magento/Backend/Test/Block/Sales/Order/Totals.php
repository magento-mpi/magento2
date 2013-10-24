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
     * Grand total search mask
     *
     * @var string
     */
    protected  $_grandTotalMask;

    /**
     * Initialize block elements
     */
    protected function _init()
    {
        $this->_grandTotalMask          = '//tr[normalize-space(td)="Grand Total"]//span';
    }

    /**
     * Get Grand Total Text
     *
     * @return array|string
     */
    public function getGrandTotal()
    {
        return $this->_rootElement->find($this->_grandTotalMask, Locator::SELECTOR_XPATH)->getText();
    }
}
