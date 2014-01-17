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
    protected $callbackLink = '//a[contains(@class, "confidential")]';

    /**
     * Go back to storefront
     *
     */
    public function clickReturnLink()
    {
        $this->_rootElement->find($this->callbackLink, Locator::SELECTOR_XPATH)->click();
    }
}
