<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Checkout\Controller\Onepage;

class Index extends \Magento\Checkout\Controller\Onepage
{
    /**
     * Checkout page
     *
     * @return void
     */
    public function execute()
    {
        if (!$this->_objectManager->get('Magento\Checkout\Helper\Data')->canOnepageCheckout()) {
            $this->messageManager->addError(__('The onepage checkout is disabled.'));
            $this->_redirect('checkout/cart');
            return;
        }
        $quote = $this->getOnepage()->getQuote();
        if (!$quote->hasItems() || $quote->getHasError() || !$quote->validateMinimumAmount()) {
            $this->_redirect('checkout/cart');
            return;
        }

        $this->_objectManager->get('Magento\Checkout\Model\Session')->setCartWasUpdated(false);
        $currentUrl = $this->_objectManager->create('Magento\Framework\UrlInterface')
            ->getUrl(
                '*/*/*',
                array('_secure' => true)
            );
        $this->_objectManager->get('Magento\Customer\Model\Session')->setBeforeAuthUrl($currentUrl);
        $this->getOnepage()->initCheckout();
        $this->_view->loadLayout();
        $layout = $this->_view->getLayout();
        $layout->initMessages();
        $layout->getBlock('head')->setTitle(__('Checkout'));
        $this->_view->renderLayout();
    }
}
