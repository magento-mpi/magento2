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
use Mtf\Client\Element\Locator;
use Magento\Customer\Test\Fixture\Customer;

/**
 * Class Register
 * Register new customer on Frontend
 *
 * @package Magento\Customer\Test\Block\Form
 */
class Register extends Form
{
    /**
     * 'Submit' form button
     *
     * @var string
     */
    protected $submit = '.action.submit';

    /**
     * Fill billing address
     *
     * @param Customer $fixture
     */
    public function registerCustomer(Customer $fixture)
    {
        $this->fill($fixture);
        $this->fill($fixture->getDefaultBillingAddress());
        $this->_rootElement->find($this->submit, Locator::SELECTOR_CSS)->click();
    }
}
