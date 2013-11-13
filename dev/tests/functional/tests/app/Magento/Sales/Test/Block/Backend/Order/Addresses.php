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
     * Form for billing address
     *
     * @var BillingAddress
     */
    protected $billingAddressForm;

    /**
     * Form for shipping address
     *
     * @var ShippingAddress
     */
    protected $shippingAddressForm;

    /**
     * Global page template block
     *
     * @var Template
     */
    protected $templateBlock;

    /**
     * @inheritdoc
     */
    protected function _init()
    {
        $this->billingAddressForm = Factory::getBlockFactory()->getMagentoSalesBackendOrderBillingAddress(
            $this->_rootElement->find('#order-billing_address')
        );
        $this->shippingAddressForm = Factory::getBlockFactory()->getMagentoSalesBackendOrderShippingAddress(
            $this->_rootElement->find('#order-shipping_address')
        );
        $this->templateBlock = Factory::getBlockFactory()->getMagentoBackendTemplate(
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
        $this->shippingAddressForm->uncheckSameAsBillingShippingAddress();
        $this->templateBlock->waitLoader();
        $billingAddress = $fixture->getBillingAddress();
        if (empty($billingAddress)) {
            $this->billingAddressForm->fill($fixture->getCustomer()->getDefaultBillingAddress());
        } else {
            $this->billingAddressForm->fill($billingAddress);
        }
        $this->templateBlock->waitLoader();
        $this->shippingAddressForm->setSameAsBillingShippingAddress();
        $this->templateBlock->waitLoader();
    }
}
