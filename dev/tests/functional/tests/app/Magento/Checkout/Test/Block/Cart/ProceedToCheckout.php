<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Checkout\Test\Block\Cart;

use Mtf\Block\Block;
use Mtf\Client\Element\Locator;

/**
 * Class ProceedToCheckout
 *
 * Checkout methods items block
 */
class ProceedToCheckout extends Block
{
    /**
     * Proceed To Checkout button selector
     *
     * @var string
     */
    protected $proceedToCheckoutButton = 'button[title="Proceed to Checkout"]';

    /**
     * Click on Proceed To Checkout button
     *
     * @return void
     */
    public function proceedToCheckout()
    {
        $this->_rootElement->find($this->proceedToCheckoutButton)->click();
    }
}
