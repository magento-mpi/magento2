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

namespace Magento\Backend\Test\Block\Admin;

use Mtf\Block\Form;
use Mtf\Client\Element\Locator;
use Mtf\Client\Element;

/**
 * Class Login
 * Login form for backend user
 *
 * @package Magento\Backend\Test\Block\Admin
 */
class Login extends Form
{
    /**
     * 'Log in' button
     *
     * @var string
     */
    protected $submit = '[type=submit]';

    /**
     * Submit login form
     */
    public function submit()
    {
        $this->_rootElement->find($this->submit, Locator::SELECTOR_CSS)->click();
    }
}
