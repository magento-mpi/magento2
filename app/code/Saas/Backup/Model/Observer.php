<?php
/**
 * Observer for the Saas_Backup module
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_Backup_Model_Observer
{
    /**
     * @var Mage_Core_Controller_Request_Http
     */
    private $_request;

    /**
     * @var Mage_Backend_Model_Url
     */
    private $_saasHelper;

    /**
     * @param Mage_Core_Controller_Request_Http $request
     * @param Saas_Saas_Helper_Data $saasHelper
     */
    public function __construct(
        Mage_Core_Controller_Request_Http $request,
        Saas_Saas_Helper_Data $saasHelper
    ) {
        $this->_request = $request;
        $this->_saasHelper = $saasHelper;
    }

    /**
     * Limit Backup module functionality
     *
     * @param Varien_Event_Observer $observer
     * @return Saas_Backup_Model_Observer
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function limitBackupFunctionality(Varien_Event_Observer $observer)
    {
        if ($this->_request->getControllerModule() == 'Mage_Adminhtml'
            && $this->_request->getControllerName() == 'system_backup'
        ) {
            $this->_saasHelper->customizeNoRoutForward($this->_request);
        }
        return $this;
    }
}
