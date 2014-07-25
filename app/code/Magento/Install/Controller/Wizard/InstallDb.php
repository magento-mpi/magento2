<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Install\Controller\Wizard;

class InstallDb extends \Magento\Install\Controller\Wizard
{
    /**
     * Install DB
     *
     * @return void
     */
    public function execute()
    {
        $this->_checkIfInstalled();
        $step = $this->_getWizard()->getStepByName('config');
        try {
            $this->_getInstaller()->installDb();
            /**
             * Clear session config data
             */
            $this->_session->getConfigData(true);

            $this->_storeManager->getStore()->resetConfig();
            $this->_dbUpdater->updateData();

            $this->getResponse()->setRedirect($step->getNextUrl());
        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
            $this->getResponse()->setRedirect($step->getUrl());
        }
    }
}
