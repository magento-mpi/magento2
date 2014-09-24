<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Checkout\Test\Block\Onepage;

use Mtf\Block\Form;
use Magento\Checkout\Test\Fixture\Checkout;
use Mtf\Client\Element\Locator;
use Mtf\Fixture\FixtureInterface;

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
     * Selector for loading mask element
     *
     * @var string
     */
    protected $loadingMask = '.loading-mask';

    /**
     * Select how to perform checkout whether guest or registered customer
     *
     * @param Checkout $fixture
     * @return void
     */
    public function checkoutMethod(Checkout $fixture)
    {
        if ($fixture->isRegisteredCustomer()) {
            $this->loginCustomer($fixture->getCustomer());
        } elseif ($fixture->getCustomer()) {
            $this->registerCustomer();
            $this->clickContinue();
        } else {
            $this->guestCheckout();
            $this->clickContinue();
        }
    }

    /**
     * Perform guest checkout
     *
     * @return void
     */
    public function guestCheckout()
    {
        $this->_rootElement->find($this->guestCheckout)->click();
    }

    /**
     * Register customer during checkout
     *
     * @return void
     */
    public function registerCustomer()
    {
        $this->_rootElement->find($this->registerCustomer)->click();
        $this->_rootElement->find($this->continue, Locator::SELECTOR_CSS)->click();
        $this->waitForElementNotVisible($this->loadingMask);
    }

    /**
     * Login customer during checkout
     *
     * @param FixtureInterface $customer
     * @return void
     */
    public function loginCustomer(FixtureInterface $customer)
    {
        $this->fill($customer);
        $this->_rootElement->find($this->login)->click();
        $this->waitForElementNotVisible($this->loadingMask);
    }

    /**
     * Click continue on checkout method block
     *
     * @return void
     */
    public function clickContinue()
    {
        $this->_rootElement->find($this->continue)->click();
        $this->waitForElementNotVisible($this->loadingMask);
    }
}
