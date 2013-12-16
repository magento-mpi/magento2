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

namespace Magento\Customer\Test\Block\Form;

use Mtf\Block\Form;
use Mtf\Client\Element;
use Mtf\Client\Element\Locator;
use Magento\Customer\Test\Fixture\Customer;

/**
 * Class Login
 * Form for frontend login
 *
 * @package Magento\Customer\Test\Block\Form
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
     * {@inheritdoc}
     */
    protected $_mapping = array(
        'email' => '#email',
        'password' => '#pass'
    );

    /**
     * Fill customer login data
     *
     * @param array $fields
     * @param Element $element
     */
    protected function _fill(array $fields, Element $element = null)
    {
        $loginData = array(
            $fields['email'],
            $fields['password']
        );
        parent::_fill($loginData, $element);
    }

    /**
     * Login customer in the Frontend
     *
     * @param Customer $fixture
     */
    public function login(Customer $fixture)
    {
        $this->fill($fixture);
        $this->submit();
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
