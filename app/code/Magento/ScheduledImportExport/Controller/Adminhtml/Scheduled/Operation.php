<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_ScheduledImportExport
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Operation controller
 *
 * @category    Magento
 * @package     Magento_ScheduledImportExport
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_ScheduledImportExport_Controller_Adminhtml_Scheduled_Operation extends Magento_Adminhtml_Controller_Action
{
    /**
     * Initialize layout.
     *
     * @return Magento_ScheduledImportExport_Controller_Adminhtml_Scheduled_Operation
     */
    protected function _initAction()
    {
        try {
            $this->_title(__('Scheduled Imports/Exports'))
                ->loadLayout()
                ->_setActiveMenu('Magento_ScheduledImportExport::system_convert_magento_scheduled_operation');
        } catch (Magento_Core_Exception $e) {
            Mage::getSingleton('Magento_Adminhtml_Model_Session')->addError($e->getMessage());
            $this->_redirect('*/scheduled_operation/index');
        }

        return $this;
    }

    /**
     * Check access (in the ACL) for current user.
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_ScheduledImportExport::magento_scheduled_operation');
    }

    /**
     * Index action.
     *
     * @return void
     */
    public function indexAction()
    {
        $this->_initAction();
        $this->renderLayout();
    }

    /**
     * Create new operation action.
     *
     * @return void
     */
    public function newAction()
    {
        $operationType = $this->getRequest()->getParam('type');
        $this->_initAction()
            ->_title(
                Mage::helper('Magento_ScheduledImportExport_Helper_Data')->getOperationHeaderText($operationType, 'new')
            );

        $this->renderLayout();
    }

    /**
     * Edit operation action.
     *
     * @return void
     */
    public function editAction()
    {
        $this->_initAction();

        /** @var $operation Magento_ScheduledImportExport_Model_Scheduled_Operation */
        $operation = Mage::registry('current_operation');
        $operationType = $operation->getOperationType();

        /** @var $helper Magento_ScheduledImportExport_Helper_Data */
        $helper = Mage::helper('Magento_ScheduledImportExport_Helper_Data');
        $this->_title(
            $helper->getOperationHeaderText($operationType, 'edit')
        );

        $this->renderLayout();
    }

    /**
     * Save operation action
     *
     * @return void
     */
    public function saveAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $data = $request->getPost();
            if (isset($data['id']) && !is_numeric($data['id'])
                || !isset($data['id']) && (!isset($data['operation_type']) || empty($data['operation_type']))
                || !is_array($data['start_time'])
            ) {
                Mage::getSingleton('Magento_Adminhtml_Model_Session')
                    ->addError(__("We couldn't save the scheduled operation."));
                $this->_redirect('*/*/*', array('_current' => true));

                return;
            }
            $data['start_time'] = join(':', $data['start_time']);
            if (isset($data['export_filter']) && is_array($data['export_filter'])) {
                $data['entity_attributes']['export_filter'] = $data['export_filter'];
                if (isset($data['skip_attr']) && is_array($data['skip_attr'])) {
                    $data['entity_attributes']['skip_attr'] = array_filter($data['skip_attr'], 'intval');
                }
            }

            try {
                $operation = Mage::getModel('Magento_ScheduledImportExport_Model_Scheduled_Operation')->setData($data);
                $operation->save();
                Mage::getSingleton('Magento_Adminhtml_Model_Session')->addSuccess(
                    Mage::helper('Magento_ScheduledImportExport_Helper_Data')
                        ->getSuccessSaveMessage($operation->getOperationType())
                );
            } catch (Magento_Core_Exception $e) {
                Mage::getSingleton('Magento_Adminhtml_Model_Session')->addError($e->getMessage());
            } catch (Exception $e) {
                Mage::logException($e);
                Mage::getSingleton('Magento_Adminhtml_Model_Session')->addError(
                    __("We couldn't save the scheduled operation.")
                );
            }
        }
        $this->_redirect('*/scheduled_operation/index');
    }

    /**
     * Delete operation action
     *
     * @return void
     */
    public function deleteAction()
    {
        $request = $this->getRequest();
        $id = (int)$request->getParam('id');
        if ($id) {
            try {
                Mage::getModel('Magento_ScheduledImportExport_Model_Scheduled_Operation')->setId($id)->delete();
                Mage::getSingleton('Magento_Adminhtml_Model_Session')->addSuccess(
                    Mage::helper('Magento_ScheduledImportExport_Helper_Data')->getSuccessDeleteMessage(
                        $request->getParam('type')
                    )
                );
            } catch (Magento_Core_Exception $e) {
                Mage::getSingleton('Magento_Adminhtml_Model_Session')->addError($e->getMessage());
            } catch (Exception $e) {
                Mage::logException($e);
                Mage::getSingleton('Magento_Adminhtml_Model_Session')->addError(
                    __('Something sent wrong deleting the scheduled operation.')
                );
            }
        }
        $this->_redirect('*/scheduled_operation/index');
    }

    /**
     * Ajax grid action
     *
     * @return void
     */
    public function gridAction()
    {
        $this->loadLayout(false);
        $this->renderLayout();
    }

    /**
     * Batch delete action
     *
     * @return void
     */
    public function massDeleteAction()
    {
        $request = $this->getRequest();
        $ids = $request->getParam('operation');
        if (is_array($ids)) {
            $ids = array_filter($ids, 'intval');
            try {
                $operations = Mage::getResourceModel(
                    'Magento_ScheduledImportExport_Model_Resource_Scheduled_Operation_Collection'
                );
                $operations->addFieldToFilter($operations->getResource()->getIdFieldName(), array('in' => $ids));
                foreach ($operations as $operation) {
                    $operation->delete();
                }
                Mage::getSingleton('Magento_Adminhtml_Model_Session')->addSuccess(
                    __('We deleted a total of %1 record(s).', count($operations))
                );
            } catch (Magento_Core_Exception $e) {
                Mage::getSingleton('Magento_Adminhtml_Model_Session')->addError($e->getMessage());
            } catch (Exception $e) {
                Mage::logException($e);
                Mage::getSingleton('Magento_Adminhtml_Model_Session')->addError(__('We cannot delete all items.'));
            }
        }
        $this->_redirect('*/scheduled_operation/index');
    }

    /**
     * Batch change status action
     *
     * @return void
     */
    public function massChangeStatusAction()
    {
        $request = $this->getRequest();
        $ids = $request->getParam('operation');
        $status = (bool)$request->getParam('status');

        if (is_array($ids)) {
            $ids = array_filter($ids, 'intval');

            try {
                $operations = Mage::getResourceModel(
                    'Magento_ScheduledImportExport_Model_Resource_Scheduled_Operation_Collection'
                );
                $operations->addFieldToFilter($operations->getResource()->getIdFieldName(), array('in' => $ids));

                foreach ($operations as $operation) {
                    $operation->setStatus($status)
                        ->save();
                }
                Mage::getSingleton('Magento_Adminhtml_Model_Session')->addSuccess(
                    __('A total of %1 record(s) have been updated.', count($operations))
                );
            } catch (Magento_Core_Exception $e) {
                Mage::getSingleton('Magento_Adminhtml_Model_Session')->addError($e->getMessage());
            } catch (Exception $e) {
                Mage::logException($e);
                Mage::getSingleton('Magento_Adminhtml_Model_Session')
                    ->addError(__('We cannot change status for all items.'));
            }
        }
        $this->_redirect('*/scheduled_operation/index');
    }

    /**
     * Get grid-filter of entity attributes action.
     *
     * @return void
     */
    public function getFilterAction()
    {
        $data = $this->getRequest()->getParams();
        if ($this->getRequest()->isXmlHttpRequest() && $data) {
            try {
                $this->loadLayout();

                /** @var $export Magento_ScheduledImportExport_Model_Export */
                $export = Mage::getModel('Magento_ScheduledImportExport_Model_Export')->setData($data);

                /** @var $attrFilterBlock Magento_ScheduledImportExport_Block_Adminhtml_Export_Filter */
                $attrFilterBlock = $this->getLayout()->getBlock('export.filter')
                    ->setOperation($export);

                $export->filterAttributeCollection(
                    $attrFilterBlock->prepareCollection(
                        $export->getEntityAttributeCollection()
                    )
                );
                $this->renderLayout();
                return;
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        } else {
            $this->_getSession()->addError(__('No valid data sent'));
        }
        $this->_redirect('*/*/index');
    }

    /**
     * Run task through http request.
     *
     * @return void
     */
    public function cronAction()
    {
        $result = false;
        try {
            $operationId = (int)$this->getRequest()->getParam('operation');
            $schedule = new Magento_Object();
            $schedule->setJobCode(
                Magento_ScheduledImportExport_Model_Scheduled_Operation::CRON_JOB_NAME_PREFIX . $operationId
            );

            /*
               We need to set default (frontend) area to send email correctly because we run cron task from backend.
               If it wouldn't be done, then in email template resources will be loaded from adminhtml area
               (in which we have only default theme) which is defined in preDispatch()

                Add: After elimination of skins and refactoring of themes we can't just switch area,
                cause we can't be sure that theme set for previous area exists in new one
            */
            $design = $this->_objectManager->get('Magento_Core_Model_View_DesignInterface');
            $area = $design->getArea();
            $theme = $design->getDesignTheme();
            $design->setDesignTheme(
                $design->getConfigurationDesignTheme(Magento_Core_Model_App_Area::AREA_FRONTEND)
            );

            /** @var $observer Magento_ScheduledImportExport_Model_Observer */
            $observer = Mage::getModel('Magento_ScheduledImportExport_Model_Observer');
            $result = $observer->processScheduledOperation($schedule, true);

            // restore current design area and theme
            $design->setDesignTheme($theme, $area);
        } catch (Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        }

        if ($result) {
            $this->_getSession()
                ->addSuccess(
                    __('The operation ran.')
                );
        } else {
            $this->_getSession()
                ->addError(
                    __('Unable to run operation')
                );
        }

        $this->_redirect('*/*/index');
    }

    /**
     * Run log cleaning through http request.
     *
     * @return void
     */
    public function logCleanAction()
    {
        $schedule = new Magento_Object();
        $result = Mage::getModel('Magento_ScheduledImportExport_Model_Observer')->scheduledLogClean($schedule, true);
        if ($result) {
            $this->_getSession()
                ->addSuccess(
                    __('We deleted the history files.')
                );
        } else {
            $this->_getSession()
                ->addError(__('Something went wrong deleting the history files.'));
        }
        $this->_redirect('*/system_config/edit', array('section' => $this->getRequest()->getParam('section')));
    }
}
