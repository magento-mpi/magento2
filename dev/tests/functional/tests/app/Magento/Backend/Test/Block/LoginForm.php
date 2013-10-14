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

namespace Magento\Backend\Test\Block;

use Mtf\Block\Form;
use Mtf\Client\Element\Locator;

/**
 * Class LoginForm
 * Login form for backend user
 *
 * @package Magento\Backend\Test\Block
 */
class LoginForm extends Form
{
    /**
     * Submit login form
     */
    public function submit()
    {
        $this->_rootElement->find('[type=submit]', Locator::SELECTOR_CSS)->click();
    }
}
