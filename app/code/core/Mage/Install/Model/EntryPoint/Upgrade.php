<?php
/**
 * Entry point for upgrading application
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Install_Model_EntryPoint_Upgrade extends Mage_Core_Model_EntryPointAbstract
{
    /**
     * Apply scheme & data updates
     */
    protected function _processRequest()
    {
        /** @var $cache Mage_Core_Model_Cache */
        $cache = $this->_objectManager->get('Mage_Core_Model_Cache');
        $cache->flush();

        /** @var $appState \Mage_Core_Model_App_State */
        $appState = $this->_objectManager->get('Mage_Core_Model_App_State');
        $appState->setIsDeveloperMode(true);

        /** @var $updater \Mage_Core_Model_Db_Updater */
        $updater = $this->_objectManager->get('Mage_Core_Model_Db_Updater');
        $updater->updateScheme();
        $updater->updateData();
    }
}
