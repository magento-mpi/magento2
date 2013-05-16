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
     * Event manager model
     *
     * @var Mage_Core_Model_Event_Manager
     */
    protected $_eventManager;

    /**
     * Import state helper
     *
     * @var Saas_ImportExport_Helper_Import_State
     */
    protected $_stateHelper;

    /**
     * @param Mage_Core_Controller_Request_Http $request
     * @param Mage_Core_Controller_Response_Http $response
     * @param Magento_ObjectManager $objectManager
     * @param Mage_Core_Controller_Varien_Front $frontController
     * @param Mage_Core_Model_Layout_Factory $layoutFactory
     * @param Mage_Core_Model_Event_Manager $eventManager
     * @param Saas_ImportExport_Helper_Import_State $stateHelper
     * @param string $areaCode
     * @param array $invokeArgs
     */
    public function __construct(
        Mage_Core_Controller_Request_Http $request,
        Mage_Core_Controller_Response_Http $response,
        Magento_ObjectManager $objectManager,
        Mage_Core_Controller_Varien_Front $frontController,
        Mage_Core_Model_Layout_Factory $layoutFactory,
        Mage_Core_Model_Event_Manager $eventManager,
        Saas_ImportExport_Helper_Import_State $stateHelper,
        $areaCode = null,
        array $invokeArgs = array()
    ) {
        parent::__construct($request, $response, $objectManager, $frontController, $layoutFactory, $areaCode,
            $invokeArgs);

        $this->_eventManager = $eventManager;
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
        if ($this->getRequest()->isDispatched()
            && $this->_stateHelper->isInProgress()
            && $this->getRequest()->getActionName() !== 'busy' ) {
            if ($this->getRequest()->isPost()) {
                $this->_renderErrorMessage(
                    'Import process has already queued by another session. Please wait until it finishes.');
                $this->setFlag('', self::FLAG_NO_DISPATCH, true);
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
        $block = $this->getLayout()->getBlock('busy');
        if ($block) {
            $block->setStatusMessage($this->__('Import process has already queued. Please wait until it finishes.'));
        }
        $this->renderLayout();
    }

    /**
     * Start import process action
     */
    public function startAction()
    {
        if ($this->getRequest()->isPost()) {
            $this->_stateHelper->setTaskAsQueued();
            $this->_eventManager->dispatch($this->_getEventName());
            $this->_renderSuccessMessage('Import task has been added to queue');
        } else {
            $this->_redirect('*/*/index');
        }
    }

    /**
     * Set message to import frame result block
     *
     * @param string $message
     */
    protected function _renderErrorMessage($message)
    {
        if ($resultBlock = $this->_getImportFrameBlock()) {
            $resultBlock->addError($this->__($message));
            $this->renderLayout();
        }
    }

    /**
     * Set message to import frame result block
     *
     * @param string $message
     */
    protected function _renderSuccessMessage($message)
    {
        if ($resultBlock = $this->_getImportFrameBlock()) {
            $resultBlock->addSuccess($this->__($message));
            $this->renderLayout();
        }
    }

    /**
     * Get import frame block
     *
     * @return Mage_ImportExport_Block_Adminhtml_Import_Frame_Result|bool
     */
    protected function _getImportFrameBlock()
    {
        $this->loadLayout(false);
        $resultBlock = $this->getLayout()->getBlock('import.frame.result');
        if ($resultBlock) {
            $resultBlock->addAction('remove', array('upload_button', 'edit_form'))
                ->addAction('innerHTML', 'import_validation_container_header', $this->__('Status'))
                ->addAction('hide', array('edit_form', 'upload_button', 'messages'));
        }
        return $resultBlock;
    }

    /**
     * Get event name depends on import entity
     *
     * @return string
     */
    protected function _getEventName()
    {
        $entity = $this->getRequest()->getParam('entity');
        if ($entity == 'catalog_product') {
            $taskName = 'import_catalog_product';
        } elseif (in_array($entity, array('customer_composite', 'customer', 'customer_address'))) {
            $taskName = 'import_customer';
        } else {
            $taskName = '';
        }
        return 'process_' . $taskName;
    }
}
