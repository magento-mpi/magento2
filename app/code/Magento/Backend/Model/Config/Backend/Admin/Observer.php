<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Backend_Model_Config_Backend_Admin_Observer
{
    /**
     * Log out user and redirect him to new admin custom url
     *
     * @SuppressWarnings(PHPMD.ExitExpression)
     */
    public function afterCustomUrlChanged()
    {
        if (is_null(Mage::registry('custom_admin_path_redirect'))) {
            return;
        }

        /** @var $adminSession Magento_Backend_Model_Auth_Session */
        $adminSession = Mage::getSingleton('Magento_Backend_Model_Auth_Session');
        $adminSession->unsetAll();
        $adminSession->getCookie()->delete($adminSession->getSessionName());

        $route = Mage::helper('Magento_Backend_Helper_Data')->getAreaFrontName();

        Mage::app()->getResponse()
            ->setRedirect(Mage::getBaseUrl() . $route)
            ->sendResponse();
        exit(0);
    }
}
