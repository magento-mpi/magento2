<?php
/**
 * Multi-tenant database scheme updater entry point
 *
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */
namespace Magento\MultiTenant\Upgrade;

class EntryPoint extends \Mage_Core_Model_EntryPointAbstract
{
    /**
     * Apply scheme updates
     */
    protected function _processRequest()
    {
        /** @var $appState \Mage_Core_Model_App_State */
        $appState = $this->_objectManager->get('Mage_Core_Model_App_State');
        $appState->setIsDeveloperMode(true);

        /** @var $updater \Mage_Core_Model_Db_Updater */
        $updater = $this->_objectManager->get('Mage_Core_Model_Db_Updater');
        $updater->updateScheme();
    }
}
