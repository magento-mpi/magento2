<?php
/**
 * ExportImport Controller
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_ImportExport_Adminhtml_ExportController extends Mage_Adminhtml_Controller_Action
{
    /**
     * @var Mage_Core_Model_Authorization
     */
    protected $_authorizationModel;

    /**
     * Event manager model
     *
     * @var Mage_Core_Model_Event_Manager
     */
    protected $_eventManager;

    /**
     * @var Saas_ImportExport_Helper_Export_State
     */
    protected $_stateHelper;

    /**
     * @var Saas_ImportExport_Helper_Export_File
     */
    protected $_fileHelper;

    /**
     * @param Mage_Core_Controller_Request_Http $request
     * @param Mage_Core_Controller_Response_Http $response
     * @param Magento_ObjectManager $objectManager
     * @param Mage_Core_Controller_Varien_Front $frontController
     * @param Mage_Core_Model_Layout_Factory $layoutFactory
     * @param Mage_Core_Model_Authorization $authorizationModel
     * @param Mage_Core_Model_Event_Manager $eventManager
     * @param Saas_ImportExport_Helper_Export_State $stateHelper
     * @param Saas_ImportExport_Helper_Export_File $fileHelper
     * @param string $areaCode
     * @param array $invokeArgs
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        Mage_Core_Controller_Request_Http $request,
        Mage_Core_Controller_Response_Http $response,
        Magento_ObjectManager $objectManager,
        Mage_Core_Controller_Varien_Front $frontController,
        Mage_Core_Model_Layout_Factory $layoutFactory,
        Mage_Core_Model_Authorization $authorizationModel,
        Mage_Core_Model_Event_Manager $eventManager,
        Saas_ImportExport_Helper_Export_State $stateHelper,
        Saas_ImportExport_Helper_Export_File $fileHelper,
        $areaCode = null,
        array $invokeArgs = array()
    ) {
        parent::__construct($request, $response, $objectManager, $frontController, $layoutFactory, $areaCode,
            $invokeArgs);
        $this->_authorizationModel = $authorizationModel;
        $this->_eventManager = $eventManager;
        $this->_stateHelper = $stateHelper;
        $this->_fileHelper = $fileHelper;
    }

    /**
     * Custom constructor.
     *
     * @return void
     */
    protected function _construct()
    {
        // Define module dependent translate
        $this->setUsedModuleName('Saas_ImportExport');
    }

    /**
     * Check access (in the ACL) for current user
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorizationModel->isAllowed('Mage_ImportExport::export');
    }

    /**
     * Redirect on index page if export is in progress
     *
     * @return Saas_ImportExport_Adminhtml_ImportController
     */
    public function preDispatch()
    {
        parent::preDispatch();
        if ($this->getRequest()->isDispatched()
            && $this->_stateHelper->isInProgress()
            && $this->getRequest()->getActionName() !== 'check') {
            $this->_getSession()->addError($this->__('Another export is in progress'));
            $this->setFlag('', self::FLAG_NO_DISPATCH, true);
            return $this->_redirect('*/*/index');
        }
        return $this;
    }

    /**
     * Add task to queue for processing export
     *
     * @return Saas_ImportExport_Adminhtml_ExportController
     * @throws Mage_Core_Exception
     */
    public function exportAction()
    {
        if ($this->getRequest()->getPost(Mage_ImportExport_Model_Export::FILTER_ELEMENT_GROUP)) {
            try {
                $this->_stateHelper->setTaskAsQueued();
                $this->_eventManager->dispatch($this->_getEventName(), array('export_params' => $this->getRequest()->getParams()));
                $this->_getSession()->addSuccess($this->__('Export task has been added to queue'));
            } catch (Exception $e) {
                Mage::logException($e);
                $this->_getSession()->addError($this->__('No valid data sent'));
            }
        } else {
            $this->_getSession()->addError($this->__('No valid data sent'));
        }
        return $this->_redirect('*/*/index');
    }

    /**
     * Upload last export file
     *
     * @return void
     */
    public function downloadAction()
    {
        if (!$this->_fileHelper->isExist()) {
            $this->_getSession()->addError($this->__('Export file does not exist'));
            $this->_redirect('*/*/index');
            return;
        }
        try {
            $this->_prepareDownloadResponse($this->_fileHelper->getDownloadName(), array(
                'type'  => 'filename',
                'value' => $this->_fileHelper->getPath(),
            ), $this->_fileHelper->getMimeType());
        } catch (Magento_Filesystem_Exception $fe) {
            $this->_getSession()->addError($this->__('Export file does not exist'));
            $this->_fileHelper->removeLastExportFile();
            $this->_redirect('*/*/index');
        } catch (Exception $e) {
            Mage::logException($e);
            $this->_getSession()->addError($this->__('Cannot download file'));
            $this->_redirect('*/*/index');
        }
    }

    /**
     * Remove last export file
     *
     * @return void
     */
    public function removeAction()
    {
        try {
            $this->_fileHelper->removeLastExportFile();
            $this->_getSession()->addSuccess($this->__('Export file has been removed'));
        } catch (Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        }
        $this->_redirect('*/*/index');
    }

    /**
     * Check whether file generating is already finished
     */
    public function checkAction()
    {
        $res = array('finished' => false);
        if ($this->_stateHelper->isInProgress()) {
                $res['message'] = $this->_stateHelper->getTaskStatusMessage();
        } else {
            $res['finished'] = true;
            $res['html'] = $this->_getExportInfoHtml();
//          do not show "export finished" message
            $this->_stateHelper->setTaskAsNotified();
        }
        $this->_endAjax($res);
    }

    /**
     * Get export file info in html
     *
     * @return string
     */
    protected function _getExportInfoHtml()
    {
        $block = $this->getLayout()->createBlock('Saas_ImportExport_Block_Adminhtml_Export_Result_Download');
        return $block ? $block->toHtml() : '';
    }

    /**
     * Get event name depends on export entity
     *
     * @return string
     */
    protected function _getEventName()
    {
        $entity = $this->getRequest()->getParam('entity');
        if ($entity == 'catalog_product') {
            $taskName = 'export_catalog_product';
        } elseif (in_array($entity, array('customer', 'customer_address'))) {
            $taskName = 'export_customer';
        } else {
            $taskName = '';
        }
        return 'process_' . $taskName;
    }

    /**
     * Set body content for ajax request
     *
     * @param array $res
     */
    protected function _endAjax($res)
    {
        $helper = $this->_objectManager->get('Mage_Core_Helper_Data');
        $responseContent = $helper->jsonEncode($res);
        $this->getResponse()->setBody($responseContent);
    }
}
