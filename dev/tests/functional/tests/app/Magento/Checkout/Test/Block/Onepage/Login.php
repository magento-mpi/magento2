<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Checkout\Test\Block\Onepage;

use Magento\Checkout\Test\Fixture\Checkout;
use Mtf\Block\Form;
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
     * @param FixtureInterface $fixture
     * @return void
     */
    public function checkoutMethod(FixtureInterface $fixture)
    {
        /** @var Checkout $fixture */
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
        $browser = $this->browser;
        $selector = $this->loadingMask;
        $browser->waitUntil(
            function () use ($browser, $selector) {
                $element = $browser->find($selector);
                return $element->isVisible() == false ? true : null;
            }
        );
    }
}
