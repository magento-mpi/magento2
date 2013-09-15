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
     * Backend data
     *
     * @var Magento_Backend_Helper_Data
     */
    protected $_backendData = null;

    /**
     * Core registry
     *
     * @var Magento_Core_Model_Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param Magento_Backend_Helper_Data $backendData
     * @param Magento_Core_Model_Registry $coreRegistry
     */
    public function __construct(
        Magento_Backend_Helper_Data $backendData,
        Magento_Core_Model_Registry $coreRegistry
    ) {
        $this->_backendData = $backendData;
        $this->_coreRegistry = $coreRegistry;
    }

    /**
     * Log out user and redirect him to new admin custom url
     *
     * @SuppressWarnings(PHPMD.ExitExpression)
     */
    public function afterCustomUrlChanged()
    {
        if (is_null($this->_coreRegistry->registry('custom_admin_path_redirect'))) {
            return;
        }

        /** @var $adminSession \Magento\Backend\Model\Auth\Session */
        $adminSession = \Mage::getSingleton('Magento\Backend\Model\Auth\Session');
        $adminSession->unsetAll();
        $adminSession->getCookie()->delete($adminSession->getSessionName());

        $route = $this->_backendData->getAreaFrontName();

        \Mage::app()->getResponse()
            ->setRedirect(\Mage::getBaseUrl() . $route)
            ->sendResponse();
        exit(0);
    }
}
