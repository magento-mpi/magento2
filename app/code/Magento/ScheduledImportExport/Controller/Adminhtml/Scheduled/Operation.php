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
namespace Magento\ScheduledImportExport\Controller\Adminhtml\Scheduled;

class Operation extends \Magento\Adminhtml\Controller\Action
{
    /**
     * Core registry
     *
     * @var \Magento\Core\Model\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\Backend\Controller\Context $context
     * @param \Magento\Core\Model\Registry $coreRegistry
     */
    public function __construct(
        \Magento\Backend\Controller\Context $context,
        \Magento\Core\Model\Registry $coreRegistry
    ) {
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context);
    }

    /**
     * Initialize layout.
     *
     * @return \Magento\ScheduledImportExport\Controller\Adminhtml\Scheduled\Operation
     */
    protected function _initAction()
    {
        try {
            $this->_title(__('Scheduled Imports/Exports'))
                ->loadLayout()
                ->_setActiveMenu('Magento_ScheduledImportExport::system_convert_magento_scheduled_operation');
        } catch (\Magento\Core\Exception $e) {
            \Mage::getSingleton('Magento\Adminhtml\Model\Session')->addError($e->getMessage());
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
        $this->_initAction()->_title(
            $this->_objectManager->get('Magento\ScheduledImportExport\Helper\Data')
                ->getOperationHeaderText($operationType, 'new')
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

        /** @var $operation \Magento\ScheduledImportExport\Model\Scheduled\Operation */
        $operation = $this->_coreRegistry->registry('current_operation');
        $operationType = $operation->getOperationType();

        /** @var $helper \Magento\ScheduledImportExport\Helper\Data */
        $helper = $this->_objectManager->get('Magento\ScheduledImportExport\Helper\Data');
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
                \Mage::getSingleton('Magento\Adminhtml\Model\Session')
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
                $operation = \Mage::getModel('Magento\ScheduledImportExport\Model\Scheduled\Operation')->setData($data);
                $operation->save();
                \Mage::getSingleton('Magento\Adminhtml\Model\Session')->addSuccess(
                    $this->_objectManager->get('Magento\ScheduledImportExport\Helper\Data')
                        ->getSuccessSaveMessage($operation->getOperationType())
                );
            } catch (\Magento\Core\Exception $e) {
                \Mage::getSingleton('Magento\Adminhtml\Model\Session')->addError($e->getMessage());
            } catch (\Exception $e) {
                \Mage::logException($e);
                \Mage::getSingleton('Magento\Adminhtml\Model\Session')->addError(
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
                \Mage::getModel('Magento\ScheduledImportExport\Model\Scheduled\Operation')->setId($id)->delete();
                \Mage::getSingleton('Magento\Adminhtml\Model\Session')->addSuccess(
                    $this->_objectManager->get('Magento\ScheduledImportExport\Helper\Data')->getSuccessDeleteMessage(
                        $request->getParam('type')
                    )
                );
            } catch (\Magento\Core\Exception $e) {
                \Mage::getSingleton('Magento\Adminhtml\Model\Session')->addError($e->getMessage());
            } catch (\Exception $e) {
                \Mage::logException($e);
                \Mage::getSingleton('Magento\Adminhtml\Model\Session')->addError(
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
                $operations = \Mage::getResourceModel(
                    'Magento\ScheduledImportExport\Model\Resource\Scheduled\Operation\Collection'
                );
                $operations->addFieldToFilter($operations->getResource()->getIdFieldName(), array('in' => $ids));
                foreach ($operations as $operation) {
                    $operation->delete();
                }
                \Mage::getSingleton('Magento\Adminhtml\Model\Session')->addSuccess(
                    __('We deleted a total of %1 record(s).', count($operations))
                );
            } catch (\Magento\Core\Exception $e) {
                \Mage::getSingleton('Magento\Adminhtml\Model\Session')->addError($e->getMessage());
            } catch (\Exception $e) {
                \Mage::logException($e);
                \Mage::getSingleton('Magento\Adminhtml\Model\Session')->addError(__('We cannot delete all items.'));
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
                $operations = \Mage::getResourceModel(
                    'Magento\ScheduledImportExport\Model\Resource\Scheduled\Operation\Collection'
                );
                $operations->addFieldToFilter($operations->getResource()->getIdFieldName(), array('in' => $ids));

                foreach ($operations as $operation) {
                    $operation->setStatus($status)
                        ->save();
                }
                \Mage::getSingleton('Magento\Adminhtml\Model\Session')->addSuccess(
                    __('A total of %1 record(s) have been updated.', count($operations))
                );
            } catch (\Magento\Core\Exception $e) {
                \Mage::getSingleton('Magento\Adminhtml\Model\Session')->addError($e->getMessage());
            } catch (\Exception $e) {
                \Mage::logException($e);
                \Mage::getSingleton('Magento\Adminhtml\Model\Session')
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

                /** @var $export \Magento\ScheduledImportExport\Model\Export */
                $export = \Mage::getModel('Magento\ScheduledImportExport\Model\Export')->setData($data);

                /** @var $attrFilterBlock \Magento\ScheduledImportExport\Block\Adminhtml\Export\Filter */
                $attrFilterBlock = $this->getLayout()->getBlock('export.filter')
                    ->setOperation($export);

                $export->filterAttributeCollection(
                    $attrFilterBlock->prepareCollection(
                        $export->getEntityAttributeCollection()
                    )
                );
                $this->renderLayout();
                return;
            } catch (\Exception $e) {
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
            $schedule = new \Magento\Object();
            $schedule->setJobCode(
                \Magento\ScheduledImportExport\Model\Scheduled\Operation::CRON_JOB_NAME_PREFIX . $operationId
            );

            /*
               We need to set default (frontend) area to send email correctly because we run cron task from backend.
               If it wouldn't be done, then in email template resources will be loaded from adminhtml area
               (in which we have only default theme) which is defined in preDispatch()

                Add: After elimination of skins and refactoring of themes we can't just switch area,
                cause we can't be sure that theme set for previous area exists in new one
            */
            $design = $this->_objectManager->get('Magento\Core\Model\View\DesignInterface');
            $area = $design->getArea();
            $theme = $design->getDesignTheme();
            $design->setDesignTheme(
                $design->getConfigurationDesignTheme(\Magento\Core\Model\App\Area::AREA_FRONTEND)
            );

            /** @var $observer \Magento\ScheduledImportExport\Model\Observer */
            $observer = \Mage::getModel('Magento\ScheduledImportExport\Model\Observer');
            $result = $observer->processScheduledOperation($schedule, true);

            // restore current design area and theme
            $design->setDesignTheme($theme, $area);
        } catch (\Exception $e) {
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
        $schedule = new \Magento\Object();
        $result = \Mage::getModel('Magento\ScheduledImportExport\Model\Observer')->scheduledLogClean($schedule, true);
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
