<?php
/**
 * {license_notice}
 *   
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Test\Block\Form;

use Mtf\Block\Form;
use Mtf\Client\Element\Locator;
use Magento\Customer\Test\Fixture\Customer;

/**
 */
class ForgotPassword extends Form
{
    /**
     * 'Submit' form button
     *
     * @var string
     */
    protected $submit = '.action.submit';

    /**
     * Fill and submit form
     *
     * @param Customer $fixture
     */
    public function resetForgotPassword(Customer $fixture)
    {
        $this->fill($fixture);
        $this->_rootElement->find($this->submit, Locator::SELECTOR_CSS)->click();
    }
}
