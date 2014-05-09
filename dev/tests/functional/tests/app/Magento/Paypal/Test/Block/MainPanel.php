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
 * Class MainPanel
 * MainPanel block on the PayPal page
 *
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
