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
 * Customer create form for frontend
 *
 * @package Magento\Customer\Test\Block\Form
 */
class Create extends Form
{
    /**
     * Submit button
     *
     * @var string
     */
    protected $submitButton = '.action.submit';

    /**
     * Mapping for field locator
     *
     * @var array
     */
    protected $_mapping = array(
        'firstname' => '#firstname',
        'lastname' => '#lastname',
        'email' => '#email_address',
        'password' => '#password',
        'confirmation' => '#confirmation'
    );


    /**
     * Fill form with customer data and submit
     *
     * @param Customer $fixture
     */
    public function create(Customer $fixture)
    {
        $this->fill($fixture);
        $this->submit();
    }

    /**
     * Submit login form
     */
    public function submit()
    {
        $this->_rootElement->find($this->submitButton, Locator::SELECTOR_CSS)->click();
    }
}
