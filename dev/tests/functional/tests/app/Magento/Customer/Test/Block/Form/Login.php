<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\Customer\Test\Block\Form;

use Mtf\Block\Form;
use Mtf\Client\Element\Locator;
use Mtf\Fixture\FixtureInterface;

/**
 * Class Login
 * Form for frontend login
 */
class Login extends Form
{
    /**
     * Login button for registered customers
     *
     * @var string
     */
    protected $loginButton = '.action.login';

    /**
     * 'Register' customer button
     *
     * @var string
     */
    protected $registerButton = '.action.create';

    /**
     * Login customer in the Frontend
     *
     * @param FixtureInterface $customer
     *
     * @SuppressWarnings(PHPMD.ConstructorWithNameAsEnclosingClass)
     */
    public function login(FixtureInterface $customer)
    {
        $this->fill($customer);
        $this->submit();
        $this->waitForElementNotVisible($this->loginButton, Locator::SELECTOR_CSS);
    }

    /**
     * Submit login form
     */
    public function submit()
    {
        $this->_rootElement->find($this->loginButton, Locator::SELECTOR_CSS)->click();
    }

    /**
     * Press 'Register' button
     */
    public function registerCustomer()
    {
        $this->_rootElement->find($this->registerButton, Locator::SELECTOR_CSS)->click();
    }

    /**
     * Check whether block is visible
     *
     * @return bool
     */
    public function isVisible()
    {
        return $this->_rootElement->isVisible();
    }
}
