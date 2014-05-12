<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Currency controller
 */
namespace Magento\Directory\Controller;

class Currency extends \Magento\Framework\App\Action\Action
{
    /**
     * @return void
     */
    public function switchAction()
    {
        /** @var \Magento\Store\Model\StoreManagerInterface $storeManager */
        $storeManager = $this->_objectManager->get('Magento\Store\Model\StoreManagerInterface');
        $currency = (string)$this->getRequest()->getParam('currency');
        if ($currency) {
            $storeManager->getStore()->setCurrentCurrencyCode($currency);
        }
        $storeUrl = $storeManager->getStore()->getBaseUrl();
        $this->getResponse()->setRedirect($this->_redirect->getRedirectUrl($storeUrl));
    }
}
