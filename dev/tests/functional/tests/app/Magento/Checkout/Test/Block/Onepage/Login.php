<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Checkout\Test\Block\Onepage;

use Mtf\Block\Form;
use Mtf\Client\Element\Locator;
use Magento\Checkout\Test\Fixture\Checkout;

/**
 * Class Login
 * One page checkout status login block
 */
class Login extends Form
{
    /**
     * Login button
     *
     * @var string
     */
    protected $login = '[data-action=checkout-method-login]';

    /**
     * Continue checkout button
     *
     * @var string
     */
    protected $continue = '#onepage-guest-register-button';

    /**
     * 'Checkout as Guest' radio button
     *
     * @var string
     */
    protected $guestCheckout = '[id="login:guest"]';

    /**
     * 'Register' radio button
     *
     * @var string
     */
    protected $registerCustomer = '[id="login:register"]';

    /**
     * Select how to perform checkout whether guest or registered customer
     *
     * @param Checkout $fixture
     * @return void
     */
    public function checkoutMethod(Checkout $fixture)
    {
        if ($fixture->isRegisteredCustomer()) {
            $this->loginCustomer($fixture);
        } elseif ($fixture->getCustomer()) {
            $this->registerCustomer();
        } else {
            $this->guestCheckout();
        }
    }

    /**
     * Perform guest checkout
     *
     * @return void
     */
    public function guestCheckout()
    {
        $this->_rootElement->find($this->guestCheckout, Locator::SELECTOR_CSS)->click();
        $this->_rootElement->find($this->continue, Locator::SELECTOR_CSS)->click();
        $this->waitForElementNotVisible('.loading-mask');
    }

    /**
     * Register customer during checkout
     *
     * @return void
     */
    private function registerCustomer()
    {
        $this->_rootElement->find($this->registerCustomer, Locator::SELECTOR_CSS)->click();
        $this->_rootElement->find($this->continue, Locator::SELECTOR_CSS)->click();
        $this->waitForElementNotVisible('.loading-mask');
    }

    /**
     * Login customer during checkout
     *
     * @param Checkout $fixture
     * @return void
     */
    private function loginCustomer(Checkout $fixture)
    {
        $customer = $fixture->getCustomer();
        $this->fill($customer);
        $this->_rootElement->find($this->login, Locator::SELECTOR_CSS)->click();
        $this->waitForElementNotVisible('.loading-mask');
    }
}
