<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Paypal\Test\Block\Express;

use Magento\Checkout\Test\Fixture\Checkout;
use Magento\Paypal\Test\Block\Express;
use Magento\Shipping\Test\Fixture\Method;
use Mtf\Block\Form;
use Mtf\Client\Element\Locator;

/**
 * Class Review
 * Paypal Express Onepage checkout block
 *
 */
class Review extends Form
{
    /**
     * 'Place Order' button
     *
     * @var string
     */
    protected $placeOrder = '#review-button';

    /**
     * 'Update Order Data' button
     *
     * @var string
     */
    protected $updateOrder = '#update-order';

    /**
     * Shipping methods dropdown
     *
     * @var string
     */
    protected $shippingMethod = '#shipping-method';

    /**
     * Billing address block
     *
     * @var string
     */
    protected $billingBlock = '#billing-address';

    /**
     * Shipping address block
     *
     * @var string
     */
    protected $shippingBlock = '#shipping-address';

    /**
     * Select shipping method
     *
     * @param Method $fixture
     */
    public function selectShippingMethod(Method $fixture)
    {
        $shippingMethod = $fixture->getData('fields');
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
