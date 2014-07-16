<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Ogone\Controller\Api;

class Decline extends \Magento\Ogone\Controller\Api
{
    /**
     * When payment got decline
     * need to change order status to cancelled
     * take the user back to shopping cart
     *
     * @return void
     */
    public function execute()
    {
        if (!$this->_validateOgoneData()) {
            $this->_redirect('checkout/cart');
            return;
        }
        $this->_getCheckout()->setQuoteId($this->_getCheckout()->getOgoneQuoteId());
        $this->_declineProcess();
    }
}
