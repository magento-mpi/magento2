<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
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
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Staging Manage controller
 */
class Enterprise_Staging_Adminhtml_Staging_ManageController extends Mage_Adminhtml_Controller_Action
{
    protected function _construct()
    {
        // Define module dependent translate
        $this->setUsedModuleName('Enterprise_Staging');
    }

    /**
     * Initialize staging from request parameters
     *
     * @return Enterprise_Staging_Model_Staging
     */
    protected function _initStaging($stagingId = null)
    {
        if (is_null($stagingId)) {
            $stagingId  = (int) $this->getRequest()->getParam('id');
        }
        $staging    = Mage::getModel('enterprise_staging/staging');

        if (!$stagingId) {
            if ($websiteIds = $this->getRequest()->getParam('websites')) {
                $staging->setMasterWebsiteIds($websiteIds);
            }

            if ($storeIds = $this->getRequest()->getParam('stores')) {
                $staging->setMasterStoreIds($storeIds);
            }

            if ($setId = (int) $this->getRequest()->getParam('set')) {
                $staging->setDatasetId($setId);
            }

            if ($type = $this->getRequest()->getParam('type')) {
                $staging->setType($type);
            }
        }

        if ($stagingId) {
            $staging->load($stagingId);
        }

        if (Mage::registry('staging')) {
            Mage::unregister('staging');
        }

        Mage::register('staging', $staging);
        return $staging;
    }

    /**
     * Initialize staging event from request parameters
     *
     * @return Enterprise_Staging_Model_Staging_Event
     */
    protected function _initEvent($eventId = null)
    {
        if (is_null($eventId)) {
            $eventId  = (int) $this->getRequest()->getParam('id');
        }
        $event    = Mage::getModel('enterprise_staging/staging_event');

        if ($eventId) {
            $event->load($eventId);
        }

        $stagingId = $event->getStagingId();

        if ($stagingId) {
            $this->_initStaging($stagingId);
        }

        $event->restoreMap();

        if (Mage::registry('staging_event')) {
            Mage::unregister('staging_event');
        }

        Mage::register('staging_event', $event);

        return $event;
    }

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

        $backup    = Mage::getModel('enterprise_staging/staging_backup');

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

        if (Mage::registry('staging_backup')) {
            Mage::unregister('staging_backup');
        }

        Mage::register('staging_backup', $backup);

        return $backup;
    }

    /**
     * execute index action: load layout, render layout
     *
     */
    public function indexAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Staging edit form
     */
    public function editAction()
    {
        $staging = $this->_initStaging();

//        $websiteIds = (array) $staging->getMasterWebsiteIds();
//        if ($websiteIds) {
//            foreach ($websiteIds as $websiteId) {
//                $website = Mage::app()->getWebsite($websiteId);
//                if ($website->getIsStaging()) {
//                    $this->_getSession()->addError('Some of selected website is staging.');
//                    $this->_redirect('*/*/edit', array('_current' => false));
//                    return $this;
//                }
//
//                $storeIds = $staging->getMasterStoreIds();
//                if ($storeIds) {
//                    $currentStoreIds = isset($storeIds[$websiteId]) ? $storeIds[$websiteId] : array();
//                    if ($currentStoreIds) {
//                        foreach ($currentStoreIds as $storeId) {
//                            $store = Mage::app()->getStore($storeId);
//                            if ($store->getIsStaging()) {
//                                $this->_getSession()->addError('Some of selected stores is staging.');
//                                $this->_redirect('*/*/edit', array('_current' => false));
//                                return $this;
//                            }
//                        }
//                    }
//                }
//            }
//        }

        Mage::dispatchEvent('staging_edit_action', array('staging' => $staging));

        $_additionalLayoutPart = '';
        if (!$staging->getId()) {
            $_additionalLayoutPart = '_new';
        }

        if (!$staging->getId()) {
            $entryPoint = Mage::getSingleton('enterprise_staging/entry');
            if ($entryPoint->isAutomatic()) {
                try {
                    $this->_getSession()->addNotice($this->__('Base URL for this website will be created automatically.'));
                    $entryPoint->canEntryPointBeCreated();
                }
                catch (Mage_Core_Exception $e) {
                    $this->_getSession()->addError($e->getMessage());
                }
            }
        }

        $this->loadLayout(array(
            'default',
            strtolower($this->getFullActionName()),
            'staging_'.$staging->getType() . $_additionalLayoutPart
        ));

        $this->_setActiveMenu('enterprise_staging');

        $this->renderLayout();
        Mage::dispatchEvent('on_staging_edit_after', array('staging' => $staging));
    }

    /**
     * Staging grid for AJAX request
     */
    public function gridAction()
    {
        $staging = $this->_initStaging();

        $this->getResponse()->setBody(
            $this->getLayout()
                ->createBlock('enterprise_staging/manage_staging_grid')
                ->setStaging($staging)
                ->toHtml()
        );
    }

    /**
     * execute event action: init event, load layout, render layout
     *
     */
    public function eventAction()
    {
        $this->_initEvent();

        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Event grid for AJAX request
     */
    public function eventGridAction()
    {
        $staging = $this->_initStaging();
        $this->getResponse()->setBody(
            $this->getLayout()
                ->createBlock('enterprise_staging/manage_staging_edit_tabs_event')
                ->setStaging($staging)
                ->toHtml()
        );
    }

    /**
     * Retrieve "add new staging store view form" (AJAX request)
     */
    public function createStagingStoreAction()
    {
        $staging = $this->_initStaging();

        $this->getResponse()->setBody(
            $this->getLayout()
                ->createBlock('enterprise_staging/manage_staging_edit_tabs_website_store_item')
                ->setStaging($staging)
                ->toHtml()
        );
    }

    /**
     * execute validate methods
     *
     */
    public function validateAction()
    {
        $response = new Varien_Object();
        $response->setError(false);

        try {
            $stagingData = $this->getRequest()->getPost('staging');
            Mage::getModel('enterprise_staging/staging')
                ->setId($this->getRequest()->getParam('id'))
                ->addData($stagingData)
                ->validate();
        } catch (Enterprise_Staging_Exception $e) {
            $response->setError(true);
            $response->setMessage($e->getMessage());
        } catch (Exception $e) {
            $this->_getSession()->addError($e->getMessage());
            $this->_initLayoutMessages('adminhtml/session');
            $response->setError(true);
            $response->setMessage($this->getLayout()->getMessagesBlock()->getGroupedHtml());
        }

        $this->getResponse()->setBody($response->toJson());
    }

    /**
     * Initialize staging before saving
     */
    protected function _initStagingSave()
    {
        $staging = $this->_initStaging();

        $stagingData = $this->getRequest()->getPost('staging');

        $items = isset($stagingData['items']) ? $stagingData['items'] : false;
        if ($items) {
            foreach ($items as $itemData) {
                if (isset($itemData['dataset_item_id'])) {
                    if (!isset($itemData['staging_item_id'])) {
                        $item = Mage::getModel('enterprise_staging/staging_item');
                        $item->addData($itemData);
                        $staging->addItem($item);
                    } else {
                        $code = $itemData['code'];
                        $item = $staging->getItemsCollection()->getItemByCode($code);
                        $item->addData($itemData);
                    }
                } else {
                    if (isset($itemData['staging_item_id'])) {
                        $code = $itemData['code'];
                        $item = $staging->getItemsCollection()->getItemByCode($code);
                        $item->isDeleted(true);
                    }
                }
            }
        }

        $websites       = isset($stagingData['websites']) ? $stagingData['websites'] : array();
        $existWebsites  = Mage::getResourceSingleton('enterprise_staging/staging')->getWebsiteIds($staging);

        foreach ($websites as $key => $websiteData) {
            if (!is_array($websiteData)) {
                $stagingData[$key] = $websiteData;
                continue;
            }
            $websiteId          = isset($websiteData['staging_website_id']) ? $websiteData['staging_website_id'] : false;
            $masterWebsiteId    = isset($websiteData['master_website_id']) ? $websiteData['master_website_id'] : false;
            if ($websiteId && in_array($websiteId, $existWebsites)) {
                $website = $staging->getWebsitesCollection()->getItemById($websiteId);
                $website->addData($websiteData);
            } else {
                $website = Mage::getModel('enterprise_staging/staging_website');
                $website->addData($websiteData);
                $staging->addWebsite($website);
            }

            $datasetItems = isset($websiteData['dataset_items']) ? $websiteData['dataset_items'] : array();
            if ($datasetItems) {
                $items = isset($websiteData['items']) ? $websiteData['items'] : array();
                foreach ($datasetItems as $datasetItemId) {
                    if (array_key_exists($datasetItemId, $items)) {
                        $itemData = isset($items[$datasetItemId]) ? $items[$datasetItemId] : array();
                        $id = isset($itemData['used_dataset_item_id']) ? $itemData['used_dataset_item_id'] : false;
                        if (!empty($itemData['remove_item'])) {
                            if ($id) {
                                $item = $website->getItemsCollection()->getItemByDatasetItemId($id);
                                $item->isDeleted(true);
                            }
                        } else {
                            $item = $website->getItemsCollection()->getItemByDatasetItemId($id);
                            $item->addData($itemData);
                        }
                    } else {
                        $item = Mage::getModel('enterprise_staging/staging_item');
                        $item->setDatasetItemId($datasetItemId);
                        $website->addItem($item);
                    }
                }
            }

            $stores = isset($websiteData['stores'][$masterWebsiteId]) ? $websiteData['stores'][$masterWebsiteId] : array();

            $existStores = Mage::getResourceSingleton('enterprise_staging/staging_website')->getStoreIds($website);

            foreach ($stores as $storeData) {

                if (empty($storeData['staging_store'])) {
                    continue;
                }

                $storeId = isset($storeData['staging_store_id']) ? $storeData['staging_store_id'] : false;
                if ($storeId && in_array($storeId, $existStores)) {
                    $store = $website->getStoresCollection()->getItemById($storeId);
                    $store->addData($storeData);
                } else {
                    $store = Mage::getModel('enterprise_staging/staging_store');
                    $store->addData($storeData);
                    $website->addStore($store);
                }

                $datasetItems = isset($storeData['dataset_items']) ? $storeData['dataset_items'] : array();
                if ($datasetItems && !empty($storeData['use_specific_items'])) {
                    $items = isset($storeData['items']) ? $storeData['items'] :  array();
                    foreach ($datasetItems as $datasetItemId) {
                        if (array_key_exists($datasetItemId, $items)) {
                            $itemData = isset($items[$datasetItemId]) ? $items[$datasetItemId] : array();
                            $id = isset($itemData['used_dataset_item_id']) ? $itemData['used_dataset_item_id'] : false;
                            if (!empty($itemData['remove_item'])) {
                                if ($id) {
                                    $item = $store->getItemsCollection()->getItemByDatasetItemId($id);
                                    $item->isDeleted(true);
                                }
                            } else {
                                $item = $store->getItemsCollection()->getItemByDatasetItemId($id);
                                $item->addData($itemData);
                            }
                        } else {
                            $item = Mage::getModel('enterprise_staging/staging_item');
                            $item->setDatasetItemId($datasetItemId);
                            $store->addItem($item);
                        }
                    }
                }
            }
        }

        /**
         * Initialize general data for staging
         */
        $staging->addData($stagingData);

        /**
         * Initialize data for configurable staging
         */
        $data = $this->getRequest()->getPost('configurable_data');
        if ($data) {
            $staging->setConfigurableData(Zend_Json::decode($data));
        }

        Mage::dispatchEvent('enterprise_staging_prepare_save',
            array('staging' => $staging, 'request' => $this->getRequest()));

        return $staging;
    }

    /**
     * Initialize create staging process throw Ajax request/response
     *
     */
    public function createItemAction()
    {
        $this->_initStaging();

        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('enterprise_staging/manage_staging_create_run')
            ->toHtml());
        $this->getResponse()->sendResponse();
    }

    /**
     * Save/Create staging action
     */
    public function saveAction()
    {
        $redirectBack   = $this->getRequest()->getParam('back', false);
        $stagingId      = $this->getRequest()->getParam('id');
        $isEdit         = (int)($this->getRequest()->getParam('id') != null);

        $data = $this->getRequest()->getPost();

        if ($data) {
            $staging  = $this->_initStagingSave();
            $isUpdate = $staging->getId();

            try {
                if (!$isUpdate) {
                    $staging->setEventCode('create');
                } else {
                    $staging->setEventCode('update');
                }
                $staging->save();
                if (!$isUpdate) {
                    $mapData = $this->getRequest()->getPost('staging');
                    $staging->getMapperInstance()->setCreateMapData($mapData);
                    $staging->create();
                }

                $this->_getSession()->addSuccess($this->__('Staging website successfully saved.'));
                $stagingId = $staging->getId();
                Mage::dispatchEvent('on_enterprise_staging_save', array('staging' => $staging));
            } catch (Mage_Core_Exception $e) {
                //echo '<pre>'.$e;ddd();
                $this->_getSession()->addError($e->getMessage())
                    ->setStagingData($data);
                $redirectBack = true;
            } catch (Exception $e) {
                //echo '<pre>'.$e;ddd();
                $this->_getSession()->addException($e, $e->getMessage());
                $redirectBack = true;
            }
        }

        if ($redirectBack) {
            $this->_redirect('*/*/edit', array(
                'id'    => $stagingId,
                '_current'=>true
            ));
        } elseif ($this->getRequest()->getParam('popup')) {
            $this->_redirect('*/*/created', array(
                '_current'   => true,
                'id'         => $stagingId,
                'edit'       => $isEdit
            ));
        } else {
            $this->_redirect('*/*/');
        }
    }


    /**
     * Staging delete action
     */
    public function deleteAction()
    {
        $id = $this->getRequest()->getParam('id');
        if ($id) {
            $staging = Mage::getModel('enterprise_staging/staging')->load($id);

            try {

                $backupCollection = Mage::getResourceModel('enterprise_staging/staging_backup_collection')->setStagingFilter($staging->getId());
                foreach ($backupCollection as $backup) {
                    if ($backup->getId() > 0) {
                        $backup->setStaging($staging);
                        $backup->setIsDeleteTables(true);
                        $backup->delete();
                    }
                }

                Mage::dispatchEvent('enterprise_staging_controller_staging_delete', array('staging'=>$staging));
                $staging->delete();
                $this->_getSession()->addSuccess($this->__('Staging website deleted'));
            } catch (Exception $e){
                mageDebugBacktrace();fff();
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->getResponse()->setRedirect($this->getUrl('*/*/'));
    }

    /**
     * Staging merge view action
     *
     */
    public function mergeAction()
    {
        $this->_initStaging();

        $this->loadLayout();

        $this->_setActiveMenu('enterprise/staging');

        $this->renderLayout();
    }

    /**
     * Staging process merge
     *
     */
    public function mergePostAction()
    {
        $redirectBack   = $this->getRequest()->getParam('back', false);

        $staging = $this->_initStaging();
        /* @var $staging Enterprise_Staging_Model_Staging */

        $mapData = $this->getRequest()->getPost('map');

        $isMergeLater = $this->getRequest()->getPost('schedule_merge_later_flag');

        $mergeSchedulingDate = $this->getRequest()->getPost('schedule_merge_later');

        $stagingId = "";

        if ($mapData) {
            try {
                $staging->getMapperInstance()->setMapData($mapData);

                if (!empty($mapData['backup'])) {
                    $staging->setIsBackuped(1);
                }

                //scheduling merge
                if ($isMergeLater && !empty($mergeSchedulingDate)) {
                    $staging->setIsMergeLater('true');
                    $date = date("Y-m-d H:i:s", strtotime($mergeSchedulingDate));

                    //convert to internal time
                    $date = Mage::app()->getLocale()->date($date, Varien_Date::DATETIME_INTERNAL_FORMAT)->toString("YYYY-MM-dd HH:mm:ss");

                    $staging->setMergeSchedulingDate($date);
                } else {

                    if (!empty($mapData['backup'])) {
                        // run create database backup
                        $staging->backup();
                    }
                }

                $staging->merge();

                if ($staging->getId()) {
                    if ($isMergeLater && !empty($mergeSchedulingDate)) {
                        $this->_getSession()->addSuccess($this->__('Staging website successfully scheduled to merge.'));
                    } else {
                        $this->_getSession()->addSuccess($this->__('Staging website successfully merged.'));
                    }
                    $stagingId = $staging->getId();
                    Mage::dispatchEvent('on_enterprise_staging_merge', array('staging' => $staging));
                } else {
                    $redirectBack = false;
                    if ($isMergeLater && !empty($mergeSchedulingDate)) {
                        $this->_getSession()->addSuccess($this->__('Staging website(s) was successfully scheduled to merged.'));
                    } else {
                        $this->_getSession()->addSuccess($this->__('Staging website(s) was successfully merged.'));
                    }
                }
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
                $redirectBack = true;
            } catch (Exception $e) {
                $this->_getSession()->addException($e, $e->getMessage());
                $redirectBack = true;
            }
        }

        if ($redirectBack) {
            $this->_redirect('*/*/edit', array(
                'id'        => $stagingId,
                '_current'  =>true
            ));
        } else {
            $this->_redirect('*/*/');
        }
    }

    /**
     * Staging backup view action
     *
     */
    public function backupAction()
    {
        $this->_initStaging();

        $this->loadLayout();

        $this->_setActiveMenu('enterprise/staging');

        $this->renderLayout();
    }

    /**
     * backup edit process
     *
     */
    public function backupEditAction()
    {
        $this->_initBackup();

        $this->loadLayout();

        $this->_setActiveMenu('enterprise/staging');

        $this->renderLayout();
    }

    /**
     * Staging grid for AJAX request
     */
    public function backupGridAction()
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
    public function massBackupDeleteAction()
    {
        $backupDeleteIds = $this->getRequest()->getPost("backupDelete");

        $redirectBack = false;

        if (is_array($backupDeleteIds)) {
            foreach ($backupDeleteIds as $backupId) {
                if ($backupId > 0) {
                    $backup = $this->_initBackup($backupId);

                    if ($backup->getId() > 0) {

                        $staging = $backup->getStaging();

                        $redirectBack = false;

                        try{
                            $backup->setIsDeleteTables(true);
                            $backup->delete();
                        } catch (Exception $e) {
                            $redirectBack = true;
                        }
                    }
                }
            }
        }

        if ($redirectBack) {
            $this->_redirect('*/*/backup', array(
                'id'        => $backup->getId(),
                '_current'  =>true
            ));
        } else {
            $this->_redirect('*/*/backup');
        }

    }

    /**
     * Remove backup
     *
     */
    public function backupDeleteAction()
    {
        $backup = $this->_initBackup();

        $staging = $backup->getStaging();

        $redirectBack = false;

        try{

            $backup->setIsDeleteTables(true);

            $backup->delete();

        } catch (Exception $e) {

            $redirectBack = true;
        }

        if ($redirectBack) {
            $this->_redirect('*/*/backup', array(
                'id'        => $backup->getId(),
                '_current'  =>true
            ));
        } else {
            $this->_redirect('*/*/backup');
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

        $this->_setActiveMenu('enterprise/staging');

        $this->renderLayout();
    }

    /**
     * Process rollback Action
     *
     */
    public function rollbackPostAction()
    {
        $redirectBack   = $this->getRequest()->getParam('back', false);

        $stagingId = $this->getRequest()->getPost('staging_id');

        $backupId = $this->getRequest()->getPost('backup_id');

        $backup = $this->_initBackup();

        $staging = $backup->getStaging();

        $stagingId = $staging->getId();

        $mapData = $this->getRequest()->getPost('map');

        try {
            $staging->getMapperInstance()->setMapData($mapData);

            $staging->setEventId($backup->getEventId());

            $staging->rollback();

            $this->_getSession()->addSuccess($this->__('Master website successfully restored.'));

            $stagingId = $staging->getId();

            Mage::dispatchEvent('on_enterprise_staging_rollback', array('staging' => $staging));

        } catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
            $redirectBack = true;
        } catch (Exception $e) {
            $this->_getSession()->addException($e, $e->getMessage());
            $redirectBack = true;
        }

        if ($redirectBack) {
            $this->_redirect('*/*/backup', array(
                'id'        => $backupId,
                '_current'  =>true
            ));
        } else {
            $this->_redirect('*/*/');
        }
    }
}
