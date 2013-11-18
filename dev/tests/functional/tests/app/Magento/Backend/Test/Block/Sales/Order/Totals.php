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

use Mtf\Block\Block;
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
    protected $grandTotalMask = '//tr[normalize-space(td)="Grand Total"]//span';

    /**
     * Get Grand Total Text
     *
     * @return string
     */
    public function getGrandTotal()
    {
        return $this->_rootElement->find($this->grandTotalMask, Locator::SELECTOR_XPATH)->getText();
    }
}
