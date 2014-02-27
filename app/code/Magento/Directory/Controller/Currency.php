<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Directory
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Currency controller
 */
namespace Magento\Directory\Controller;

class Currency extends \Magento\App\Action\Action
{
    /**
     * @return void
     */
    public function switchAction()
    {
        /** @var \Magento\Core\Model\StoreManagerInterface $storeManager */
        $storeManager = $this->_objectManager->get('Magento\Core\Model\StoreManagerInterface');
        $currency = (string)$this->getRequest()->getParam('currency');
        if ($currency) {
            $storeManager->getStore()->setCurrentCurrencyCode($currency);
        }
        $storeUrl = $storeManager->getStore()->getBaseUrl();
        $this->getResponse()->setRedirect($this->_redirect->getRedirectUrl($storeUrl));
    }
}
