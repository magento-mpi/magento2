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
use Mtf\ObjectManager;

/**
 * Class Login
 * Login to paypal account
 */
class LoginExpress extends Form
{
    /**
     * Submit login button
     *
     * @var string
     */
    protected $submitLogin = 'input[type="submit"]';

    /**
     * Login form locator
     *
     * @var string
     */
    protected $loginForm = '#login';

    /**
     * Login form locator
     *
     * @var string
     */
    protected $oldRootLocator = '//*[*[@id="login"] or *[@id="loginBox"]]';

    /**
     * Login to Paypal account
     *
     * @param Customer $fixture
     * @return void
     * @SuppressWarnings(PHPMD.ConstructorWithNameAsEnclosingClass)
     */
    public function login(Customer $fixture)
    {
        // Wait for page to load in order to check logged customer
        $this->_rootElement->find($this->oldRootLocator, Locator::SELECTOR_XPATH)->click();
        // PayPal returns different login pages due to buyer country
        if (!$this->_rootElement->find($this->loginForm)->isVisible()) {
            $payPalLogin = ObjectManager::getInstance()->create(
                '\Magento\Paypal\Test\Block\Login',
                [
                    'element' => $this->browser->find($this->oldRootLocator)
                ]
            );
            $payPalLogin->login($fixture);
            return;
        }
        $loginForm = $this->_rootElement->find($this->loginForm);
        if (!$loginForm->isVisible()) {
            return;
        }

        $this->fill($fixture);
        $loginForm->find($this->submitLogin, Locator::SELECTOR_CSS)->click();
    }
}
