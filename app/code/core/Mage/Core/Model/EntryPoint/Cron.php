<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Core_Model_EntryPoint_Cron extends Mage_Core_Model_EntryPointAbstract
{
    /**
     * Process request to application
     */
    protected function _processRequest()
    {
        /** @var $app Mage_Core_Model_App */
        $app = $this->_objectManager->get('Mage_Core_Model_App');
        $app->setUseSessionInUrl(false);
        $app->requireInstalledInstance();

        /** @var $eventManager Mage_Core_Model_Event_Manager */
        $eventManager = $this->_objectManager->get('Mage_Core_Model_Event_Manager');
        $eventManager->addEventArea('crontab');
        $eventManager->dispatch('default');
    }
}
