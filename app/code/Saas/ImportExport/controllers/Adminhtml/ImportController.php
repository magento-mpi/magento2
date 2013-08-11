<?php
/**
 * Import Controller
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
require_once 'Mage/ImportExport/controllers/Adminhtml/ImportController.php';

class Saas_ImportExport_Adminhtml_ImportController extends Mage_ImportExport_Adminhtml_ImportController
{
    /**
     * Import state helper
     *
     * @var Saas_ImportExport_Helper_Import_State
     */
    protected $_stateHelper;

    /**
     * @param Mage_Backend_Controller_Context $context
     * @param Saas_ImportExport_Helper_Import_State $stateHelper
     */
    public function __construct(
        Mage_Backend_Controller_Context $context,
        Saas_ImportExport_Helper_Import_State $stateHelper
    ) {
        parent::__construct($context);
        $this->_stateHelper = $stateHelper;
    }

    /**
     * Controller predispatch method.
     *
     * @return Saas_ImportExport_Adminhtml_ImportController
     */
    public function preDispatch()
    {
        parent::preDispatch();
        if ($this->getRequest()->isDispatched() && $this->_stateHelper->isInProgress()
            && $this->getRequest()->getActionName() !== 'busy'
        ) {
            if ($this->getRequest()->isPost()) {
                $this->_getImportFrameBlock()->addError(
                    $this->__('Import process has already queued by another session. Please wait until it finishes.')
                );
                $this->setFlag('', self::FLAG_NO_DISPATCH, true);
                $this->renderLayout();
            } else {
                $this->_forward('busy');
            }
        }
        return $this;
    }

    /**
     * Display busy message
     */
    public function busyAction()
    {
        $this->_initAction()
            ->_title($this->__('System Busy'));
        /** @var Saas_ImportExport_Block_Adminhtml_Import_Busy $block */
        $block = $this->getLayout()->getBlock('busy');
        if (!$block) {
            throw new RuntimeException('Busy block is not found.');
        }
        $block->setStatusMessage($this->__('Import process has already queued. Please wait until it finishes.'));
        $this->renderLayout();
    }

    /**
     * Start import process action
     */
    public function startAction()
    {
        if ($this->getRequest()->isPost()) {
            try {
                $this->_stateHelper->saveTaskAsQueued();
                $this->_eventManager->dispatch($this->_getEventName());
                $this->_getImportFrameBlock()->addSuccess($this->__('Import task has been added to queue.'));
            } catch (Exception $e) {
                $this->_stateHelper->saveTaskAsNotified();
                $this->_getImportFrameBlock()->addError(
                    $this->__('Import task has not been added to queue. Please try again later'));
            }
            $this->renderLayout();
        } else {
            $this->_redirect('*/*/index');
        }
    }

    /**
     * Get import frame block
     *
     * @return Mage_ImportExport_Block_Adminhtml_Import_Frame_Result|bool
     * @throws RuntimeException
     */
    protected function _getImportFrameBlock()
    {
        $this->loadLayout(false);
        $resultBlock = $this->getLayout()->getBlock('import.frame.result');
        if (!$resultBlock) {
            throw new RuntimeException('Result block is not found.');
        }
        $resultBlock->addAction('remove', array('upload_button', 'edit_form'))
            ->addAction('innerHTML', 'import_validation_container_header', $this->__('Status'))
            ->addAction('hide', array('edit_form', 'upload_button', 'messages'));
        return $resultBlock;
    }

    /**
     * Get event name depends on import entity
     *
     * @return string
     * @throws InvalidArgumentException
     */
    protected function _getEventName()
    {
        $entity = $this->getRequest()->getParam('entity');
        if ($entity == 'catalog_product') {
            $taskName = 'import_catalog_product';
        } elseif (in_array($entity, array('customer_composite', 'customer', 'customer_address'))) {
            $taskName = 'import_customer';
        } else {
            throw new InvalidArgumentException('Parameter "entity" is not valid.');
        }
        return 'process_' . $taskName;
    }
}
