<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Core_Model_EntryPoint_Cron extends Magento_Core_Model_EntryPointAbstract
{
    /**
     * Process request to application
     */
    protected function _processRequest()
    {
        /** @var $app Magento_Core_Model_App */
        $app = $this->_objectManager->get('Magento_Core_Model_App');
        $app->setUseSessionInUrl(false);
        $app->requireInstalledInstance();

        /** @var $eventManager Magento_Core_Model_Event_Manager */
        $eventManager = $this->_objectManager->get('Magento_Core_Model_Event_Manager');
        /** @var Magento_Core_Model_Config_Scope $configScope */
        $configScope = $this->_objectManager->get('Magento_Core_Model_Config_Scope');
        $configScope->setCurrentScope('crontab');
        $eventManager->dispatch('default');
    }
}
