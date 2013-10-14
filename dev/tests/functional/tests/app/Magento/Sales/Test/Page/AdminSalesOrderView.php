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
 * Class AdminSalesOrderView
 * Order view page
 *
 * @package Magento\Sales\Test\Page
 */
class AdminSalesOrderView extends Page
{
    /**
     * URL for manage orders page
     */
    const MCA = 'admin/sales_order/view';

    /**
     * Sales order grid
     *
     * @var Order\Totals
     */
    private $orderTotalsBlock;

    /**
     * Custom constructor
     */
    protected function _init()
    {
        $this->_url = $this->_url = $_ENV['app_backend_url'] . self::MCA;
        $this->orderTotalsBlock = Factory::getBlockFactory()->getMagentoBackendSalesOrderTotals(
            $this->_browser->find('.order-totals', Locator::SELECTOR_CSS));
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
}
