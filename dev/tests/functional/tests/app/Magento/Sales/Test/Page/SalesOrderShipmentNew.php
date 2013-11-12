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

/**
 * Class SalesOrder
 * Manage orders page
 *
 * @package Magento\Sales\Test\Page
 */
class SalesOrderShipmentNew extends Page
{
    /**
     * URL for manage orders page
     */
    const MCA = 'sales/order/shipment/new';

    /**
     * Custom constructor
     */
    protected function _init()
    {
        $this->_url = $this->_url = $_ENV['app_backend_url'] . self::MCA;
    }

    /**
     * Get shipment totals
     *
     * @return \Magento\Backend\Test\Block\Sales\Order\Shipment\Totals
     */
    public function getTotalsBlock()
    {
        return Factory::getBlockFactory()->getMagentoBackendSalesOrderShipmentTotals(
            $this->_browser->find('.order-totals')
        );
    }

    /**
     * Getter for page actions block
     *
     * @return \Magento\Backend\Test\Block\PageActions
     */
    public function getPageActionsBlock()
    {
        return Factory::getBlockFactory()->getMagentoBackendPageActions(
            $this->_browser->find('.page-actions')
        );
    }
}
