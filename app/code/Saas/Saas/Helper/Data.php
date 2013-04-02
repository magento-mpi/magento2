<?php
/**
 * Saas helper data
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_Saas_Helper_Data
{
    /**
     * @var Mage_Core_Model_StoreManager
     */
    protected $_storeManager;

    /**
     * @var Mage_Core_Model_Config
     */
    protected $_config;

    /**
     * @param Mage_Core_Model_StoreManager $storeManage
     * @param Mage_Core_Model_Config $config
     */
    public function __construct(Mage_Core_Model_StoreManager $storeManage, Mage_Core_Model_Config $config)
    {
        $this->_storeManager = $storeManage;
        $this->_config = $config;
    }

    /**
     * Customize a request and if needed CurrentStore for noRout forward
     *
     * @param Mage_Core_Controller_Request_Http $request
     * @see Mage_Core_Controller_Varien_Router_Default::match
     */
    public function customizeNoRoutForward(Mage_Core_Controller_Request_Http $request)
    {
        $noRoute        = explode('/', $this->_storeManager->getStore()->getConfig('web/default/no_route'));
        $moduleName     = isset($noRoute[0]) && $noRoute[0] ? $noRoute[0] : 'core';
        $controllerName = isset($noRoute[1]) ? $noRoute[1] : 'index';
        $actionName     = isset($noRoute[2]) ? $noRoute[2] : 'index';

        if ($this->_storeManager->getStore()->isAdmin()) {
            $adminFrontName = (string)$this->_config->getNode('admin/routers/adminhtml/args/frontName');
            if ($adminFrontName != $moduleName) {
                $moduleName     = 'core';
                $controllerName = 'index';
                $actionName     = 'noRoute';
                $this->_storeManager->setCurrentStore($this->_storeManager->getDefaultStoreView());
            }
        }

        $request->initForward()
            ->setModuleName($moduleName)
            ->setControllerName($controllerName)
            ->setActionName($actionName)
            ->setDispatched(false);
    }
}
