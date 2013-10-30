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
use Magento\Backend\Test\Block\Sales\Order\Shipment\Totals;

/**
 * Class AdminSalesOrder
 * Manage orders page
 *
 * @package Magento\Sales\Test\Page
 */
class AdminSalesOrderShipmentNew extends Page
{
    /**
     * URL for manage orders page
     */
    const MCA = 'admin/sales_order_shipment/new';

    /**
     * @var Totals
     */
    private $totalsBlock;

    /**
     * Custom constructor
     */
    protected function _init()
    {
        $this->_url = $this->_url = $_ENV['app_backend_url'] . self::MCA;

        $this->totalsBlock = Factory::getBlockFactory()->getMagentoBackendSalesOrderShipmentTotals(
            $this->_browser->find('.order-totals')
        );
    }

    /**
     * Get sales order grid
     *
     * @return Totals
     */
    public function getOrderGridBlock()
    {
        return $this->totalsBlock;
    }
}
