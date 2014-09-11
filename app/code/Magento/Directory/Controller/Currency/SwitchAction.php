<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Directory\Controller\Currency;

class SwitchAction extends \Magento\Framework\App\Action\Action
{
    /**
     * @return void
     */
    public function execute()
    {
        /** @var \Magento\Framework\StoreManagerInterface $storeManager */
        $storeManager = $this->_objectManager->get('Magento\Framework\StoreManagerInterface');
        $currency = (string)$this->getRequest()->getParam('currency');
        if ($currency) {
            $storeManager->getStore()->setCurrentCurrencyCode($currency);
        }
        $storeUrl = $storeManager->getStore()->getBaseUrl();
        $this->getResponse()->setRedirect($this->_redirect->getRedirectUrl($storeUrl));
    }
}
