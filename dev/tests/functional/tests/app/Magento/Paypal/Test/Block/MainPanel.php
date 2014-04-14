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

/**
 * Class MainPanel
 * MainPanel block on the PayPal page
 *
 * @package Magento\Paypal\Test\Block
 */
class MainPanel extends Form
{
    /**
     * Link back to storefront
     *
     * @var string
     */
    protected $callbackLink = '[name="merchant_return_link"]';

    /**
     * Go back to storefront
     */
    public function clickReturnLink()
    {
        $this->_rootElement->find($this->callbackLink)->click();
    }
}
