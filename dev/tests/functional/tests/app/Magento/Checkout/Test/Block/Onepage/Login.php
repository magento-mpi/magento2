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

use Mtf\Block\Block;
use Mtf\Client\Element\Locator;
use Magento\Checkout\Test\Fixture\Checkout;

/**
 * Class Login
 * One page checkout status
 *
 * @package Magento\Checkout\Test\Block\Onepage
 */
class Login extends Block
{
    /**
     * Continue checkout button
     *
     * @var string
     */
    private $continue;

    /**
     * 'Checkout as Guest' radio button
     *
     * @var string
     */
    private $guestCheckout;

    /**
     * 'Register' radio button
     *
     * @var string
     */
    private $registerCustomer;

    /**
     * Initialize block elements
     */
    protected function _init()
    {
        $this->continue = '#onepage-guest-register-button';
        $this->guestCheckout = '[id="login:guest"]';
        $this->registerCustomer = '[id="login:register"]';
    }

    /**
     * Select how to perform checkout whether guest or registered customer
     *
     * @param Checkout $fixture
     */
    public function checkoutMethod(Checkout $fixture)
    {
        if ($fixture->getCustomer()) {
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
}
