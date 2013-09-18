<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GoogleCheckout
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @category    Magento
 * @package     Magento_GoogleCheckout
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_GoogleCheckout_Controller_Redirect extends Magento_Core_Controller_Front_Action
{
    /**
     *  Send request to Google Checkout and return Response Api
     *
     *  @return Magento_GoogleCheckout_Model_Api_Xml_Checkout
     */
    protected function _getApi ()
    {
        $session = Mage::getSingleton('Magento_Checkout_Model_Session');
        $api = Mage::getModel('Magento_GoogleCheckout_Model_Api');
        /* @var $quote Magento_Sales_Model_Quote */
        $quote = $session->getQuote();

        if (!$quote->hasItems()) {
            $this->getResponse()->setRedirect(Mage::getUrl('checkout/cart'));
            $api->setError(true);
        }

        $storeQuote = Mage::getModel('Magento_Sales_Model_Quote')->setStoreId(Mage::app()->getStore()->getId());
        $storeQuote->merge($quote);
        $storeQuote
            ->setItemsCount($quote->getItemsCount())
            ->setItemsQty($quote->getItemsQty())
            ->setChangedFlag(false);
        $storeQuote->save();

        $baseCurrency = $quote->getBaseCurrencyCode();
        $currency = Mage::app()->getStore($quote->getStoreId())->getBaseCurrency();


        /*
         * Set payment method to google checkout, so all price rules will work out this case
         * and will use right sales rules
         */
        if ($quote->isVirtual()) {
            $quote->getBillingAddress()->setPaymentMethod('googlecheckout');
        } else {
            $quote->getShippingAddress()->setPaymentMethod('googlecheckout');
        }

        $quote->collectTotals()->save();

        if (!$api->getError()) {
            $api = $api->setAnalyticsData($this->getRequest()->getPost('analyticsdata'))
                ->checkout($quote);

            $response = $api->getResponse();
            if ($api->getError()) {
                Mage::getSingleton('Magento_Checkout_Model_Session')->addError($api->getError());
            } else {
                $quote->setIsActive(false)->save();
                $session->replaceQuote($storeQuote);
                Mage::getModel('Magento_Checkout_Model_Cart')->init()->save();
                if ($this->_objectManager->get('Magento_Core_Model_Store_Config')
                    ->getConfigFlag('google/checkout/hide_cart_contents')
                ) {
                    $session->setGoogleCheckoutQuoteId($session->getQuoteId());
                    $session->setQuoteId(null);
                }
            }
        }
        return $api;
    }

    public function checkoutAction()
    {
        $session = Mage::getSingleton('Magento_Checkout_Model_Session');
        $this->_eventManager->dispatch('googlecheckout_checkout_before', array('quote' => $session->getQuote()));
        $api = $this->_getApi();

        if ($api->getError()) {
            $url = Mage::getUrl('checkout/cart');
        } else {
            $url = $api->getRedirectUrl();
        }
        $this->getResponse()->setRedirect($url);
    }

    /**
     * When a customer chooses Google Checkout on Checkout/Payment page
     *
     */
    public function redirectAction()
    {
        $api = $this->_getApi();

        if ($api->getError()) {
            $this->getResponse()->setRedirect(Mage::getUrl('checkout/cart'));
            return;
        } else {
            $url = $api->getRedirectUrl();
            $this->loadLayout();
            $this->getLayout()->getBlock('googlecheckout_redirect')->setRedirectUrl($url);
            $this->renderLayout();
        }
    }

    public function cartAction()
    {
        $hideCartContents = $this->_objectManager->get('Magento_Core_Model_Store_Config')
            ->getConfigFlag('google/checkout/hide_cart_contents');
        if ($hideCartContents) {
            $session = Mage::getSingleton('Magento_Checkout_Model_Session');
            if ($session->getQuoteId()) {
                $session->getQuote()->delete();
            }
            $session->setQuoteId($session->getGoogleCheckoutQuoteId());
            $session->setGoogleCheckoutQuoteId(null);
        }

        $this->_redirect('checkout/cart');
    }

    public function continueAction()
    {
        $session = Mage::getSingleton('Magento_Checkout_Model_Session');

        if ($quoteId = $session->getGoogleCheckoutQuoteId()) {
            $quote = Mage::getModel('Magento_Sales_Model_Quote')->load($quoteId)
                ->setIsActive(false)->save();
        }
        $session->clear();

        $hideCartContents = $this->_objectManager->get('Magento_Core_Model_Store_Config')
            ->getConfigFlag('google/checkout/hide_cart_contents');
        if ($hideCartContents) {
            $session->setGoogleCheckoutQuoteId(null);
        }

        $url = $this->_objectManager->get('Magento_Core_Model_Store_Config')
            ->getConfig('google/checkout/continue_shopping_url');
        if (empty($url)) {
            $this->_redirect('');
        } elseif (substr($url, 0, 4) === 'http') {
            $this->getResponse()->setRedirect($url);
        } else {
            $this->_redirect($url);
        }
    }

    /**
     * Redirect to login page
     */
    public function redirectLogin()
    {
        $this->setFlag('', 'no-dispatch', true);
        $this->getResponse()->setRedirect(
            $this->_objectManager->get('Magento_Core_Helper_Url')->addRequestParam(
                $this->_objectManager->get('Magento_Customer_Helper_Data')->getLoginUrl(),
                array('context' => 'checkout')
            )
        );
    }
}
