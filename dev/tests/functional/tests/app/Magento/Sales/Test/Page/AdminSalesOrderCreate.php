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
use Magento\Sales\Test\Block\Backend\Order\BillingAddress;
use Magento\Sales\Test\Block\Backend\Order\OrderCreationSummary;
use Magento\Sales\Test\Block\Backend\Order\PaymentMethods;
use Magento\Sales\Test\Block\Backend\Order\ProductsAddGrid;
use Magento\Sales\Test\Block\Backend\Order\ProductsOrderedGrid;
use Magento\Sales\Test\Block\Backend\Order\SelectStoreView;
use Magento\Sales\Test\Block\Backend\Order\ShippingAddress;
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
     * @var CustomerSelectionGrid
     */
    protected $_orderCustomerBlock;

    /**
     * @var ProductsOrderedGrid
     */
    protected $_itemsOrderedGrid;

    /**
     * @var ProductsAddGrid
     */
    protected $_itemsAddGrid;

    /**
     * @var BillingAddress
     */
    protected $_billingAddressForm;

    /**
     * @var ShippingAddress
     */
    protected $_shippingAddressForm;

    /**
     * @var OrderCreationSummary
     */
    protected $_orderSummaryBlock;

    /**
     * @var SelectStoreView
     */
    protected $_selectStoreViewBlock;

    /**
     * @var PaymentMethods
     */
    protected $_paymentMethodsBlock;

    /**
     * @var ShippingMethods
     */
    protected $_shippingMethodsBlock;

    /**
     * Backend abstract block
     *
     * @var Template
     */
    protected $_templateBlock;

    /**
     * Custom constructor
     */
    protected function _init()
    {
        $this->_url = $_ENV['app_backend_url'] . self::MCA;
        $this->_orderCustomerBlock = Factory::getBlockFactory()->getMagentoSalesBackendOrderCustomerSelectionGrid(
            $this->_browser->find('#order-customer-selector')
        );
        $this->_itemsOrderedGrid = Factory::getBlockFactory()->getMagentoSalesBackendOrderProductsOrderedGrid(
            $this->_browser->find('#order-items')
        );
        $this->_itemsAddGrid = Factory::getBlockFactory()->getMagentoSalesBackendOrderProductsAddGrid(
            $this->_browser->find('#order-search')
        );
        $this->_billingAddressForm = Factory::getBlockFactory()->getMagentoSalesBackendOrderBillingAddress(
            $this->_browser->find('#order-billing_address')
        );
        $this->_shippingAddressForm = Factory::getBlockFactory()->getMagentoSalesBackendOrderShippingAddress(
            $this->_browser->find('#order-shipping_address')
        );
        $this->_orderSummaryBlock = Factory::getBlockFactory()->getMagentoSalesBackendOrderOrderCreationSummary(
            $this->_browser->find('.order-summary')
        );
        $this->_selectStoreViewBlock = Factory::getBlockFactory()->getMagentoSalesBackendOrderSelectStoreView(
            $this->_browser->find('#order-store-selector')
        );
        $this->_paymentMethodsBlock = Factory::getBlockFactory()->getMagentoSalesBackendOrderPaymentMethods(
            $this->_browser->find('.order-billing-method')
        );
        $this->_shippingMethodsBlock = Factory::getBlockFactory()->getMagentoSalesBackendOrderShippingMethods(
            $this->_browser->find('.order-shipping-method')
        );
        $this->_templateBlock = Factory::getBlockFactory()->getMagentoBackendTemplate(
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
        return $this->_orderCustomerBlock;
    }

    /**
     * Getter for order selected products grid
     *
     * @return ProductsOrderedGrid
     */
    public function getItemsOrderedGrid()
    {
        return $this->_itemsOrderedGrid;
    }

    /**
     * Getter for order select products grid
     *
     * @return ProductsAddGrid
     */
    public function getItemsAddGrid()
    {
        return $this->_itemsAddGrid;
    }

    /**
     * Getter for customer order billing address form
     *
     * @return BillingAddress
     */
    public function getBillingAddressForm()
    {
        return $this->_billingAddressForm;
    }

    /**
     * Getter for customer order shipping address form
     *
     * @return ShippingAddress
     */
    public function getShippingAddressForm()
    {
        return $this->_shippingAddressForm;
    }

    /**
     * Getter for order summary block
     *
     * @return OrderCreationSummary
     */
    public function getOrderSummaryBlock()
    {
        return $this->_orderSummaryBlock;
    }

    /**
     * Getter for store view selection
     *
     * @return SelectStoreView
     */
    public function getSelectStoreViewBlock()
    {
        return $this->_selectStoreViewBlock;
    }

    /**
     * Getter for payment methods block
     *
     * @return PaymentMethods
     */
    public function getPaymentMethodsBlock()
    {
        return $this->_paymentMethodsBlock;
    }

    /**
     * Getter for shipping methods block
     *
     * @return ShippingMethods
     */
    public function getShippingMethodsBlock()
    {
        return $this->_shippingMethodsBlock;
    }

    /**
     * Get abstract block
     *
     * @return \Magento\Backend\Test\Block\Template
     */
    public function getTemplateBlock()
    {
        return $this->_templateBlock;
    }
}
