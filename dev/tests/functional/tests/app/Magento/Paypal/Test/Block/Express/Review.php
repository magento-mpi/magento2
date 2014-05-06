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
use Mtf\Fixture\DataFixture;
use Magento\Paypal\Test\Block\Express;
use Magento\Checkout\Test\Fixture\Checkout;
use Magento\Shipping\Test\Fixture\Method;

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
    protected $shippingMethod = '#shipping_method';

    /**
     * Billing address block
     *
     * @var string
     */
    protected $billingBlock ='#billing-address';

    /**
     * Shipping address block
     *
     * @var string
     */
    protected $shippingBlock = '#shipping-address';

    /**
     * Get billing address block
     *
     * @return \Magento\Paypal\Test\Block\Express\Review\Billing
     */
    public function getBillingBlock()
    {
        return Factory::getBlockFactory()->getMagentoPaypalExpressReviewBilling(
            $this->_rootElement->find($this->billingBlock, Locator::SELECTOR_CSS)
        );
    }

    /**
     * Get shipping address block
     *
     * @return \Magento\Paypal\Test\Block\Express\Review\Shipping
     */
    public function getShippingBlock()
    {
        return Factory::getBlockFactory()->getMagentoPaypalExpressReviewShipping(
            $this->_rootElement->find($this->shippingBlock, Locator::SELECTOR_CSS)
        );
    }

    /**
     * Verify order information data
     *
     * @param Checkout $fixture
     * @return bool
     */
    public function verifyOrderInformation(Checkout $fixture)
    {
        return $this->verifyForm($fixture->getBillingAddress(), $this->getBillingBlock())
            && $this->verifyForm($fixture->getShippingAddress(), $this->getShippingBlock());
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
        $data = $this->dataMapping($telephone);
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

    /**
     * Verify that fixture is equals data on form
     *
     * @param DataFixture $fixture
     * @param Form $form
     * @return bool
     */
    protected function verifyForm(DataFixture $fixture, Form $form)
    {
        $data = $fixture->getData();
        $preparedData = [];
        $formData = $form->getData($fixture);

        foreach ($data['fields'] as $key => $field) {
            $preparedData[$key] = $field['value'];
        }

        $dataDiff = array_diff($preparedData, $formData);
        return 0 === count($dataDiff);
    }
}
