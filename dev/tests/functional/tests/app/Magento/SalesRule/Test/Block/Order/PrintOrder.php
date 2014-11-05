<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\SalesRule\Test\Block\Order;

/**
 * Print Order block.
 */
class PrintOrder extends \Magento\Sales\Test\Block\Order\PrintOrder
{
    /**
     * Sales rule selector.
     *
     * @var string
     */
    protected $salesRuleSelector = '.discount';

    /**
     * Returns sales rule block on print order page.
     *
     * @return \Magento\SalesRule\Test\Block\Order\PrintOrder\SalesRule
     */
    public function getSalesRuleBlock()
    {
        $salesRule = $this->blockFactory->create(
            'Magento\SalesRule\Test\Block\Order\PrintOrder\SalesRule',
            ['element' => $this->_rootElement->find($this->salesRuleSelector)]
        );

        return $salesRule;
    }
}
