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

namespace Magento\Paypal\Test\Block;

use Mtf\Block\Form;
use Mtf\Client\Element\Locator;

/**
 * Class Billing
 * Billing block on the PayPal page
 *
 * @package Magento\Paypal\Test\Block
 */
class Billing extends Form
{
    /**
     * Link to PayPal login page
     *
     * @var string
     */
    protected $loadLogin = '#loadLogin';

    /**
     * Go to PayPal login page
     *
     */
    public function clickLoginLink()
    {
        $loginLink = $this->_rootElement->find($this->loadLogin, Locator::SELECTOR_CSS);
        if($loginLink->isVisible()) {
            $loginLink->click();
        }
    }
}
