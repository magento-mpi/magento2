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

namespace Magento\Paypal\Test\Block\Express;

use Mtf\Block\Block;
use Mtf\Factory\Factory;
use Mtf\Client\Element\Locator;
use Magento\Paypal\Test\Block\Express;
use Magento\Checkout\Test\Fixture\Checkout;

/**
 * Class Review
 * Paypal Express Onepage checkout block
 *
 * @package Magento\Paypal\Test\Block\Express
 */
class Review extends Block
{
    /**
     * 'Place Order' button
     *
     * @var string
     */
    private $placeOrder;

    /**
     * 'Update Order Data' button
     *
     * @var string
     */
    private $updateOrder;

    /**
     * Shipping methods dropdown
     *
     * @var string
     */
    private $shippingMethod;

    /**
     * Billing address block
     *
     * @var Review\Billing
     */
    private $billingBlock;

    /**
     * Shipping address block
     *
     * @var Review\Shipping
     */
    private $shippingBlock;

    /**
     * Initialize block elements
     */
    protected function _init()
    {
        //Elements
        $this->placeOrder = '#review-button';
        $this->shippingMethod = '#shipping_method';
        $this->updateOrder = '#update-order';

        //Blocks
        $this->billingBlock = Factory::getBlockFactory()->getMagentoPaypalExpressReviewBilling(
            $this->_rootElement->find('#billing-address-form', Locator::SELECTOR_CSS));
        $this->shippingBlock = Factory::getBlockFactory()->getMagentoPaypalExpressReviewShipping(
            $this->_rootElement->find('#shipping-address-form', Locator::SELECTOR_CSS));
    }

    /**
     * Get billing address block
     *
     * @return \Magento\Paypal\Test\Block\Express\Review\Billing
     */
    public function getBillingBlock()
    {
        return $this->billingBlock;
    }

    /**
     * Get shipping address block
     *
     * @return \Magento\Paypal\Test\Block\Express\Review\Shipping
     */
    public function getShippingBlock()
    {
        return $this->shippingBlock;
    }

    /**
     * Verify order information data
     *
     * @param Checkout $fixture
     * @return bool
     */
    public function verifyOrderInformation(Checkout $fixture)
    {
        //TODO assert constraints
        $this->getBillingBlock()->verify($fixture->getBillingAddress());
        $this->getShippingBlock()->verify($fixture->getShippingAddress());
    }

    /**
     * Select shipping method
     *
     * @param Checkout $fixture
     */
    public function selectShippingMethod(Checkout $fixture)
    {
        $shippingMethod = $fixture->getShippingMethods()->getData('fields');
        $this->_rootElement->find($this->shippingMethod, Locator::SELECTOR_CSS, 'select')
            ->setOptionGroupValue($shippingMethod['shipping_service'], $shippingMethod['shipping_method']);
    }

    /**
     * Press 'Update Order Data' button
     */
    public function updateOrder()
    {
        $this->_rootElement->find($this->updateOrder, Locator::SELECTOR_CSS)->click();
    }

    /**
     * Place order
     */
    public function placeOrder()
    {
        $this->waitForElementNotVisible($this->placeOrder . ':disabled');
        $this->_rootElement->find($this->placeOrder, Locator::SELECTOR_CSS)->click();
    }
}
