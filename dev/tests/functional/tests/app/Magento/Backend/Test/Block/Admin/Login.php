<?php
/**
 * {license_notice}
 *
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
