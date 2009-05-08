<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Enterprise
 * @package    Enterprise_Staging
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://www.magentocommerce.com/license/enterprise-edition
 */

require_once 'Enterprise/Staging/controllers/Adminhtml/Staging/ManageController.php';
/**
 * Staging Manage controller
 */
class Enterprise_Staging_Adminhtml_Staging_BackupController extends Enterprise_Staging_Adminhtml_Staging_ManageController
{
    /**
     * Initialize staging backup from request parameters
     *
     * @return Enterprise_Staging_Model_Staging_Backup
     */
    protected function _initBackup($backupId = null)
    {
        if (is_null($backupId)) {
            $backupId  = (int) $this->getRequest()->getParam('id');
        }

        $backup = Mage::getModel('enterprise_staging/staging_backup');
        if ($backupId) {
            $backup->load($backupId);
        }

        $eventId = $backup->getEventId();
        if ($eventId) {
            $this->_initEvent($eventId);
        } else {
            $stagingId = $backup->getStagingId();
            if ($stagingId) {
                $this->_initStaging($stagingId);
            }
        }

        if ($backup->getId()) {
            $backup->restoreMap();
        }

        Mage::register('staging_backup', $backup);

        return $backup;
    }

    /**
     * Staging backup view action
     *
     */
    public function indexAction()
    {
        $this->_initStaging();

        $this->loadLayout();
        $this->_setActiveMenu('system/enterprise_staging');
        $this->renderLayout();
    }

    /**
     * backup edit process
     *
     */
    public function editAction()
    {
        $backup = $this->_initBackup();

        $staging = $backup->getStaging();

        if (!$backup->canRollback()) {
            $this->_getSession()->addNotice($this->__('All Backup Items are outdated. The Backup is read-only.'));
        }

        if ($staging && $staging->isStatusProcessing()) {
            $this->_getSession()->addNotice($this->__('This Backup is read-only, because a Merge or Rollback is in progress. Please try again later.'));
        }

        $this->loadLayout();
        $this->_setActiveMenu('system/enterprise_staging');
        $this->renderLayout();
    }

    /**
     * Staging grid for AJAX request
     */
    public function gridAction()
    {
        $staging = $this->_initStaging();

        $this->getResponse()->setBody(
            $this->getLayout()
                ->createBlock('enterprise_staging/manage_staging_backup_grid')
                ->setStaging($staging)
                ->toHtml()
        );
    }

    /**
     * Remove mass backups
     *
     */
    public function massDeleteAction()
    {
        $backupDeleteIds = $this->getRequest()->getPost("backupDelete");
        if (is_array($backupDeleteIds)) {
            foreach ($backupDeleteIds as $backupId) {
                if (!empty($backupId)) {
                    $backup = Mage::getModel('enterprise_staging/staging_backup')
                        ->load($backupId);
                    if ($backup->getId()) {
                        try{
                            $backup->setIsDeleteTables(true);
                            $backup->delete();
                        } catch (Exception $e) {
                            $this->_getSession()->addNotice(
                                $this->__('Couldn\'t remove backup: #%s', $backup->getId()));
                        }
                    }
                }
            }
        }

        $this->_redirect('*/*/');
    }

    /**
     * Remove backup
     *
     */
    public function deleteAction()
    {
        $backup         = $this->_initBackup();
        $redirectBack   = false;

        try{
            $backup->setIsDeleteTables(true);
            $backup->delete();
        } catch (Exception $e) {
            $redirectBack = true;
        }

        if ($redirectBack) {
            $this->_redirect('*/*/', array(
                'id'        => $backup->getId(),
                '_current'  => true
            ));
        } else {
            $this->_redirect('*/*/');
        }
    }

    /**
     * Staging grid for AJAX request
     */
    public function rollbackGridAction()
    {
        $backupId = $this->getRequest()->getParam('id');

        $backup = $this->_initBackup($backupId);

        $staging = $backup->getStaging();

        $this->getResponse()->setBody(
            $this->getLayout()
                ->createBlock('enterprise_staging/manage_staging_backup_edit_tabs_rollback')
                ->setStaging($staging)
                ->setBackup($backup)
                ->toHtml()
        );
    }

    /**
     * Rollback view action
     *
     */
    public function rollbackAction()
    {
        $this->_initBackup();

        $this->loadLayout();

        $this->_setActiveMenu('system/enterprise_staging');

        $this->renderLayout();
    }

    /**
     * Process rollback Action
     *
     */
    public function rollbackPostAction()
    {
        $redirectBack   = $this->getRequest()->getParam('back', false);
        $backupId       = $this->getRequest()->getPost('backup_id');
        $backup         = $this->_initBackup();
        $staging        = $backup->getStaging();
        $mapData        = $this->getRequest()->getPost('map');

        try {
            $staging->getMapperInstance()->setRollbackMapData($mapData);

            if ($staging->getMapperInstance()->hasStagingItems()) {
                $staging->setEventId($backup->getEventId());
                $staging->rollback();
                $this->_getSession()->addSuccess($this->__('Master website successfully restored.'));
            } else {
                $this->_getSession()->addNotice($this->__('There are no items were selected for rollback.'));
            }
        } catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
            $redirectBack = true;
        } catch (Exception $e) {
            $this->_getSession()->addException($e, $e->getMessage());
            $redirectBack = true;
        }

        if ($redirectBack) {
            $this->_redirect('*/*/edit', array(
                'id'        => $backupId,
                '_current'  => true
            ));
        } else {
            $this->_redirect('*/*/');
        }
    }
}
