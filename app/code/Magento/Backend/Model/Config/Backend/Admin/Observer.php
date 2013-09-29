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
     * Backend data
     *
     * @var Magento_Backend_Helper_Data
     */
    protected $_backendData;

    /**
     * Core registry
     *
     * @var Magento_Core_Model_Registry
     */
    protected $_coreRegistry;

    /**
     * @var Magento_Backend_Model_Auth_Session
     */
    protected $_authSession;

    /**
     * @var Magento_Core_Model_App
     */
    protected $_app;

    /**
     * @var Magento_Core_Model_StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @param Magento_Backend_Helper_Data $backendData
     * @param Magento_Core_Model_Registry $coreRegistry
     * @param Magento_Backend_Model_Auth_Session $authSession
     * @param Magento_Core_Model_App $app
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     */
    public function __construct(
        Magento_Backend_Helper_Data $backendData,
        Magento_Core_Model_Registry $coreRegistry,
        Magento_Backend_Model_Auth_Session $authSession,
        Magento_Core_Model_App $app,
        Magento_Core_Model_StoreManagerInterface $storeManager
    ) {
        $this->_backendData = $backendData;
        $this->_coreRegistry = $coreRegistry;
        $this->_authSession = $authSession;
        $this->_app = $app;
        $this->_storeManager = $storeManager;
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

        $this->_authSession->unsetAll();
        $this->_authSession->getCookie()->delete($this->_authSession->getSessionName());

        $route = $this->_backendData->getAreaFrontName();

        $this->_app->getResponse()
            ->setRedirect($this->_storeManager->getStore()->getBaseUrl() . $route)
            ->sendResponse();
        exit(0);
    }
}
