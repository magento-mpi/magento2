<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Persistent\Controller\Index;

class SaveMethod extends \Magento\Persistent\Controller\Index
{
    /**
     * Save onepage checkout method to be register
     *
     * @return void
     */
    public function execute()
    {
        if ($this->_getHelper()->isPersistent()) {
            $this->_getHelper()->getSession()->removePersistentCookie();
            if (!$this->_customerSession->isLoggedIn()) {
                $this->_customerSession->setCustomerId(null)->setCustomerGroupId(null);
            }

            $this->_persistentObserver->setQuoteGuest();
        }

        $checkoutUrl = $this->_redirect->getRefererUrl();
        $this->getResponse()->setRedirect($checkoutUrl . (strpos($checkoutUrl, '?') ? '&' : '?') . 'register');
    }
}
