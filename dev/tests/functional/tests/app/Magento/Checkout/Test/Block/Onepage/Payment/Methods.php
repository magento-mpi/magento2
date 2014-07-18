<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Checkout\Test\Block\Onepage\Payment;

use Mtf\Block\Form;
use Mtf\Client\Element\Locator;
use Magento\Checkout\Test\Fixture\Checkout;

/**
 * Class Methods
 * One page checkout status payment method block
 *
 */
class Methods extends Form
{
    /**
     * Payment method selector
     *
     * @var string
     */
    protected $paymentMethod = '[for=p_method_%s]';

    /**
     * Continue checkout button
     *
     * @var string
     */
    protected $continue = '#payment-buttons-container button';

    /**
     * Wait element
     *
     * @var string
     */
    protected $waitElement = '.loading-mask';

    /**
     * Array with store credit and reward points selectors
     *
     * @var array
     */
    protected $pointsSelectors = [
        'storeCredit' => '#customerbalance-available-amount',
        'rewardPoints' => '[name="payment[use_reward_points]"]',
    ];

    /**
     * Select payment method
     *
     * @param Checkout $fixture
     */
    public function selectPaymentMethod(Checkout $fixture)
    {
        $payment = $fixture->getPaymentMethod();
        $paymentCode = $payment->getPaymentCode();
        $this->_rootElement->find(sprintf($this->paymentMethod, $paymentCode), Locator::SELECTOR_CSS)->click();

        $dataConfig = $payment->getDataConfig();
        if (isset($dataConfig['payment_form_class'])) {
            $paymentFormClass = $dataConfig['payment_form_class'];
            /** @var $formBlock \Magento\Payment\Test\Block\Form\Cc */
            $formBlock = new $paymentFormClass(
                $this->_rootElement->find('#payment_form_' . $paymentCode),
                $this->blockFactory,
                $this->mapper,
                $this->browser
            );
            $formBlock->fill($fixture);
        }

        $this->_rootElement->find($this->continue, Locator::SELECTOR_CSS)->click();
        $this->waitForElementNotVisible($this->waitElement);
    }

    /**
     * Select payment method
     *
     * @param $paymentCode
     */
    public function clickOnPaymentMethod($paymentCode)
    {
        $this->applyCustomerPoints();
        $this->_rootElement->find(sprintf($this->paymentMethod, $paymentCode), Locator::SELECTOR_CSS)->click();
        if ($paymentCode == 'purchaseorder' && $this->_rootElement->find('#po_number')->isVisible()) {
            $this->_rootElement->find('#po_number')->setValue(rand(1000000, 9999999));
        }
    }

    /**
     * Press "Continue" button
     */
    public function pressContinue()
    {
        $this->_rootElement->find($this->continue, Locator::SELECTOR_CSS)->click();
        $this->waitForElementNotVisible($this->waitElement);
    }

    /**
     * Apply reward points or store credit
     *
     * @return void
     */
    protected function applyCustomerPoints()
    {
        foreach ($this->pointsSelectors as $selector) {
            $checkBox = $this->_rootElement->find($selector);
            if ($checkBox->isVisible()) {
                $checkBox->click();
            }
        }
    }
}
