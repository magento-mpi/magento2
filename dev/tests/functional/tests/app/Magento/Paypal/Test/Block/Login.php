<?php
/**
 * {license_notice}
 *
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
     * 'Pay with my PayPal account' section
     *
     * @var string
     */
    protected $loginSection = '[class$=":ClickLogin"]';

    /**
     * Login to Paypal account
     *
     * @param Customer $fixture
     * @SuppressWarnings(PHPMD.ConstructorWithNameAsEnclosingClass)
     */
    public function login(Customer $fixture)
    {
        $loginSection = $this->_rootElement->find($this->loginSection);
        if ($loginSection->isVisible()) {
            $loginSection->click();
        }
        $this->waitForElementVisible($this->submitLogin);
        $this->fill($fixture);
        $this->_rootElement->find($this->submitLogin, Locator::SELECTOR_CSS)->click();
    }
}
