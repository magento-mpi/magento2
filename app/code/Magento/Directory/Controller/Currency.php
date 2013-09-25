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
class Magento_Directory_Controller_Currency extends Magento_Core_Controller_Front_Action
{
    public function switchAction()
    {
        /** @var Magento_Core_Model_StoreManagerInterface $storeManager */
        $storeManager = $this->_objectManager->get('Magento_Core_Model_StoreManagerInterface');
        $currency = (string)$this->getRequest()->getParam('currency');
        if ($currency) {
            $storeManager->getStore()->setCurrentCurrencyCode($currency);
        }
        $this->_redirectReferer($storeManager->getStore()->getBaseUrl());
    }
}
