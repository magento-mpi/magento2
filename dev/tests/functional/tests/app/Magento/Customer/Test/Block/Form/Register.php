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
     * Create Account button
     *
     * @var string
     */
    protected $submit = '.action.submit';

    /**
     * Click on Create Account button
     */
    public function registerCustomer()
    {
        $this->_rootElement->find($this->submit)->click();
    }
}