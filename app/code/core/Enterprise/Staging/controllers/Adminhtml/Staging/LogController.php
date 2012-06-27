<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Staging
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Staging Manage controller
 */
class Enterprise_Staging_Adminhtml_Staging_LogController extends Mage_Adminhtml_Controller_Action
{
    protected function _construct()
    {
        // Define module dependent translate
        $this->setUsedModuleName('Enterprise_Staging');
    }

    /**
     * View History Log Grid
     *
     */
    public function indexAction()
    {
        $this->_title($this->__('System'))
             ->_title($this->__('Content Staging'))
             ->_title($this->__('Log'));

        $this->loadLayout();
        $this->_setActiveMenu('Mage_Adminhtml::system');
        $this->renderLayout();
    }

    /**
     * View details for Log  entry
     *
     */
    public function viewAction()
    {
        $this->_initLog();

        $this->_title($this->__('System'))
             ->_title($this->__('Content Staging'))
             ->_title($this->__('Log'))
             ->_title($this->__('Log Entry'));

        $this->loadLayout();
        $this->_setActiveMenu('Mage_Adminhtml::system');
        $this->renderLayout();
    }

    /**
     * Preparing log model with loaded data by passed id
     *
     * @param int $logId
     * @return Enterprise_Staging_Model_Staging_Log
     */
    protected function _initLog($logId = null)
    {
        if (is_null($logId)) {
            $logId  = (int) $this->getRequest()->getParam('id');
        }

        if ($logId) {
            $log = Mage::getModel('Enterprise_Staging_Model_Staging_Log')
                ->load($logId);

            if ($log->getId()) {
                Mage::register('log', $log);
                return $log;
            }
        }
        return false;
    }

    /**
     * Check current user permission on resource and privilege
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('Mage_Backend_Model_Auth_Session')->isAllowed('system/enterprise_staging/staging_log');
    }
}
