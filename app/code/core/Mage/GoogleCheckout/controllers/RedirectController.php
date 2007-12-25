<?php

class Mage_GoogleCheckout_RedirectController extends Mage_Core_Controller_Front_Action
{
    public function checkoutAction()
    {
        $quote = Mage::getSingleton('checkout/session')->getQuote();
        $api = Mage::getModel('googlecheckout/api')
            ->checkoutShoppingCart($quote);

        $response = $api->getResponse();
        if ($api->getError()) {
            Mage::getSingleton('checkout/session')->addError($api->getError());
            $url = Mage::getUrl('checkout/cart');
        } else {
            $url = $api->getRedirectUrl();
        }
        $this->getResponse()->setRedirect($url);
    }
}