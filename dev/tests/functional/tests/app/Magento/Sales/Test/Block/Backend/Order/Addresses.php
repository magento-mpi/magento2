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

namespace Magento\Sales\Test\Block\Backend\Order;

use Magento\Backend\Test\Block\Template;
use Magento\Sales\Test\Fixture\Order;
use Mtf\Block\Block;
use Mtf\Client\Element\Locator;
use Mtf\Factory\Factory;

/**
 * Block for encapsulating all work with addresses in backend order creation
 *
 * @package Magento\Sales\Test\Block\Backend\Order
 */
class Addresses extends Block
{
    /**
     * @var BillingAddress
     */
    protected $_billingAddressForm;

    /**
     * @var ShippingAddress
     */
    protected $_shippingAddressForm;

    /**
     * @var Template
     */
    protected $_templateBlock;

    /**
     * @inheritdoc
     */
    protected function _init()
    {
        $this->_billingAddressForm = Factory::getBlockFactory()->getMagentoSalesBackendOrderBillingAddress(
            $this->_rootElement->find('#order-billing_address')
        );
        $this->_shippingAddressForm = Factory::getBlockFactory()->getMagentoSalesBackendOrderShippingAddress(
            $this->_rootElement->find('#order-shipping_address')
        );
        $this->_templateBlock = Factory::getBlockFactory()->getMagentoBackendTemplate(
            $this->_rootElement->find('./ancestor::body', Locator::SELECTOR_XPATH)
        );
    }

    /**
     * Fill addresses based on present data in customer and order fixtures
     *
     * @param Order $fixture
     */
    public function fillAddresses(Order $fixture)
    {
        $this->_billingAddressForm ->fill($fixture->getBillingAddress());
        $this->_templateBlock->waitLoader();
        $this->_shippingAddressForm->setSameAsBillingShippingAddress();
        $this->_templateBlock->waitLoader();
    }
}
