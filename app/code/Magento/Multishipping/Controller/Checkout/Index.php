<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Multishipping\Controller\Checkout;

class Index extends \Magento\Multishipping\Controller\Checkout
{
    /**
     * Index action of Multishipping checkout
     *
     * @return void
     */
    public function execute()
    {
        $this->_getCheckoutSession()->setCartWasUpdated(false);
        $this->_redirect('*/*/addresses');
    }
}
