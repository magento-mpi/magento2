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
     * @var Saas_ImportExport_Helper_Export
     */
    protected $_exportHelper;

    /**
     * @param Mage_Core_Controller_Request_Http $request
     * @param Mage_Core_Controller_Response_Http $response
     * @param Magento_ObjectManager $objectManager
     * @param Mage_Core_Controller_Varien_Front $frontController
     * @param Mage_Core_Model_Layout_Factory $layoutFactory
     * @param Mage_Core_Model_Authorization $authorizationModel
     * @param Mage_Core_Model_Event_Manager $eventManager
     * @param Saas_ImportExport_Helper_Export $exportHelper
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
        Saas_ImportExport_Helper_Export $exportHelper,
        $areaCode = null,
        array $invokeArgs = array()
    ) {
        parent::__construct($request, $response, $objectManager, $frontController, $layoutFactory, $areaCode,
            $invokeArgs
        );
        $this->_authorizationModel = $authorizationModel;
        $this->_eventManager = $eventManager;
        $this->_exportHelper = $exportHelper;
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
     * Add task to queue for processing export
     *
     * @return Saas_ImportExport_Adminhtml_ExportController
     * @throws Mage_Core_Exception
     */
    public function exportAction()
    {
        if ($this->getRequest()->getPost(Mage_ImportExport_Model_Export::FILTER_ELEMENT_GROUP)) {
            try {
                if ($this->_exportHelper->isTaskAdded()) {
                    throw new Mage_Core_Exception($this->__('Export task has been already added to queue'));
                }
                $this->_exportHelper->setTaskAsQueued();
                $this->_eventManager->dispatch($this->_getEventName(), array('export_params' => $this->getRequest()->getParams()));
                $this->_getSession()->addSuccess($this->__('Export task has been added to queue'));
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
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
        if ($this->_exportHelper->isTaskAdded()) {
            $this->_getSession()->addError($this->__('Another export is in progress'));
            $this->_redirect('*/*/index');
            return;
        }
        if (!$this->_exportHelper->isFileExist()) {
            $this->_getSession()->addError($this->__('Export file does not exist'));
            $this->_redirect('*/*/index');
            return;
        }
        $this->_prepareDownloadResponse($this->_exportHelper->getFileDownloadName(), array(
            'type'  => 'filename',
            'value' => $this->_exportHelper->getFilePath(),
        ), $this->_exportHelper->getFileMimeType());
    }

    /**
     * Remove last export file
     *
     * @return void
     */
    public function removeAction()
    {
        if ($this->_exportHelper->isTaskAdded()) {
            $this->_getSession()->addError($this->__('Another export is in progress'));
            $this->_redirect('*/*/index');
            return;
        }
        try {
            $this->_exportHelper->removeLastExportFile();
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
        $res = array(
            'finished' => false
        );
        if ($this->_exportHelper->isTaskAdded()) {
            if ($this->_exportHelper->isProcessMaxLifetimeReached()) {
                $this->_exportHelper->removeTask();
                $res['finished'] = true;
                $res['html'] = $this->__('An error has occurred during export. Please try again later.');
            } else {
                $res['message'] = $this->_exportHelper->getTaskStatusMessage();
            }
        } else {
            $res['finished'] = true;
            $res['html'] = $this->_getExportInfoHtml();
//          do not show message about finish file exporting
            $this->_exportHelper->setTaskAsNotified();
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
        if ($entity == 'customer_address') {
            $entity = 'customer';
        }
        return 'process_export_' . $entity;
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
