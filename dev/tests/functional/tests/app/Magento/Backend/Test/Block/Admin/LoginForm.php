<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Backend\Test\Block\Admin;

use Mtf\Block\Form;

/**
 * Class Login
 * Login form for backend user
 *
 * @package Magento\Backend\Test\Block\Admin
 */
class LoginForm extends Form{
    /**
     * 'Log in' button
     *
     * @var string
     */
    protected $submit = '[type="submit"]';

    /**
     * Click on Log in button
     */
    public function submit()
    {
        $this->_rootElement->find($this->submit)->click();
    }
} 