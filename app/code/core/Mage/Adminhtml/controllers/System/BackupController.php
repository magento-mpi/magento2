<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Backup admin controller
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_System_BackupController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Backup list action
     */
    public function indexAction()
    {
        $this->_title($this->__('System'))->_title($this->__('Tools'))->_title($this->__('Backups'));

        if($this->getRequest()->getParam('ajax')) {
            $this->_forward('grid');
            return;
        }

        $this->loadLayout();
        $this->_setActiveMenu('system');
        $this->_addBreadcrumb(Mage::helper('Mage_Adminhtml_Helper_Data')->__('System'), Mage::helper('Mage_Adminhtml_Helper_Data')->__('System'));
        $this->_addBreadcrumb(Mage::helper('Mage_Adminhtml_Helper_Data')->__('Tools'), Mage::helper('Mage_Adminhtml_Helper_Data')->__('Tools'));
        $this->_addBreadcrumb(Mage::helper('Mage_Adminhtml_Helper_Data')->__('Backups'), Mage::helper('Mage_Adminhtml_Helper_Data')->__('Backup'));

        $this->_addContent($this->getLayout()->createBlock('Mage_Adminhtml_Block_Backup', 'backup'));

        $this->renderLayout();
    }

    /**
     * Backup list action
     */
    public function gridAction()
    {
        $this->getResponse()->setBody($this->getLayout()->createBlock('Mage_Adminhtml_Block_Backup_Grid')->toHtml());
    }

    /**
     * Create backup action
     *
     * @return Mage_Adminhtml_Controller_Action
     */
    public function createAction()
    {
        if (!$this->getRequest()->isAjax()) {
            return $this->getUrl('*/*/index');
        }

        $response = new Varien_Object();
        $helper = Mage::helper('Mage_Backup_Helper_Data');

        try {
            $type = $this->getRequest()->getParam('type');

            $backupManager = Mage_Backup::getBackupInstance($type)
                ->setBackupExtension($helper->getExtensionByType($type))
                ->setTime(time())
                ->setBackupsDir($helper->getBackupsDir());

            Mage::register('backup_manager', $backupManager);

            if ($this->getRequest()->getParam('maintenance_mode')) {
                $turnedOn = $helper->turnOnMaintenanceMode();

                if (!$turnedOn) {
                    $response->setError(
                        Mage::helper('Mage_Backup_Helper_Data')->__("Warning! System couldn't put store on the maintenance mode.") . ' '
                        . Mage::helper('Mage_Backup_Helper_Data')->__("Please deselect the sufficient check-box, if you want to continue backup's creation")
                    );
                    $backupManager->setErrorMessage(Mage::helper('Mage_Backup_Helper_Data')->__("System couldn't put store on the maintenance mode"));
                    return $this->getResponse()->setBody($response->toJson());
                }
            }

            if ($type != Mage_Backup_Helper_Data::TYPE_DB) {
                $backupManager->setRootDir(Mage::getBaseDir())
                    ->addIgnorePaths($helper->getBackupIgnorePaths());
            }

            $successMessage = $helper->getCreateSuccessMessageByType($type);

            $backupManager->create();

            $this->_getSession()->addSuccess($successMessage);

            if ($this->getRequest()->getParam('maintenance_mode')) {
                $helper->turnOffMaintenanceMode();
            }

            $response->setRedirectUrl($this->getUrl('*/*/index'));
        } catch (Mage_Backup_Exception_NotEnoughFreeSpace $e) {
            $errorMessage = Mage::helper('Mage_Backup_Helper_Data')->__('Not enough free space to create backup.');
        } catch (Mage_Backup_Exception_NotEnoughPermissions $e) {
            Mage::log($e->getMessage());
            $errorMessage = Mage::helper('Mage_Backup_Helper_Data')->__('Not enough permissions to create backup.');
        } catch (Exception  $e) {
            Mage::log($e->getMessage());
            $errorMessage = Mage::helper('Mage_Backup_Helper_Data')->__('An error occurred while creating the backup.');
        }

        if (!empty($errorMessage)) {
            $response->setError($errorMessage);
            $backupManager->setErrorMessage($errorMessage);
        }

        $this->getResponse()->setBody($response->toJson());
    }

    /**
     * Download backup action
     *
     * @return Mage_Adminhtml_Controller_Action
     */
    public function downloadAction()
    {
        $backup = Mage::getModel('Mage_Backup_Model_Backup')
            ->setTime((int)$this->getRequest()->getParam('time'))
            ->setType($this->getRequest()->getParam('type'))
            ->setPath(Mage::helper('Mage_Backup_Helper_Data')->getBackupsDir());
        /* @var $backup Mage_Backup_Model_Backup */

        if (!$backup->exists()) {
            return $this->_redirect('*/*');
        }

        $fileName = Mage::helper('Mage_Backup_Helper_Data')->generateBackupDownloadName($backup);

        $this->_prepareDownloadResponse($fileName, null, 'application/octet-stream', $backup->getSize());

        $this->getResponse()->sendHeaders();

        $backup->output();
        exit();
    }

    /**
     * Rollback Action
     *
     * @return Mage_Adminhtml_Controller_Action
     */
    public function rollbackAction()
    {
        if (!Mage::helper('Mage_Backup_Helper_Data')->isRollbackAllowed()){
            return $this->_forward('denied');
        }

        if (!$this->getRequest()->isAjax()) {
            return $this->getUrl('*/*/index');
        }

        $helper = Mage::helper('backup');
        $response = new Varien_Object();

        try {
            $type = $this->getRequest()->getParam('type');

            $backupManager = Mage_Backup::getBackupInstance($type)
                ->setBackupExtension($helper->getExtensionByType($type))
                ->setTime($this->getRequest()->getParam('time'))
                ->setBackupsDir($helper->getBackupsDir())
                ->setResourceModel(Mage::getResourceModel('Mage_Backup_Model_Resource_Db'));

            Mage::register('backup_manager', $backupManager);

            $passwordValid = Mage::getModel('Mage_Backup_Model_Backup')->validateUserPassword(
                $this->getRequest()->getParam('password')
            );

            if (!$passwordValid) {
                $response->setError(Mage::helper('Mage_Backup_Helper_Data')->__('Invalid Password.'));
                $backupManager->setErrorMessage(Mage::helper('Mage_Backup_Helper_Data')->__('Invalid Password.'));
                return $this->getResponse()->setBody($response->toJson());
            }

            if ($this->getRequest()->getParam('maintenance_mode')) {
                $turnedOn = $helper->turnOnMaintenanceMode();

                if (!$turnedOn) {
                    $response->setError(
                        Mage::helper('Mage_Backup_Helper_Data')->__("Warning! System couldn't put store on the maintenance mode.") . ' '
                        . Mage::helper('Mage_Backup_Helper_Data')->__("Please deselect the sufficient check-box, if you want to continue rollback processing")
                    );
                    $backupManager->setErrorMessage(Mage::helper('Mage_Backup_Helper_Data')->__("System couldn't put store on the maintenance mode"));
                    return $this->getResponse()->setBody($response->toJson());
                }
            }

            if ($type != Mage_Backup_Helper_Data::TYPE_DB) {

                $backupManager->setRootDir(Mage::getBaseDir())
                    ->addIgnorePaths($helper->getRollbackIgnorePaths());

                if ($this->getRequest()->getParam('use_ftp', false)) {
                    $backupManager->setUseFtp(
                        $this->getRequest()->getParam('ftp_host', ''),
                        $this->getRequest()->getParam('ftp_user', ''),
                        $this->getRequest()->getParam('ftp_pass', ''),
                        $this->getRequest()->getParam('ftp_path', '')
                    );
                }
            }

            $backupManager->rollback();

            $helper->invalidateCache()->invalidateIndexer();

            $adminSession = $this->_getSession();
            $adminSession->unsetAll();
            $adminSession->getCookie()->delete($adminSession->getSessionName());

            if ($this->getRequest()->getParam('maintenance_mode')) {
                $helper->turnOffMaintenanceMode();
            }

            $response->setRedirectUrl($this->getUrl('*'));

        } catch (Mage_Backup_Exception_CantLoadSnapshot $e) {
            $errorMsg = Mage::helper('Mage_Backup_Helper_Data')->__('Backup file not found');
        } catch (Mage_Backup_Exception_FtpConnectionFailed $e) {
            $errorMsg = Mage::helper('Mage_Backup_Helper_Data')->__('Failed to connect to FTP');
        } catch (Mage_Backup_Exception_FtpValidationFailed $e) {
            $errorMsg = Mage::helper('Mage_Backup_Helper_Data')->__('Failed to validate FTP');
        } catch (Mage_Backup_Exception_NotEnoughPermissions $e) {
            Mage::log($e->getMessage());
            $errorMsg = Mage::helper('Mage_Backup_Helper_Data')->__('Not enough permissions to perform rollback');
        } catch (Exception $e) {
            Mage::log($e->getMessage());
            $errorMsg = Mage::helper('Mage_Backup_Helper_Data')->__('Failed to rollback');
        }

        if (!empty($errorMsg)) {
            $response->setError($errorMsg);
            $backupManager->setErrorMessage($errorMsg);
        }

        $this->getResponse()->setBody($response->toJson());
    }

    /**
     * Delete backups mass action
     *
     * @return Mage_Adminhtml_Controller_Action
     */
    public function massDeleteAction()
    {
        $backupIds = $this->getRequest()->getParam('ids', array());

        if (!is_array($backupIds) || !count($backupIds)) {
            return $this->_redirect('*/*/index');
        }

        /** @var $backupModel Mage_Backup_Model_Backup */
        $backupModel = Mage::getModel('Mage_Backup_Model_Backup');
        $resultData = new Varien_Object();
        $resultData->setIsSuccess(false);
        $resultData->setDeleteResult(array());
        Mage::register('backup_manager', $resultData);

        $deleteFailMessage = Mage::helper('Mage_Backup_Helper_Data')->__('Failed to delete one or several backups.');

        try {
            $allBackupsDeleted = true;

            foreach ($backupIds as $id) {
                list($time, $type) = explode('_', $id);

                $backupModel->setTime((int)$time)
                    ->setType($type)
                    ->setPath(Mage::helper('Mage_Backup_Helper_Data')->getBackupsDir())
                    ->deleteFile();

                if ($backupModel->exists()) {
                    $allBackupsDeleted = false;
                    $result = Mage::helper('Mage_Adminhtml_Helper_Data')->__('failed');
                } else {
                    $result = Mage::helper('Mage_Adminhtml_Helper_Data')->__('successful');
                }

                $resultData->setDeleteResult(
                    array_merge($resultData->getDeleteResult(), array($backupModel->getFileName() . ' ' . $result))
                );
            }

            $resultData->setIsSuccess(true);
            if ($allBackupsDeleted) {
                $this->_getSession()->addSuccess(
                    Mage::helper('Mage_Backup_Helper_Data')->__('The selected backup(s) has been deleted.')
                );
            }
            else {
                throw new Exception($deleteFailMessage);
            }
        } catch (Exception $e) {
            $resultData->setIsSuccess(false);
            $this->_getSession()->addError($deleteFailMessage);
        }

        return $this->_redirect('*/*/index');
    }

    /**
     * Check Permissions for all actions
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('Mage_Admin_Model_Session')->isAllowed('system/tools/backup');
    }

    /**
     * Retrive adminhtml session model
     *
     * @return Mage_Adminhtml_Model_Session
     */
    protected function _getSession()
    {
        return Mage::getSingleton('Mage_Adminhtml_Model_Session');
    }
}
