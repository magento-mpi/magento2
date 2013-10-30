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

use Magento\Backend\Test\Block\PageActions;
use Mtf\Page\Page;
use Mtf\Factory\Factory;
use Mtf\Client\Element\Locator;
use Magento\Backend\Test\Block\Sales\Order;

/**
 * Class AdminSalesOrder
 * Manage orders page
 *
 * @package Magento\Sales\Test\Page
 */
class AdminSalesOrder extends Page
{
    /**
     * URL for manage orders page
     */
    const MCA = 'admin/sales_order';

    /**
     * Sales order grid
     *
     * @var Order\Grid
     */
    private $orderGridBlock;

    /**
     * @var PageActions
     */
    protected $_pageActionsBlock;

    /**
     * Custom constructor
     */
    protected function _init()
    {
        $this->_url = $this->_url = $_ENV['app_backend_url'] . self::MCA;
        $this->orderGridBlock = Factory::getBlockFactory()->getMagentoBackendSalesOrderGrid(
            $this->_browser->find('#sales_order_grid', Locator::SELECTOR_CSS));
        $this->_pageActionsBlock = Factory::getBlockFactory()->getMagentoBackendPageActions(
            $this->_browser->find('.page-actions')
        );
    }

    /**
     * Get sales order grid
     *
     * @return \Magento\Backend\Test\Block\Sales\Order\Grid
     */
    public function getOrderGridBlock()
    {
        return $this->orderGridBlock;
    }

    /**
     * Getter for page actions block
     *
     * @return PageActions
     */
    public function getPageActionsBlock()
    {
        return $this->_pageActionsBlock;
    }
}
