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
namespace Magento\GoogleCheckout\Controller;

class Redirect extends \Magento\Core\Controller\Front\Action
{
    /**
     *  Send request to Google Checkout and return Response Api
     *
     *  @return \Magento\GoogleCheckout\Model\Api\Xml\Checkout
     */
    protected function _getApi ()
    {
        $session = \Mage::getSingleton('Magento\Checkout\Model\Session');
        $api = \Mage::getModel('\Magento\GoogleCheckout\Model\Api');
        /* @var $quote \Magento\Sales\Model\Quote */
        $quote = $session->getQuote();

        if (!$quote->hasItems()) {
            $this->getResponse()->setRedirect(\Mage::getUrl('checkout/cart'));
            $api->setError(true);
        }

        $storeQuote = \Mage::getModel('\Magento\Sales\Model\Quote')->setStoreId(\Mage::app()->getStore()->getId());
        $storeQuote->merge($quote);
        $storeQuote
            ->setItemsCount($quote->getItemsCount())
            ->setItemsQty($quote->getItemsQty())
            ->setChangedFlag(false);
        $storeQuote->save();

        $baseCurrency = $quote->getBaseCurrencyCode();
        $currency = \Mage::app()->getStore($quote->getStoreId())->getBaseCurrency();


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
                \Mage::getSingleton('Magento\Checkout\Model\Session')->addError($api->getError());
            } else {
                $quote->setIsActive(false)->save();
                $session->replaceQuote($storeQuote);
                \Mage::getModel('\Magento\Checkout\Model\Cart')->init()->save();
                if (\Mage::getStoreConfigFlag('google/checkout/hide_cart_contents')) {
                    $session->setGoogleCheckoutQuoteId($session->getQuoteId());
                    $session->setQuoteId(null);
                }
            }
        }
        return $api;
    }

    public function checkoutAction()
    {
        $session = \Mage::getSingleton('Magento\Checkout\Model\Session');
        $this->_eventManager->dispatch('googlecheckout_checkout_before', array('quote' => $session->getQuote()));
        $api = $this->_getApi();

        if ($api->getError()) {
            $url = \Mage::getUrl('checkout/cart');
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
            $this->getResponse()->setRedirect(\Mage::getUrl('checkout/cart'));
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
        if (\Mage::getStoreConfigFlag('google/checkout/hide_cart_contents')) {
            $session = \Mage::getSingleton('Magento\Checkout\Model\Session');
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
        $session = \Mage::getSingleton('Magento\Checkout\Model\Session');

        if ($quoteId = $session->getGoogleCheckoutQuoteId()) {
            $quote = \Mage::getModel('\Magento\Sales\Model\Quote')->load($quoteId)
                ->setIsActive(false)->save();
        }
        $session->clear();

        if (\Mage::getStoreConfigFlag('google/checkout/hide_cart_contents')) {
            $session->setGoogleCheckoutQuoteId(null);
        }

        $url = \Mage::getStoreConfig('google/checkout/continue_shopping_url');
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
     *
     */
    public function redirectLogin()
    {
        $this->setFlag('', 'no-dispatch', true);
        $this->getResponse()->setRedirect(
            \Mage::helper('Magento\Core\Helper\Url')->addRequestParam(
                \Mage::helper('Magento\Customer\Helper\Data')->getLoginUrl(),
                array('context' => 'checkout')
            )
        );
    }

}
