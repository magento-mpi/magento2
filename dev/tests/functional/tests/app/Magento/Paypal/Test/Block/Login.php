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

namespace Magento\Paypal\Test\Block;

use Mtf\Block\Form;
use Mtf\Client\Element\Locator;
use Magento\Paypal\Test\Fixture\Customer;

/**
 * Class Login
 * Login to paypal account
 *
 * @package Magento\Paypal\Test\Block
 */
class Login extends Form
{
    /**
     * Submit login button
     *
     * @var string
     */
    protected $submitLogin = '#submitLogin';

    /**
     * Login to Paypal account
     *
     * @param Customer $fixture
     */
    public function login(Customer $fixture)
    {
        $this->waitForElementVisible($this->submitLogin);
        $this->fill($fixture);
        $this->_rootElement->find($this->submitLogin, Locator::SELECTOR_CSS)->click();
    }
}
