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

namespace Magento\Checkout\Test\Block\Onepage;

use Mtf\Block\Form;
use Mtf\Client\Element\Locator;
use Magento\Checkout\Test\Fixture\Checkout;

/**
 * Class Login
 * One page checkout status login block
 *
 * @package Magento\Checkout\Test\Block\Onepage
 */
class Login extends Form
{
    /**
     * Mapping for field locator
     *
     * @var array
     */
    protected $_mapping = array(
        'email' => '[id="login-email"]',
        'password' => '[id="login-password"]',
    );

    /**
     * Email field
     *
     * @var string
     */
    protected $email = '[id="login-email"]';

    /**
     * Password field
     *
     * @var string
     */
    protected $password = '[id="login-password"]';

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
     */
    public function checkoutMethod(Checkout $fixture)
    {
        if ($fixture->isRegisteredCustomer()) {
            $this->loginCustomer($fixture);
        }
        else if ($fixture->getCustomer()) {
            $this->registerCustomer();
        } else {
            $this->guestCheckout();
        }
    }

    /**
     * Perform guest checkout
     */
    private function guestCheckout()
    {
        $this->_rootElement->find($this->guestCheckout, Locator::SELECTOR_CSS)->click();
        $this->_rootElement->find($this->continue, Locator::SELECTOR_CSS)->click();
        $this->waitForElementNotVisible('.please-wait');
    }

    /**
     * Register customer during checkout
     */
    private function registerCustomer()
    {
        $this->_rootElement->find($this->registerCustomer, Locator::SELECTOR_CSS)->click();
        $this->_rootElement->find($this->continue, Locator::SELECTOR_CSS)->click();
        $this->waitForElementNotVisible('.please-wait');
    }

    /**
     * Login customer during checkout
     *
     * @param Checkout $fixture
     */
    private function loginCustomer(Checkout $fixture)
    {
        $customer = $fixture->getCustomer();
        $this->_rootElement->find(
            $this->email, Locator::SELECTOR_CSS)->setValue($customer->getEmail()
        );
        $this->_rootElement->find(
            $this->password, Locator::SELECTOR_CSS)->setValue($customer->getPassword()
        );
        $this->_rootElement->find($this->login, Locator::SELECTOR_CSS)->click();
        $this->waitForElementNotVisible('.please-wait');
    }
}
