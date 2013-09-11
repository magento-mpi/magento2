<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Backend\Model\Config\Backend\Admin;

class Observer
{
    /**
     * Log out user and redirect him to new admin custom url
     *
     * @SuppressWarnings(PHPMD.ExitExpression)
     */
    public function afterCustomUrlChanged()
    {
        if (is_null(\Mage::registry('custom_admin_path_redirect'))) {
            return;
        }

        /** @var $adminSession \Magento\Backend\Model\Auth\Session */
        $adminSession = \Mage::getSingleton('Magento\Backend\Model\Auth\Session');
        $adminSession->unsetAll();
        $adminSession->getCookie()->delete($adminSession->getSessionName());

        $route = \Mage::helper('Magento\Backend\Helper\Data')->getAreaFrontName();

        \Mage::app()->getResponse()
            ->setRedirect(\Mage::getBaseUrl() . $route)
            ->sendResponse();
        exit(0);
    }
}
