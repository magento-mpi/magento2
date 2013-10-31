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

use Magento\Sales\Test\Block\AdminOrderBillingAddress;
use Magento\Sales\Test\Block\AdminOrderCreateSummary;
use Magento\Sales\Test\Block\AdminOrderProductsAddGrid;
use Magento\Sales\Test\Block\AdminOrderProductsOrderedGrid;
use Magento\Sales\Test\Block\AdminOrderShippingAddress;
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
     * @var AdminOrderProductsOrderedGrid
     */
    protected $_itemsOrderedGrid;

    /**
     * @var AdminOrderProductsAddGrid
     */
    protected $_itemsAddGrid;

    /**
     * @var AdminOrderBillingAddress
     */
    protected $_billingAddressForm;

    /**
     * @var AdminOrderShippingAddress
     */
    protected $_shippingAddressForm;

    /**
     * @var AdminOrderCreateSummary
     */
    protected $_orderSummaryBlock;

    /**
     * Custom constructor
     */
    protected function _init()
    {
        $this->_url = $_ENV['app_backend_url'] . self::MCA;
        $this->_orderCustomerBlock = Factory::getBlockFactory()->getMagentoSalesOrderCustomerSelectionGrid(
            $this->_browser->find('#order-customer-selector')
        );
        $this->_itemsOrderedGrid = Factory::getBlockFactory()->getMagentoSalesAdminOrderProductsOrderedGrid(
            $this->_browser->find('#order-items')
        );
        $this->_itemsAddGrid = Factory::getBlockFactory()->getMagentoSalesAdminOrderProductsAddGrid(
            $this->_browser->find('#order-search')
        );
        $this->_billingAddressForm = Factory::getBlockFactory()->getMagentoSalesAdminOrderBillingAddress(
            $this->_browser->find('#order-billing_address')
        );
        $this->_shippingAddressForm = Factory::getBlockFactory()->getMagentoSalesAdminOrderShippingAddress(
            $this->_browser->find('#order-shipping_address')
        );
        $this->_orderSummaryBlock = Factory::getBlockFactory()->getMagentoSalesAdminOrderCreateSummary(
            $this->_browser->find('.order-summary')
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

    /**
     * Getter for order selected products grid
     *
     * @return AdminOrderProductsOrderedGrid
     */
    public function getItemsOrderedGrid()
    {
        return $this->_itemsOrderedGrid;
    }

    /**
     * Getter for order select products grid
     *
     * @return AdminOrderProductsAddGrid
     */
    public function getItemsAddGrid()
    {
        return $this->_itemsAddGrid;
    }

    /**
     * Getter for customer order billing address form
     *
     * @return AdminOrderBillingAddress
     */
    public function getBillingAddressForm()
    {
        return $this->_billingAddressForm;
    }

    /**
     * Getter for customer order shipping address form
     *
     * @return AdminOrderShippingAddress
     */
    public function getShippingAddressForm()
    {
        return $this->_shippingAddressForm;
    }

    /**
     * Getter for customer order shipping address form
     *
     * @return AdminOrderShippingAddress
     */
    public function getOrderSummaryBlock()
    {
        return $this->_orderSummaryBlock;
    }
}
