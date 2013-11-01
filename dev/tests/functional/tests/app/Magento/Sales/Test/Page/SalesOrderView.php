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

namespace Magento\Sales\Test\Page;

use Mtf\Page\Page;
use Mtf\Factory\Factory;
use Mtf\Client\Element\Locator;
use Magento\Backend\Test\Block\Sales\Order;

/**
 * Class SalesOrderView
 * Order view page
 *
 * @package Magento\Sales\Test\Page
 */
class SalesOrderView extends Page
{
    /**
     * URL for manage orders page
     */
    const MCA = 'sales/order/view';

    /**
     * Sales order grid
     *
     * @var Order\Totals
     */
    private $orderTotalsBlock;

    /**
     * Sales order grid
     *
     * @var Order\Totals
     */
    private $orderHistoryBlock;



    /**
     * Custom constructor
     */
    protected function _init()
    {
        $this->_url = $this->_url = $_ENV['app_backend_url'] . self::MCA;
        $this->orderTotalsBlock = Factory::getBlockFactory()->getMagentoBackendSalesOrderTotals(
            $this->_browser->find('.order-totals', Locator::SELECTOR_CSS));
        $this->orderHistoryBlock = Factory::getBlockFactory()->getMagentoBackendSalesOrderHistory(
            $this->_browser->find('.order-comments-history', Locator::SELECTOR_CSS));

    }

    /**
     * Get order totals block
     *
     * @return \Magento\Backend\Test\Block\Sales\Order\Totals
     */
    public function getOrderTotalsBlock()
    {
        return $this->orderTotalsBlock;
    }

    /**
     * Get order totals block
     *
     * @return \Magento\Backend\Test\Block\Sales\Order\History
     */
    public function getOrderHistoryBlock()
    {
        return $this->orderHistoryBlock;
    }
}
