<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Paypal\Test\Block;

use Mtf\Block\Form;

/**
 * Class Billing
 * Billing block on the PayPal page
 *
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
        $loginLink = $this->_rootElement->find($this->loadLogin);
        if ($loginLink->isVisible()) {
            $loginLink->click();
        }
    }
}
