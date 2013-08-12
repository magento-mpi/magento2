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
        $this->_title(__('Backups'));

        if($this->getRequest()->getParam('ajax')) {
            $this->_forward('grid');
            return;
        }

        $this->loadLayout();
        $this->_setActiveMenu('Mage_Backup::system_tools_backup');
        $this->_addBreadcrumb(__('System'), __('System'));
        $this->_addBreadcrumb(__('Tools'), __('Tools'));
        $this->_addBreadcrumb(__('Backups'), __('Backup'));

        $this->renderLayout();
    }

    /**
     * Backup list action
     */
    public function gridAction()
    {
        $this->renderLayot(false);
        $this->renderLayout();
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

        $response = new Magento_Object();

        /**
         * @var Mage_Backup_Helper_Data $helper
         */
        $helper = Mage::helper('Mage_Backup_Helper_Data');

        try {
            $type = $this->getRequest()->getParam('type');

            if ($type == Mage_Backup_Helper_Data::TYPE_SYSTEM_SNAPSHOT
                && $this->getRequest()->getParam('exclude_media')
            ) {
                $type = Mage_Backup_Helper_Data::TYPE_SNAPSHOT_WITHOUT_MEDIA;
            }

            $backupManager = Mage_Backup::getBackupInstance($type)
                ->setBackupExtension($helper->getExtensionByType($type))
                ->setTime(time())
                ->setBackupsDir($helper->getBackupsDir());

            $backupManager->setName($this->getRequest()->getParam('backup_name'));

            Mage::register('backup_manager', $backupManager);

            if ($this->getRequest()->getParam('maintenance_mode')) {
                $turnedOn = $helper->turnOnMaintenanceMode();

                if (!$turnedOn) {
                    $response->setError(
                        __('You need more permissions to activate maintenance mode right now.')
                            . ' ' . __('To continue with the backup, you need to either deselect "Put store on the maintenance mode" or update your permissions.')
                    );
                    $backupManager->setErrorMessage(__("Something went wrong putting your store into maintenance mode."));
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

            $response->setRedirectUrl($this->getUrl('*/*/index'));
        } catch (Mage_Backup_Exception_NotEnoughFreeSpace $e) {
            $errorMessage = __('You need more free space to create a backup.');
        } catch (Mage_Backup_Exception_NotEnoughPermissions $e) {
            Mage::log($e->getMessage());
            $errorMessage = __('You need more permissions to create a backup.');
        } catch (Exception  $e) {
            Mage::log($e->getMessage());
            $errorMessage = __('Something went wrong creating the backup.');
        }

        if (!empty($errorMessage)) {
            $response->setError($errorMessage);
            $backupManager->setErrorMessage($errorMessage);
        }

        if ($this->getRequest()->getParam('maintenance_mode')) {
            $helper->turnOffMaintenanceMode();
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
        /* @var $backup Mage_Backup_Model_Backup */
        $backup = Mage::getModel('Mage_Backup_Model_Backup')->loadByTimeAndType(
            $this->getRequest()->getParam('time'),
            $this->getRequest()->getParam('type')
        );

        if (!$backup->getTime() || !$backup->exists()) {
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

        $helper = Mage::helper('Mage_Backup_Helper_Data');
        $response = new Magento_Object();

        try {
            /* @var $backup Mage_Backup_Model_Backup */
            $backup = Mage::getModel('Mage_Backup_Model_Backup')->loadByTimeAndType(
                $this->getRequest()->getParam('time'),
                $this->getRequest()->getParam('type')
            );

            if (!$backup->getTime() || !$backup->exists()) {
                return $this->_redirect('*/*');
            }

            if (!$backup->getTime()) {
                throw new Mage_Backup_Exception_CantLoadSnapshot();
            }

            $type = $backup->getType();

            $backupManager = Mage_Backup::getBackupInstance($type)
                ->setBackupExtension($helper->getExtensionByType($type))
                ->setTime($backup->getTime())
                ->setBackupsDir($helper->getBackupsDir())
                ->setName($backup->getName(), false)
                ->setResourceModel(Mage::getResourceModel('Mage_Backup_Model_Resource_Db'));

            Mage::register('backup_manager', $backupManager);

            $passwordValid = Mage::getModel('Mage_Backup_Model_Backup')->validateUserPassword(
                $this->getRequest()->getParam('password')
            );

            if (!$passwordValid) {
                $response->setError(__('Please correct the password.'));
                $backupManager->setErrorMessage(__('Please correct the password.'));
                return $this->getResponse()->setBody($response->toJson());
            }

            if ($this->getRequest()->getParam('maintenance_mode')) {
                $turnedOn = $helper->turnOnMaintenanceMode();

                if (!$turnedOn) {
                    $response->setError(
                        __('You need more permissions to activate maintenance mode right now.')
                            . ' ' . __('To continue with the rollback, you need to either deselect "Put store on the maintenance mode" or update your permissions.')
                    );
                    $backupManager->setErrorMessage(__("Something went wrong putting your store into maintenance mode."));
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

            $response->setRedirectUrl($this->getUrl('*'));
        } catch (Mage_Backup_Exception_CantLoadSnapshot $e) {
            $errorMsg = __('The backup file was not found.');
        } catch (Mage_Backup_Exception_FtpConnectionFailed $e) {
            $errorMsg = __('We couldn\'t connect to the FTP.');
        } catch (Mage_Backup_Exception_FtpValidationFailed $e) {
            $errorMsg = __('Failed to validate FTP');
        } catch (Mage_Backup_Exception_NotEnoughPermissions $e) {
            Mage::log($e->getMessage());
            $errorMsg = __('You need more permissions to create a backup.');
        } catch (Exception $e) {
            Mage::log($e->getMessage());
            $errorMsg = __('Failed to rollback');
        }

        if (!empty($errorMsg)) {
            $response->setError($errorMsg);
            $backupManager->setErrorMessage($errorMsg);
        }

        if ($this->getRequest()->getParam('maintenance_mode')) {
            $helper->turnOffMaintenanceMode();
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
        $resultData = new Magento_Object();
        $resultData->setIsSuccess(false);
        $resultData->setDeleteResult(array());
        Mage::register('backup_manager', $resultData);

        $deleteFailMessage = __('We couldn\'t delete one or more backups.');

        try {
            $allBackupsDeleted = true;

            foreach ($backupIds as $id) {
                list($time, $type) = explode('_', $id);
                $backupModel
                    ->loadByTimeAndType($time, $type)
                    ->deleteFile();

                if ($backupModel->exists()) {
                    $allBackupsDeleted = false;
                    $result = __('failed');
                } else {
                    $result = __('successful');
                }

                $resultData->setDeleteResult(
                    array_merge($resultData->getDeleteResult(), array($backupModel->getFileName() . ' ' . $result))
                );
            }

            $resultData->setIsSuccess(true);
            if ($allBackupsDeleted) {
                $this->_getSession()->addSuccess(
                    __('The selected backup(s) has been deleted.')
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
        return $this->_authorization->isAllowed('Mage_Backup::backup');
    }
}
