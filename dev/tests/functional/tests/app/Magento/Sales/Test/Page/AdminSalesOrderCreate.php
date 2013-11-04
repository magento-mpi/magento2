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

use Magento\Backend\Test\Block\Template;
use Magento\Sales\Test\Block\Backend\Order\Addresses;
use Magento\Sales\Test\Block\Backend\Order\OrderCreationSummary;
use Magento\Sales\Test\Block\Backend\Order\PaymentMethods;
use Magento\Sales\Test\Block\Backend\Order\ProductsAddGrid;
use Magento\Sales\Test\Block\Backend\Order\ProductsOrderedGrid;
use Magento\Sales\Test\Block\Backend\Order\SelectStoreView;
use Magento\Sales\Test\Block\Backend\Order\CustomerSelectionGrid;
use Magento\Sales\Test\Block\Backend\Order\ShippingMethods;
use Mtf\Factory\Factory;
use Mtf\Page\Page;

class AdminSalesOrderCreate extends Page
{
    /**
     * URL for manage orders page
     */
    const MCA = 'admin/sales_order_create/index';

    /**
     * Grid for customer selection
     *
     * @var CustomerSelectionGrid
     */
    protected $orderCustomerBlock;

    /**
     * Grid which contain already ordered products
     *
     * @var ProductsOrderedGrid
     */
    protected $itemsOrderedGrid;

    /**
     * Grid for adding products to order
     *
     * @var ProductsAddGrid
     */
    protected $itemsAddGrid;

    /**
     * Block with billing and shipping addresses forms
     *
     * @var Addresses
     */
    protected $addressesBlock;

    /**
     * Block with order creation summary
     *
     * @var OrderCreationSummary
     */
    protected $orderSummaryBlock;

    /**
     * Block for store view selection
     *
     * @var SelectStoreView
     */
    protected $selectStoreViewBlock;

    /**
     * Block with payment methods for order creation
     *
     * @var PaymentMethods
     */
    protected $paymentMethodsBlock;

    /**
     * Block with shipping methods for order creation
     *
     * @var ShippingMethods
     */
    protected $shippingMethodsBlock;

    /**
     * Backend abstract block
     *
     * @var Template
     */
    protected $templateBlock;

    /**
     * Custom constructor
     */
    protected function _init()
    {
        $this->_url = $_ENV['app_backend_url'] . self::MCA;
        $this->orderCustomerBlock = Factory::getBlockFactory()->getMagentoSalesBackendOrderCustomerSelectionGrid(
            $this->_browser->find('#order-customer-selector')
        );
        $this->itemsOrderedGrid = Factory::getBlockFactory()->getMagentoSalesBackendOrderProductsOrderedGrid(
            $this->_browser->find('#order-items')
        );
        $this->itemsAddGrid = Factory::getBlockFactory()->getMagentoSalesBackendOrderProductsAddGrid(
            $this->_browser->find('#order-search')
        );
        $this->addressesBlock = Factory::getBlockFactory()->getMagentoSalesBackendOrderAddresses(
            $this->_browser->find('#order-addresses')
        );
        $this->orderSummaryBlock = Factory::getBlockFactory()->getMagentoSalesBackendOrderOrderCreationSummary(
            $this->_browser->find('.order-summary')
        );
        $this->selectStoreViewBlock = Factory::getBlockFactory()->getMagentoSalesBackendOrderSelectStoreView(
            $this->_browser->find('#order-store-selector')
        );
        $this->paymentMethodsBlock = Factory::getBlockFactory()->getMagentoSalesBackendOrderPaymentMethods(
            $this->_browser->find('.order-billing-method')
        );
        $this->shippingMethodsBlock = Factory::getBlockFactory()->getMagentoSalesBackendOrderShippingMethods(
            $this->_browser->find('.order-shipping-method')
        );
        $this->templateBlock = Factory::getBlockFactory()->getMagentoBackendTemplate(
            $this->_browser->find('#html-body')
        );
    }

    /**
     * Getter for customer selection grid
     *
     * @return CustomerSelectionGrid
     */
    public function getOrderCustomerBlock()
    {
        return $this->orderCustomerBlock;
    }

    /**
     * Getter for order selected products grid
     *
     * @return ProductsOrderedGrid
     */
    public function getItemsOrderedGrid()
    {
        return $this->itemsOrderedGrid;
    }

    /**
     * Getter for order select products grid
     *
     * @return ProductsAddGrid
     */
    public function getItemsAddGrid()
    {
        return $this->itemsAddGrid;
    }

    /**
     * Getter for customer order addresses block
     *
     * @return Addresses
     */
    public function getAddressesBlock()
    {
        return $this->addressesBlock;
    }

    /**
     * Getter for order summary block
     *
     * @return OrderCreationSummary
     */
    public function getOrderSummaryBlock()
    {
        return $this->orderSummaryBlock;
    }

    /**
     * Getter for store view selection
     *
     * @return SelectStoreView
     */
    public function getSelectStoreViewBlock()
    {
        return $this->selectStoreViewBlock;
    }

    /**
     * Getter for payment methods block
     *
     * @return PaymentMethods
     */
    public function getPaymentMethodsBlock()
    {
        return $this->paymentMethodsBlock;
    }

    /**
     * Getter for shipping methods block
     *
     * @return ShippingMethods
     */
    public function getShippingMethodsBlock()
    {
        return $this->shippingMethodsBlock;
    }

    /**
     * Get abstract block
     *
     * @return \Magento\Backend\Test\Block\Template
     */
    public function getTemplateBlock()
    {
        return $this->templateBlock;
    }
}
