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
            if ($websiteId = (int) $this->getRequest()->getParam('master_website_id')) {
                $staging->setMasterWebsiteId($websiteId);
            }
            if ($type = $this->getRequest()->getParam('type')) {
                $staging->setType($type);
            }
        }
        if ($stagingId) {
            $staging->load($stagingId);
            if (!$staging->getId()) {
                return false;
            }
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
        $event = Mage::getModel('enterprise_staging/staging_event');

        if ($eventId) {
            $event->load($eventId);
        }

        $stagingId = $event->getStagingId();
        if ($stagingId) {
            $this->_initStaging($stagingId);
        }

        if ($event->getId()) {
            $event->restoreMap();
        }

        Mage::register('staging_event', $event);

        return $event;
    }

    /**
     * execute index action: load layout, render layout
     *
     */
    public function indexAction()
    {
        $this->loadLayout();
        $this->_setActiveMenu('system/enterprise_staging');
        $this->renderLayout();
    }

    /**
     * Staging edit form
     */
    public function editAction()
    {
        $staging = $this->_initStaging();
        /* @var $staging Enterprise_Staging_Model_Staging */
        if (!$staging) {
            $this->_getSession()->addError($this->__('Incorrect Id'));
            $this->_redirect('*/*/');
            return $this;
        }

        if ($staging->isStatusProcessing()) {
            $this->_getSession()->addNotice($this->__('Merge cannot be done now, because a Merge or Rollback is in progress. Please try again later.'));
        }

        Mage::dispatchEvent('staging_edit_action', array('staging' => $staging));

        $_additionalLayoutPart = '';
        if (!$staging->getId()) {
            $_additionalLayoutPart = '_new';
        }

        if (!$staging->getId()) {
            $entryPoint = Mage::getSingleton('enterprise_staging/entry');
            if ($entryPoint->isAutomatic()) {
                $this->_getSession()->addNotice($this->__('Base URL for this website will be created automatically.'));
                if (!$entryPoint->canEntryPointBeCreated()) {
                    $this->_getSession()->addNotice(Mage::helper('enterprise_staging')->__('To create entry points, the folder %s must be writeable.', $entryPoint->getBaseFolder()));
                }
            }
        }

        $this->loadLayout(array(
            'default',
            strtolower($this->getFullActionName()),
            'staging_'.$staging->getType() . $_additionalLayoutPart
        ));

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
        $event = $this->_initEvent();
        if (!$event->getId()) {
            $this->_getSession()->addError($this->__('Incorrect Id'));
            $this->_redirect('*/*');
            return $this;
        }
        $this->loadLayout();
        $this->_setActiveMenu('system/enterprise_staging');
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
     *
     * @return Enterprise_Staging_Model_Staging
     */
    protected function _initStagingSave()
    {
        $staging = $this->_initStaging();
        if (!$staging) {
            return false;
        }

        $stagingData = $this->getRequest()->getPost('staging');
        if (is_array($stagingData)) {
            $staging->addData($stagingData);
        }
        return $staging;
    }

    /**
     * Save/Create staging action
     */
    public function saveAction()
    {
        $redirectBack   = $this->getRequest()->getParam('back', false);
        $stagingId      = $this->getRequest()->getParam('id');
        $data           = $this->getRequest()->getPost('staging');

        if ($data) {
            $staging    = $this->_initStagingSave();
            if (!$staging) {
                $this->_getSession()->addError($this->__('Incorrect Id'));
                $this->_redirect('*/*/');
                return $this;
            }
            $isNew      = !$staging->getId();
            try {
                $entryPoint = Mage::getSingleton('enterprise_staging/entry');
                if ($entryPoint->isAutomatic()) {
                    if (!$entryPoint->canEntryPointBeCreated()) {
                        throw new Mage_Core_Exception(Mage::helper('enterprise_staging')->__('Please, make sure that folder %s is exists and writeable.', $entryPoint->getBaseFolder()));
                    }
                }

                $staging->save();

                $staging->getMapperInstance()->setCreateMapData($data);
                if ($isNew) {
                    $staging->create();
                } else {
                    $staging->update();
                }
                $this->_getSession()->addSuccess($this->__('Staging website successfully saved.'));
                $stagingId = $staging->getId();
                Mage::dispatchEvent('on_enterprise_staging_save', array('staging' => $staging));
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
                $redirectBack = true;
                if ($isNew) {
                    if ($staging->getStagingWebsite()) {
                        $staging->getStagingWebsite()->delete();
                    }
                    $staging->delete();
                }
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
                $redirectBack = true;
                if ($isNew) {
                    if ($staging->getStagingWebsite()) {
                        $staging->getStagingWebsite()->delete();
                    }
                    $staging->delete();
                }
            }
        }

        if ($redirectBack) {
            $this->_redirect('*/*/edit', array(
                'id'        => $stagingId,
                '_current'  => true
            ));
        } else {
            $this->_redirect('*/*/');
        }
    }

    /**
     * Reset Staging Status
     *
     */
    public function resetStatusAction()
    {
        $staging = $this->_initStaging();
        /* @var $staging Enterprise_Staging_Model_Staging */
        if (!$staging) {
            $this->_getSession()->addError($this->__('Incorrect Id'));
            $this->_redirect('*/*/');
            return $this;
        }

        $staging->setState(Enterprise_Staging_Model_Staging_Config::STATE_NEW);
        $staging->setStatus(Enterprise_Staging_Model_Staging_Config::STATUS_NEW);

        $lastEvent  = $staging->getEventsCollection()->getFirstItem();
        $state      = Enterprise_Staging_Model_Staging_Config::STATUS_COMPLETE;
        $status     = Enterprise_Staging_Model_Staging_Config::STATUS_FAIL;

        $comment    = $this->__('Failed to %s', $lastEvent->getName());
        $staging->addEvent($lastEvent->getCode(), $state, $status, $lastEvent->getName(), $comment);
        $staging->save();

        $catalogIndexFlag = Mage::getModel('catalogindex/catalog_index_flag')->loadSelf();
        if ($catalogIndexFlag->getState() == Mage_CatalogIndex_Model_Catalog_Index_Flag::STATE_RUNNING) {
            $catalogIndexFlag->delete();
        }

        $this->_redirect('*/*/edit', array(
            'id' => $staging->getId()
        ));
    }

    /**
     * Staging merge view action
     *
     */
    public function mergeAction()
    {
        $staging = $this->_initStaging();
        /* @var $staging Enterprise_Staging_Model_Staging */
        if (!$staging) {
            $this->_getSession()->addError($this->__('Incorrect Id'));
            $this->_redirect('*/*/');
            return $this;
        }

        $this->_getSession()
            ->addNotice($this->__('If no store view mapping is specified only website-related information will be merged'));

        $this->loadLayout();

        $this->_setActiveMenu('system/enterprise_staging');

        $this->renderLayout();
    }

    /**
     * Staging process merge
     *
     */
    public function mergePostAction()
    {
        $staging = $this->_initStaging();
        /* @var $staging Enterprise_Staging_Model_Staging */
        if (!$staging) {
            $this->_getSession()->addError($this->__('Incorrect Id'));
            $this->_redirect('*/*/');
            return $this;
        }

        $redirectBack = $this->getRequest()->getParam('back', false);

        $mapData = $this->getRequest()->getPost('map');

        $isMergeLater = $this->getRequest()->getPost('schedule_merge_later_flag');

        $mergeSchedulingDate = $this->getRequest()->getPost('schedule_merge_later');

        $stagingId = $staging->getId();

        if ($mapData) {
            try {
                $staging->getMapperInstance()->setMergeMapData($mapData);

                if (!empty($mapData['backup'])) {
                    $staging->setIsBackuped(1);
                }

                //scheduling merge
                if ($isMergeLater && !empty($mergeSchedulingDate)) {
                    $staging->setIsMergeLater('true');

                    //convert to internal time
                    $date = Mage::getModel('core/date')->gmtDate(null, $mergeSchedulingDate);
                    $staging->setMergeSchedulingDate($date);

                    $originDate = Mage::app()->getHelper('core')->formatDate($date, 'medium', true);
                    $staging->setMergeSchedulingOriginDate($originDate);

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
            $this->_redirect('*/*/merge', array(
                'id'        => $stagingId,
                '_current'  => true
            ));
        } else {
            $this->_redirect('*/*/');
        }
    }

    public function unscheduleAction()
    {
        $event = $this->_initEvent();
        if (!$event) {
            $this->_getSession()->addError($this->__('Incorrect Id'));
            $this->_redirect('*/*/');
            return $this;
        }

        try {
            $event->setData('merge_schedule_date', '0000-00-00 00:00:00');
            $event->save();

            $staging = $event->getStaging();
            $staging->updateAttribute('state', Enterprise_Staging_Model_Staging_Config::STATE_COMPLETE);
            $staging->updateAttribute('status', Enterprise_Staging_Model_Staging_Config::STATUS_COMPLETE);

            $this->_getSession()->addSuccess($this->__('Staging was successfully unscheduled'));
        } catch (Exception $e) {
            $this->_getSession()->addError($this->__('Failed to unschedule merge'));
        }

        $this->_redirect('*/*/edit', array(
            'id'        => $event->getStagingId(),
            '_current'  => true
        ));
    }
}
