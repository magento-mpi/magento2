<?php
/**
 * Observer for the Saas_Backup module
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_Backup_Model_Observer extends Saas_Saas_Model_Observer_Controller_LimitationAbstract
{
    /**
     * Limit Backup module functionality
     *
     * @param Varien_Event_Observer $observer
     * @return Saas_Backup_Model_Observer
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function limitBackupFunctionality(Varien_Event_Observer $observer)
    {
        if ($this->_request->getControllerName() == 'system_backup'
            && $this->_request->getControllerModule() == 'Mage_Adminhtml'
        ) {
            $this->_saasHelper->customizeNoRoutForward($this->_request);
        }
        return $this;
    }
}
