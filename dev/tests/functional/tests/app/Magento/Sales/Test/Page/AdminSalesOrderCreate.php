<?php
/**
 * {license_notice}
 *
 * @spi
 * @category    Mtf
 * @package     Mtf
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Test\Page;

use Magento\Sales\Test\Block\OrderCustomerSelectionGrid;
use Mtf\Factory\Factory;
use Mtf\Page\Page;

class AdminSalesOrderCreate extends Page
{
    /**
     * URL for manage orders page
     */
    const MCA = 'admin/sales_order_create/index';

    /**
     * @var OrderCustomerSelectionGrid
     */
    protected $_orderCustomerBlock;

    /**
     * Custom constructor
     */
    protected function _init()
    {
        $this->_url = $_ENV['app_backend_url'] . self::MCA;
        $this->_orderCustomerBlock = Factory::getBlockFactory()->getMagentoSalesOrderCustomerSelectionGrid(
            $this->_browser->find('#order-customer-selector')
        );
    }

    /**
     * Getter for customer selection grid
     *
     * @return OrderCustomerSelectionGrid
     */
    public function getOrderCustomerBlock()
    {
        return $this->_orderCustomerBlock;
    }
}
