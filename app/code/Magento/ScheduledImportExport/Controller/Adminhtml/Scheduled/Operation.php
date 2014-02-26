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

class Operation extends \Magento\Backend\App\Action
{
    /**
     * Core registry
     *
     * @var \Magento\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Registry $coreRegistry
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Registry $coreRegistry
    ) {
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context);
    }

    /**
     * Initialize layout.
     *
     * @return $this
     */
    protected function _initAction()
    {
        try {
            $this->_title->add(__('Scheduled Imports/Exports'));
            $this->_view->loadLayout();
            $this->_setActiveMenu('Magento_ScheduledImportExport::system_convert_magento_scheduled_operation');
        } catch (\Magento\Core\Exception $e) {
            $this->messageManager->addError($e->getMessage());
            $this->_redirect('adminhtml/scheduled_operation/index');
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
        $this->_view->renderLayout();
    }

    /**
     * Create new operation action.
     *
     * @return void
     */
    public function newAction()
    {
        $operationType = $this->getRequest()->getParam('type');
        $this->_initAction();
        $this->_title->add(
            $this->_objectManager->get('Magento\ScheduledImportExport\Helper\Data')
                ->getOperationHeaderText($operationType, 'new')
        );

        $this->_view->renderLayout();
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
        $this->_title->add(
            $helper->getOperationHeaderText($operationType, 'edit')
        );

        $this->_view->renderLayout();
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

            if (isset($data['id']) && !is_numeric($data['id']) || !isset($data['id'])
                && (!isset($data['operation_type']) || empty($data['operation_type']))
                || !is_array($data['start_time'])
            ) {
                $this->messageManager->addError(__("We couldn't save the scheduled operation."));
                $this->_redirect('adminhtml/*/*', array('_current' => true));

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
                /** @var \Magento\ScheduledImportExport\Model\Scheduled\Operation $operation */
                $operation = $this->_objectManager->create(
                    'Magento\ScheduledImportExport\Model\Scheduled\Operation'
                );
                $operation->setData($data);
                $operation->save();
                $this->messageManager->addSuccess(
                    $this->_objectManager->get('Magento\ScheduledImportExport\Helper\Data')
                        ->getSuccessSaveMessage($operation->getOperationType())
                );
            } catch (\Magento\Core\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->_objectManager->get('Magento\Logger')->logException($e);
                $this->messageManager->addError(__("We couldn't save the scheduled operation."));
            }
        }
        $this->_redirect('adminhtml/scheduled_operation/index');
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
                $this->_objectManager->create('Magento\ScheduledImportExport\Model\Scheduled\Operation')
                    ->setId($id)
                    ->delete();
                $this->messageManager->addSuccess(
                    $this->_objectManager->get('Magento\ScheduledImportExport\Helper\Data')->getSuccessDeleteMessage(
                        $request->getParam('type')
                    )
                );
            } catch (\Magento\Core\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->_objectManager->get('Magento\Logger')->logException($e);
                $this->messageManager->addError(__('Something sent wrong deleting the scheduled operation.'));
            }
        }
        $this->_redirect('adminhtml/scheduled_operation/index');
    }

    /**
     * Ajax grid action
     *
     * @return void
     */
    public function gridAction()
    {
        $this->_view->loadLayout(false);
        $this->_view->renderLayout();
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
                $operations = $this->_objectManager->create(
                    'Magento\ScheduledImportExport\Model\Resource\Scheduled\Operation\Collection'
                );
                $operations->addFieldToFilter($operations->getResource()->getIdFieldName(), array('in' => $ids));
                foreach ($operations as $operation) {
                    $operation->delete();
                }
                $this->messageManager->addSuccess(__('We deleted a total of %1 record(s).', count($operations)));
            } catch (\Magento\Core\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->_objectManager->get('Magento\Logger')->logException($e);
                $this->messageManager->addError(__('We cannot delete all items.'));
            }
        }
        $this->_redirect('adminhtml/scheduled_operation/index');
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
                $operations = $this->_objectManager->create(
                    'Magento\ScheduledImportExport\Model\Resource\Scheduled\Operation\Collection'
                );
                $operations->addFieldToFilter($operations->getResource()->getIdFieldName(), array('in' => $ids));

                foreach ($operations as $operation) {
                    $operation->setStatus($status)
                        ->save();
                }
                $this->messageManager->addSuccess(__('A total of %1 record(s) have been updated.', count($operations)));
            } catch (\Magento\Core\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->_objectManager->get('Magento\Logger')->logException($e);
                $this->messageManager->addError(__('We cannot change status for all items.'));
            }
        }
        $this->_redirect('adminhtml/scheduled_operation/index');
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
                $this->_view->loadLayout();

                /** @var $export \Magento\ScheduledImportExport\Model\Export */
                $export = $this->_objectManager->create('Magento\ScheduledImportExport\Model\Export')->setData($data);

                /** @var $attrFilterBlock \Magento\ScheduledImportExport\Block\Adminhtml\Export\Filter */
                $attrFilterBlock = $this->_view->getLayout()->getBlock('export.filter')
                    ->setOperation($export);

                $export->filterAttributeCollection(
                    $attrFilterBlock->prepareCollection(
                        $export->getEntityAttributeCollection()
                    )
                );
                $this->_view->renderLayout();
                return;
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            }
        } else {
            $this->messageManager->addError(__('No valid data sent'));
        }
        $this->_redirect('adminhtml/*/index');
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
            $design = $this->_objectManager->get('Magento\View\DesignInterface');
            $area = $design->getArea();
            $theme = $design->getDesignTheme();
            $design->setDesignTheme(
                $design->getConfigurationDesignTheme(\Magento\Core\Model\App\Area::AREA_FRONTEND)
            );

            $result = $this->_objectManager->get('Magento\ScheduledImportExport\Model\Observer')
                ->processScheduledOperation($schedule, true);

            // restore current design area and theme
            $design->setDesignTheme($theme, $area);
        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
        }

        if ($result) {
            $this->messageManager->addSuccess(__('The operation ran.'));
        } else {
            $this->messageManager->addError(__('Unable to run operation'));
        }

        $this->_redirect('adminhtml/*/index');
    }

    /**
     * Run log cleaning through http request.
     *
     * @return void
     */
    public function logCleanAction()
    {
        $schedule = new \Magento\Object();
        $result = $this->_objectManager->get('Magento\ScheduledImportExport\Model\Observer')
            ->scheduledLogClean($schedule, true);
        if ($result) {
            $this->messageManager->addSuccess(__('We deleted the history files.'));
        } else {
            $this->messageManager->addError(__('Something went wrong deleting the history files.'));
        }
        $this->_redirect('adminhtml/system_config/edit', array('section' => $this->getRequest()->getParam('section')));
    }
}
