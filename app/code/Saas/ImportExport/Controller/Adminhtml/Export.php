<?php
/**
 * ExportImport Controller
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_ImportExport_Controller_Adminhtml_Export extends Magento_Adminhtml_Controller_Action
{
    /**
     * @var Saas_ImportExport_Helper_Export_State
     */
    protected $_stateHelper;

    /**
     * @var Saas_ImportExport_Helper_Export_File
     */
    protected $_fileHelper;

    /**
     * @var Magento_Core_Helper_Data
     */
    protected $_logger;

    /**
     * @var Magento_Core_Model_Logger
     */
    protected $_coreHelper;

    /**
     * Constructor
     *
     * @param Mage_Backend_Controller_Context $context
     * @param Saas_ImportExport_Helper_Export_State $stateHelper
     * @param Saas_ImportExport_Helper_Export_File $fileHelper
     * @param Magento_Core_Helper_Data $coreHelper
     * @param Magento_Core_Model_Logger $logger
     * @param string|null $areaCode
     */
    public function __construct(
        Mage_Backend_Controller_Context $context,
        Saas_ImportExport_Helper_Export_State $stateHelper,
        Saas_ImportExport_Helper_Export_File $fileHelper,
        Magento_Core_Helper_Data $coreHelper,
        Magento_Core_Model_Logger $logger,
        $areaCode = null
    ) {
        parent::__construct($context, $areaCode);
        $this->_stateHelper = $stateHelper;
        $this->_fileHelper = $fileHelper;
        $this->_coreHelper = $coreHelper;
        $this->_logger = $logger;
    }

    /**
     * Custom constructor
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
        return $this->_authorization->isAllowed('Mage_ImportExport::export');
    }

    /**
     * Redirect on index page if export is in progress
     *
     * @return Saas_ImportExport_Controller_Adminhtml_Export
     */
    public function preDispatch()
    {
        parent::preDispatch();
        if ($this->getRequest()->isDispatched() && $this->_stateHelper->isInProgress()
            && $this->getRequest()->getActionName() !== 'check'
        ) {
            $this->_getSession()->addError($this->__('Another export is in progress.'));
            $this->setFlag('', self::FLAG_NO_DISPATCH, true);
            $this->_redirect('*/*/index');
        }
        return $this;
    }

    /**
     * Add task to queue for processing export
     *
     * @return void
     */
    public function exportAction()
    {
        if ($this->getRequest()->getPost(Mage_ImportExport_Model_Export::FILTER_ELEMENT_GROUP)) {
            try {
                $this->_stateHelper->saveTaskAsQueued();
                $this->_eventManager->dispatch($this->_getEventName(), array(
                    'export_params' => $this->getRequest()->getParams()
                ));
                $this->_getSession()->addSuccess($this->__('Export task has been added to queue.'));
            } catch (Exception $e) {
                $this->_stateHelper->saveTaskAsNotified();
                $this->_logger->logException($e);
                $this->_getSession()->addError($this->__('No valid data sent.'));
            }
        } else {
            $this->_getSession()->addError($this->__('No valid data sent.'));
        }
        $this->_redirect('*/*/index');
    }

    /**
     * Upload last export file
     *
     * @return void
     */
    public function downloadAction()
    {
        if (!$this->_fileHelper->isExist()) {
            $this->_getSession()->addError($this->__('Export file does not exist.'));
            $this->_redirect('*/*/index');
            return;
        }
        try {
            $this->_prepareDownloadResponse($this->_fileHelper->getDownloadName(), array(
                'type' => 'filename',
                'value' => $this->_fileHelper->getPath(),
            ), $this->_fileHelper->getMimeType());
        } catch (Magento_Filesystem_Exception $fe) {
            $this->_fileHelper->removeLastExportFile();
            $this->_getSession()->addError($this->__('Export file does not exist.'));
            $this->_redirect('*/*/index');
        } catch (Exception $e) {
            $this->_logger->logException($e);
            $this->_getSession()->addError($this->__('Cannot download file.'));
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
            $this->_getSession()->addSuccess($this->__('Export file has been removed.'));
        } catch (Magento_Filesystem_Exception $e) {
            $this->_getSession()->addError($this->__('File has not been removed.'));
        }
        $this->_redirect('*/*/index');
    }

    /**
     * Check whether file generating is already finished
     */
    public function checkAction()
    {
        if ($this->_stateHelper->isInProgress()) {
            $result = array('finished' => false, 'message' => $this->_stateHelper->getTaskStatusMessage());
        } else {
            $result = array('finished' => true, 'html' => $this->_getExportInfoHtml());
            // it is need to prevent display "export finished" message
            $this->_stateHelper->saveTaskAsNotified();
        }

        $this->getResponse()->setBody($this->_coreHelper->jsonEncode($result));
    }

    /**
     * Get export file info in html
     *
     * @return string
     */
    protected function _getExportInfoHtml()
    {
        return $this->getLayout()->createBlock('Saas_ImportExport_Block_Adminhtml_Export_Result_Download')->toHtml();
    }

    /**
     * Get event name depends on export entity
     *
     * @return string
     * @throws InvalidArgumentException
     */
    protected function _getEventName()
    {
        $entity = $this->getRequest()->getParam('entity');
        if ($entity == 'catalog_product') {
            $taskName = 'export_catalog_product';
        } elseif (in_array($entity, array('customer', 'customer_address'))) {
            $taskName = 'export_customer';
        } else {
            throw new InvalidArgumentException('Parameter "entity" is not valid.');
        }
        return 'process_' . $taskName;
    }
}
