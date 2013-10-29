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

use Mtf\Block\Form;
use Mtf\Factory\Factory;
use Mtf\Client\Element\Locator;
use Magento\Paypal\Test\Block\Express;
use Magento\Checkout\Test\Fixture\Checkout;
use Magento\Shipping\Test\Fixture\Method;
use Magento\Customer\Test\Fixture\Address;

/**
 * Class Review
 * Paypal Express Onepage checkout block
 *
 * @package Magento\Paypal\Test\Block\Express
 */
class Review extends Form
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
    protected  $shippingMethod;

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
        $this->_mapping = array(
            'telephone' => '[id="shipping:telephone"]'
        );

        //Blocks
        $this->billingBlock = Factory::getBlockFactory()->getMagentoPaypalExpressReviewBilling(
            $this->_rootElement->find('.fieldset.billing.address', Locator::SELECTOR_CSS));
        $this->shippingBlock = Factory::getBlockFactory()->getMagentoPaypalExpressReviewShipping(
            $this->_rootElement->find('.fieldset.shipping.address', Locator::SELECTOR_CSS));
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
        $this->getBillingBlock()->verify($fixture->getBillingAddress());
        $this->getShippingBlock()->verify($fixture->getShippingAddress());
    }

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
     * Set telephone to form
     *
     * @param array $telephone
     */
    public function fillTelephone(array $telephone)
    {
        $data = $this->dataMapping(array($telephone));
        $this->_fill($data);
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
